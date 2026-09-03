from __future__ import annotations

import time
from pathlib import Path

import requests
from google.auth.transport.requests import AuthorizedSession
from google.oauth2.credentials import Credentials

UPLOAD_URL = "https://photoslibrary.googleapis.com/v1/uploads"
BATCH_CREATE_URL = "https://photoslibrary.googleapis.com/v1/mediaItems:batchCreate"
RESUMABLE_CHUNK_SIZE = 8 * 1024 * 1024


class GooglePhotosError(RuntimeError):
    pass


class GooglePhotosClient:
    def __init__(self, credentials: Credentials, timeout: int = 120, max_retries: int = 5, chunk_size: int = RESUMABLE_CHUNK_SIZE):
        self.session = AuthorizedSession(credentials)
        self.timeout = timeout
        self.max_retries = max_retries
        self.chunk_size = chunk_size

    def upload_file(self, path: Path, mime_type: str) -> str:
        """Upload media bytes and return Google's upload token."""
        size = path.stat().st_size
        if size <= self.chunk_size:
            return self._upload_raw(path, mime_type, size)
        return self._upload_resumable(path, mime_type, size)

    def _upload_raw(self, path: Path, mime_type: str, size: int) -> str:
        headers = {
            "Content-type": "application/octet-stream",
            "X-Goog-Upload-Content-Type": mime_type,
            "X-Goog-Upload-Protocol": "raw",
            "Content-Length": str(size),
        }
        for attempt in range(self.max_retries + 1):
            response = None
            try:
                with path.open("rb") as handle:
                    response = self.session.post(UPLOAD_URL, headers=headers, data=handle, timeout=self.timeout)
                self._raise_for_upload(response)
                return self._require_token(response.text)
            except (requests.RequestException, GooglePhotosError):
                if attempt >= self.max_retries:
                    raise
                self._sleep_before_retry(attempt, response)
        raise AssertionError("unreachable")

    def _upload_resumable(self, path: Path, mime_type: str, size: int) -> str:
        start_headers = {
            "X-Goog-Upload-Protocol": "resumable",
            "X-Goog-Upload-Command": "start",
            "X-Goog-Upload-Header-Content-Type": mime_type,
            "X-Goog-Upload-Header-Content-Length": str(size),
            "Content-Length": "0",
            "Content-Type": "application/octet-stream",
        }
        for attempt in range(self.max_retries + 1):
            response = None
            try:
                response = self.session.post(UPLOAD_URL, headers=start_headers, timeout=self.timeout)
                self._raise_for_upload(response)
                break
            except (requests.RequestException, GooglePhotosError):
                if attempt >= self.max_retries:
                    raise
                self._sleep_before_retry(attempt, response)
        upload_url = response.headers.get("X-Goog-Upload-URL") if response is not None else None
        if not upload_url:
            raise GooglePhotosError("Google did not return a resumable upload URL")

        offset = 0
        with path.open("rb") as handle:
            while offset < size:
                chunk = handle.read(min(self.chunk_size, size - offset))
                if not chunk:
                    raise GooglePhotosError(f"Unexpected EOF while reading {path}")
                end = offset + len(chunk)
                is_final = end >= size
                command = "upload, finalize" if is_final else "upload"
                headers = {
                    "Content-Length": str(len(chunk)),
                    "X-Goog-Upload-Offset": str(offset),
                    "X-Goog-Upload-Command": command,
                }
                for attempt in range(self.max_retries + 1):
                    part_response = None
                    try:
                        part_response = self.session.post(upload_url, headers=headers, data=chunk, timeout=self.timeout)
                        self._raise_for_upload(part_response)
                        if is_final:
                            return self._require_token(part_response.text)
                        break
                    except (requests.RequestException, GooglePhotosError):
                        if attempt >= self.max_retries:
                            raise
                        self._sleep_before_retry(attempt, part_response)
                offset = end
        raise GooglePhotosError("Resumable upload completed without a final upload token")

    def batch_create(self, items: list[dict[str, str]]) -> list[dict]:
        if not 1 <= len(items) <= 50:
            raise ValueError("Google Photos batchCreate accepts 1 to 50 media items")
        payload = {
            "newMediaItems": [
                {"simpleMediaItem": {"fileName": item["file_name"], "uploadToken": item["upload_token"]}}
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
            except (requests.RequestException, GooglePhotosError, ValueError):
                if attempt >= self.max_retries:
                    raise
                self._sleep_before_retry(attempt, response)
        raise AssertionError("unreachable")

    @staticmethod
    def _require_token(value: str) -> str:
        token = value.strip()
        if not token:
            raise GooglePhotosError("Google returned an empty upload token")
        return token

    @staticmethod
    def _raise_for_upload(response: requests.Response) -> None:
        if response.ok:
            return
        if response.status_code == 429:
            raise GooglePhotosError("Google Photos rate limit (429)")
        raise GooglePhotosError(f"Upload failed ({response.status_code}): {response.text[:1000]}")

    @staticmethod
    def _raise_for_json(response: requests.Response) -> None:
        if response.ok:
            return
        if response.status_code == 429:
            raise GooglePhotosError("Google Photos rate limit (429)")
        raise GooglePhotosError(f"API request failed ({response.status_code}): {response.text[:1000]}")

    def _sleep_before_retry(self, attempt: int, response: requests.Response | None) -> None:
        delay = 30 * (2 ** attempt) if response is not None and response.status_code == 429 else min(60, 2 ** attempt)
        time.sleep(delay)
