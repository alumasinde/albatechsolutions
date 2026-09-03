from __future__ import annotations

import json
from pathlib import Path

from google.auth.transport.requests import Request
from google.oauth2.credentials import Credentials
from google_auth_oauthlib.flow import InstalledAppFlow

from .config import DEFAULT_SCOPE


def authenticate(credentials_path: Path, token_path: Path, expected_email: str | None = None) -> Credentials:
    """Open the user's system browser and authenticate the Google account securely."""
    if not credentials_path.exists():
        raise FileNotFoundError(
            "This app needs its OAuth client configuration. "
            "A packaged release can ship the desktop client configuration, but "
            "a source checkout needs client_secret.json until a project client ID is configured."
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
        creds = flow.run_local_server(
            host="127.0.0.1",
            port=0,
            access_type="offline",
            prompt="consent",
            open_browser=True,
        )

    token_path.parent.mkdir(parents=True, exist_ok=True)
    token_path.write_text(creds.to_json(), encoding="utf-8")
    return creds
