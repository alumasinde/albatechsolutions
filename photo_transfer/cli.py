from __future__ import annotations

import argparse
import sys
from pathlib import Path

from tqdm import tqdm

from .auth import authenticate
from .config import Config
from .database import TransferDB
from .google_photos import GooglePhotosClient
from .media import extract_takeout_collection, scan_media, sha256_file


def build_parser() -> argparse.ArgumentParser:
    parser = argparse.ArgumentParser(prog="photo-transfer", description="Transfer Google Photos Takeout media to another Google account.")
    parser.add_argument("--credentials", type=Path, default=Path("client_secret.json"))
    parser.add_argument("--token", type=Path, default=Path("token.json"))
    parser.add_argument("--database", type=Path, default=Path("transfer.db"))

    sub = parser.add_subparsers(dest="command", required=True)
    auth = sub.add_parser("auth", help="Authenticate the destination Google account")
    auth.set_defaults(func=cmd_auth)

    scan = sub.add_parser("scan", help="Scan Takeout media into the transfer database")
    scan.add_argument("--source", required=True, type=Path)
    scan.add_argument("--extract-to", type=Path, default=Path(".takeout"))
    scan.set_defaults(func=cmd_scan)

    transfer = sub.add_parser("transfer", help="Upload pending media to the destination account")
    transfer.add_argument("--source", required=True, type=Path)
    transfer.add_argument("--extract-to", type=Path, default=Path(".takeout"))
    transfer.add_argument("--batch-size", type=int, default=50)
    transfer.set_defaults(func=cmd_transfer)

    status = sub.add_parser("status", help="Show transfer counts")
    status.set_defaults(func=cmd_status)
    return parser


def cmd_auth(args: argparse.Namespace) -> int:
    authenticate(args.credentials, args.token)
    print(f"Destination credentials saved to {args.token}")
    return 0


def prepare_media(args: argparse.Namespace) -> Path:
    return extract_takeout_collection(args.source, args.extract_to)


def index_media(db: TransferDB, root: Path) -> int:
    count = 0
    files = list(scan_media(root))
    for media in tqdm(files, desc="Indexing media", unit="file"):
        file_hash = sha256_file(media.path)
        db.upsert_pending(
            file_path=str(media.path.resolve()),
            file_name=media.file_name,
            file_hash=file_hash,
            file_size=media.size,
            mime_type=media.mime_type,
        )
        count += 1
    return count


def cmd_scan(args: argparse.Namespace) -> int:
    db = TransferDB(args.database)
    try:
        db.reset_stale_uploads()
        root = prepare_media(args)
        count = index_media(db, root)
        counts = db.counts()
        print(f"Indexed: {count}")
        print("Status:", counts)
        return 0
    finally:
        db.close()


def cmd_transfer(args: argparse.Namespace) -> int:
    config = Config(credentials=args.credentials, token=args.token, database=args.database, batch_size=args.batch_size)
    config.validate()
    db = TransferDB(config.database)
    try:
        root = prepare_media(args)
        indexed = index_media(db, root)
        print(f"Indexed {indexed} media files.")
        creds = authenticate(config.credentials, config.token)
        client = GooglePhotosClient(creds, timeout=config.api_timeout, max_retries=config.max_retries, chunk_size=config.chunk_size)

        rows = list(db.iter_pending())
        if not rows:
            print("Nothing to upload.")
            return 0

        successful = 0
        failed = 0
        progress = tqdm(total=len(rows), desc="Transferring", unit="file")
        pending_batch: list[tuple] = []

        def flush_batch() -> None:
            nonlocal successful, failed, pending_batch
            if not pending_batch:
                return
            items = [{"file_name": row["file_name"], "upload_token": token, "file_hash": row["file_hash"]} for row, token in pending_batch]
            results = client.batch_create([{"file_name": item["file_name"], "upload_token": item["upload_token"]} for item in items])
            # Google returns one result per request entry. Mark individual successes.
            for (row, _token), result in zip(pending_batch, results):
                status = (result.get("status") or {}).get("code", 0)
                if status == 0 and result.get("mediaItem", {}).get("id"):
                    db.mark_success(row["file_hash"], result["mediaItem"]["id"])
                    successful += 1
                else:
                    message = str((result.get("status") or {}).get("message") or "Unknown media creation failure")
                    db.mark_failed(row["file_hash"], message)
                    failed += 1
                progress.update(1)
            # A defensive failure signal if an API response is unexpectedly short.
            if len(results) < len(pending_batch):
                for row, _token in pending_batch[len(results):]:
                    db.mark_failed(row["file_hash"], "Google returned fewer batch results than requested")
                    failed += 1
                    progress.update(1)
            pending_batch = []

        for row in rows:
            path = Path(row["file_path"])
            if not path.exists():
                db.mark_failed(row["file_hash"], f"File no longer exists: {path}")
                failed += 1
                progress.update(1)
                continue
            try:
                db.mark_uploading(row["file_hash"])
                token = client.upload_file(path, row["mime_type"])
                pending_batch.append((row, token))
                if len(pending_batch) >= config.batch_size:
                    flush_batch()
            except Exception as exc:  # keep the transfer running across individual file failures
                db.mark_failed(row["file_hash"], repr(exc))
                failed += 1
                progress.update(1)
        flush_batch()
        progress.close()

        print(f"Successful: {successful}")
        print(f"Failed:     {failed}")
        print("Database:", config.database)
        return 0 if failed == 0 else 2
    finally:
        db.close()


def cmd_status(args: argparse.Namespace) -> int:
    db = TransferDB(args.database)
    try:
        for status, count in db.counts().items():
            print(f"{status:10} {count}")
        return 0
    finally:
        db.close()


def main() -> int:
    parser = build_parser()
    args = parser.parse_args()
    return args.func(args)


if __name__ == "__main__":
    sys.exit(main())
