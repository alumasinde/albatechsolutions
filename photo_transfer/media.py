from __future__ import annotations

import hashlib
import mimetypes
import os
import zipfile
from dataclasses import dataclass
from pathlib import Path
from typing import Iterator

IMAGE_EXTENSIONS = {".avif", ".bmp", ".gif", ".heic", ".heif", ".ico", ".jpg", ".jpeg", ".png", ".tif", ".tiff", ".webp"}
VIDEO_EXTENSIONS = {".3gp", ".3g2", ".asf", ".avi", ".divx", ".m2t", ".m2ts", ".m4v", ".mkv", ".mmv", ".mod", ".mov", ".mp4", ".mpg", ".mpeg", ".mts", ".tod", ".wmv"}
SUPPORTED_EXTENSIONS = IMAGE_EXTENSIONS | VIDEO_EXTENSIONS


@dataclass(frozen=True)
class MediaFile:
    path: Path
    relative_path: str
    file_name: str
    mime_type: str
    size: int

    @property
    def is_video(self) -> bool:
        return self.path.suffix.lower() in VIDEO_EXTENSIONS


def extract_takeout(source: Path, destination: Path) -> Path:
    """Extract one or more Takeout ZIPs, or validate an already extracted directory."""
    if not source.exists():
        raise FileNotFoundError(source)
    destination.mkdir(parents=True, exist_ok=True)

    if source.is_dir():
        return source

    if source.suffix.lower() != ".zip":
        raise ValueError(f"Source must be a directory or .zip file: {source}")

    with zipfile.ZipFile(source) as archive:
        archive.extractall(destination)
    return destination


def extract_takeout_collection(source: Path, destination: Path) -> Path:
    """Extract all ZIP files in a directory, keeping an existing extracted tree intact."""
    if source.is_file():
        return extract_takeout(source, destination)
    if not source.is_dir():
        raise FileNotFoundError(source)

    destination.mkdir(parents=True, exist_ok=True)
    zips = sorted(source.glob("*.zip"))
    if not zips:
        return source
    for archive in zips:
        extract_takeout(archive, destination)
    return destination


def scan_media(root: Path) -> Iterator[MediaFile]:
    """Yield supported media files. Takeout JSON sidecars are intentionally ignored."""
    if not root.exists():
        raise FileNotFoundError(root)
    if not root.is_dir():
        raise ValueError(f"Media root is not a directory: {root}")

    for path in sorted(root.rglob("*"), key=lambda p: str(p).lower()):
        if not path.is_file() or path.suffix.lower() not in SUPPORTED_EXTENSIONS:
            continue
        mime_type = mimetypes.guess_type(path.name)[0] or "application/octet-stream"
        yield MediaFile(
            path=path,
            relative_path=os.path.relpath(path, root),
            file_name=path.name,
            mime_type=mime_type,
            size=path.stat().st_size,
        )


def sha256_file(path: Path, chunk_size: int = 1024 * 1024) -> str:
    digest = hashlib.sha256()
    with path.open("rb") as handle:
        while chunk := handle.read(chunk_size):
            digest.update(chunk)
    return digest.hexdigest()
