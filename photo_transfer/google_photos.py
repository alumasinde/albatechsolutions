from __future__ import annotations

import time
from pathlib import Path

import requests
from google.auth.transport.requests import AuthorizedSession
from google.oauth2.credentials import Credentials

UPLOAD_URL = "https://photoslibrary.googleapis.com/v1/uploads"
BATCH_CREATE_URL = "https://photoslibrary.googleapis.com/v1/mediaItems:batchCreate"


class GooglePhotosError(RuntimeError):
    pass


class GooglePhotosClient:
    def __init__(self, credentials: Credentials, timeout: int = 120, max_retries: int = 5, chunk_size: int = 8 * 1024 * 1024):
        self.session = AuthorizedSession(credentials)
        self.timeout = timeout
        self.max_retries = max_retries
        self.chunk_size = chunk_size

    def upload_file(self, path: Path, mime_type: str) -> str:
        """Upload raw bytes and return Google's upload token."""
        size = path.stat().st_size
        headers = {
            "Content-type": "application/octet-stream",
            "X-Goog-Upload-Content-Type": mime_type,
            "X-Goog-Upload-Protocol": "raw",
            "Content-Length": str(size),
        }

        # The raw endpoint is intentionally used here. The database makes the
        # transfer resumable at the file level; large-file chunked upload can
        # be added without changing the persistent state model.
        for attempt in range(self.max_retries + 1):
            try:
                with path.open("rb") as handle:
                    response = self.session.post(UPLOAD_URL, headers=headers, data=handle, timeout=self.timeout)
                self._raise_for_upload(response)
                token = response.text.strip()
                if not token:
                    raise GooglePhotosError("Google returned an empty upload token")
                return token
            except (requests.RequestException, GooglePhotosError) as exc:
                if attempt >= self.max_retries:
                    raise
                self._sleep_before_retry(attempt, response if 'response' in locals() else None, exc)

        raise AssertionError("unreachable")

    def batch_create(self, items: list[dict[str, str]]) -> list[dict]:
        if not 1 <= len(items) <= 50:
            raise ValueError("Google Photos batchCreate accepts 1 to 50 media items")
        payload = {
            "newMediaItems": [
                {
                    "simpleMediaItem": {
                        "fileName": item["file_name"],
                        "uploadToken": item["upload_token"],
                    }
                }
                for item in items
            ]
        }
        for attempt in range(self.max_retries + 1):
            response = None
            try:
                response = self.session.post(BATCH_CREATE_URL, json=payload, timeout=self.timeout)
                self._raise_for_json(response)
                data = response.json()
                return data.get("newMediaItemResults", [])
            except (requests.RequestException, GooglePhotosError, ValueError) as exc:
                if attempt >= self.max_retries:
                    raise
                self._sleep_before_retry(attempt, response, exc)
        raise AssertionError("unreachable")

    def _raise_for_upload(self, response: requests.Response) -> None:
        if response.ok:
            return
        if response.status_code == 429:
            raise GooglePhotosError("Google Photos rate limit (429)")
        raise GooglePhotosError(f"Upload failed ({response.status_code}): {response.text[:1000]}")

    def _raise_for_json(self, response: requests.Response) -> None:
        if response.ok:
            return
        if response.status_code == 429:
            raise GooglePhotosError("Google Photos rate limit (429)")
        raise GooglePhotosError(f"API request failed ({response.status_code}): {response.text[:1000]}")

    def _sleep_before_retry(self, attempt: int, response: requests.Response | None, exc: Exception) -> None:
        # Google documents a minimum 30 second delay for 429s. Use exponential
        # backoff for other transient failures as well, capped for usability.
        delay = 30 * (2 ** attempt) if response is not None and response.status_code == 429 else min(60, 2 ** attempt)
        time.sleep(delay)
