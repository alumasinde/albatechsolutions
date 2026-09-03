from __future__ import annotations

import json
from pathlib import Path

from google.auth.transport.requests import Request
from google.oauth2.credentials import Credentials
from google_auth_oauthlib.flow import InstalledAppFlow

from .config import DEFAULT_SCOPE


def authenticate(credentials_path: Path, token_path: Path) -> Credentials:
    """Authenticate the destination Google account for append-only Photos access."""
    if not credentials_path.exists():
        raise FileNotFoundError(
            f"OAuth client credentials not found: {credentials_path}. "
            "Download a Desktop app client_secret.json from Google Cloud Console."
        )

    creds: Credentials | None = None
    if token_path.exists():
        try:
            creds = Credentials.from_authorized_user_file(str(token_path), [DEFAULT_SCOPE])
        except (ValueError, json.JSONDecodeError) as exc:
            raise RuntimeError(f"Invalid OAuth token file: {token_path}") from exc

    if creds and creds.valid:
        return creds

    if creds and creds.expired and creds.refresh_token:
        creds.refresh(Request())
    else:
        flow = InstalledAppFlow.from_client_secrets_file(str(credentials_path), [DEFAULT_SCOPE])
        creds = flow.run_local_server(port=0, access_type="offline", prompt="consent")

    token_path.parent.mkdir(parents=True, exist_ok=True)
    token_path.write_text(creds.to_json(), encoding="utf-8")
    return creds
