from __future__ import annotations

from dataclasses import dataclass
from pathlib import Path

DEFAULT_SCOPE = "https://www.googleapis.com/auth/photoslibrary.appendonly"


@dataclass(frozen=True)
class Config:
    credentials: Path = Path("client_secret.json")
    token: Path = Path("token.json")
    database: Path = Path("transfer.db")
    batch_size: int = 50
    workers: int = 3
    max_retries: int = 5
    chunk_size: int = 8 * 1024 * 1024
    api_timeout: int = 120

    def validate(self) -> None:
        if not 1 <= self.batch_size <= 50:
            raise ValueError("batch_size must be between 1 and 50")
        if not 1 <= self.workers <= 16:
            raise ValueError("workers must be between 1 and 16")
        if self.max_retries < 0:
            raise ValueError("max_retries cannot be negative")
        if self.chunk_size <= 0:
            raise ValueError("chunk_size must be positive")
