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


def ask(label: str, default: str | None = None) -> str:
    suffix = f" [{default}]" if default else ""
    value = input(f"{label}{suffix}: ").strip()
    return value or (default or "")


def build_parser() -> argparse.ArgumentParser:
    parser = argparse.ArgumentParser(prog="photo-transfer", description="Transfer Google Photos Takeout media to another Google account.")
    parser.add_argument("--credentials", type=Path, default=Path("client_secret.json"))
    parser.add_argument("--token", type=Path, default=Path("token.json"))
    parser.add_argument("--database", type=Path, default=Path("transfer.db"))
    sub = parser.add_subparsers(dest="command")
    sub.add_parser("auth", help="Authenticate the destination Google account").set_defaults(func=cmd_auth)
    scan = sub.add_parser("scan", help="Scan Takeout media into the transfer database")
    scan.add_argument("--source", required=True, type=Path)
    scan.add_argument("--extract-to", type=Path, default=Path(".takeout"))
    scan.set_defaults(func=cmd_scan)
    transfer = sub.add_parser("transfer", help="Upload pending media to the destination account")
    transfer.add_argument("--source", required=True, type=Path)
    transfer.add_argument("--extract-to", type=Path, default=Path(".takeout"))
    transfer.add_argument("--batch-size", type=int, default=50)
    transfer.set_defaults(func=cmd_transfer)
    sub.add_parser("status", help="Show transfer counts").set_defaults(func=cmd_status)
    return parser


def cmd_auth(args: argparse.Namespace) -> int:
    authenticate(args.credentials, args.token)
    print(f"Destination credentials saved to {args.token}")
    return 0


def prepare_media(args: argparse.Namespace) -> Path:
    return extract_takeout_collection(args.source, args.extract_to)


def index_media(db: TransferDB, root: Path) -> int:
    files = list(scan_media(root))
    for media in tqdm(files, desc="Indexing media", unit="file"):
        db.upsert_pending(str(media.path.resolve()), media.file_name, sha256_file(media.path), media.size, media.mime_type)
    return len(files)


def cmd_scan(args: argparse.Namespace) -> int:
    db = TransferDB(args.database)
    try:
        db.reset_stale_uploads()
        count = index_media(db, prepare_media(args))
        print(f"Indexed: {count}\nStatus: {db.counts()}")
        return 0
    finally:
        db.close()


def cmd_transfer(args: argparse.Namespace) -> int:
    config = Config(credentials=args.credentials, token=args.token, database=args.database, batch_size=args.batch_size)
    config.validate()
    db = TransferDB(config.database)
    try:
        root = prepare_media(args)
        print(f"Indexed {index_media(db, root)} media files.")
        client = GooglePhotosClient(authenticate(config.credentials, config.token), timeout=config.api_timeout, max_retries=config.max_retries, chunk_size=config.chunk_size)
        rows = list(db.iter_pending())
        if not rows:
            print("Nothing to upload.")
            return 0
        successful = failed = 0
        progress = tqdm(total=len(rows), desc="Transferring", unit="file")
        batch: list[tuple] = []

        def flush() -> None:
            nonlocal successful, failed, batch
            if not batch: return
            results = client.batch_create([{"file_name": r["file_name"], "upload_token": t} for r, t in batch])
            for r, _t in batch[len(results):]:
                db.mark_failed(r["file_hash"], "Google returned fewer batch results than requested"); failed += 1; progress.update(1)
            for (r, _t), result in zip(batch, results):
                status = (result.get("status") or {}).get("code", 0)
                if status == 0 and result.get("mediaItem", {}).get("id"):
                    db.mark_success(r["file_hash"], result["mediaItem"]["id"]); successful += 1
                else:
                    db.mark_failed(r["file_hash"], str((result.get("status") or {}).get("message") or "Unknown media creation failure")); failed += 1
                progress.update(1)
            batch = []

        for row in rows:
            try:
                path = Path(row["file_path"])
                if not path.exists(): raise FileNotFoundError(path)
                db.mark_uploading(row["file_hash"])
                batch.append((row, client.upload_file(path, row["mime_type"])))
                if len(batch) >= config.batch_size: flush()
            except Exception as exc:
                db.mark_failed(row["file_hash"], repr(exc)); failed += 1; progress.update(1)
        flush(); progress.close()
        print(f"Successful: {successful}\nFailed:     {failed}\nDatabase: {config.database}")
        return 0 if failed == 0 else 2
    finally:
        db.close()


def cmd_status(args: argparse.Namespace) -> int:
    db = TransferDB(args.database)
    try:
        for status, count in db.counts().items(): print(f"{status:10} {count}")
        return 0
    finally:
        db.close()


def run_wizard(args: argparse.Namespace) -> int:
    print("\n" + "=" * 52 + "\n           GOOGLE PHOTOS TRANSFER\n" + "=" * 52)
    source_email = ask("Source Google email")
    source = Path(ask("Google Takeout folder"))
    destination_email = ask("Destination Google email")
    print(f"\nSource:      {source_email}\nTakeout:     {source}\nDestination: {destination_email}")
    if ask("Start transfer? (Y/n)", "Y").lower() not in {"y", "yes"}: return 0
    args.source = source
    args.extract_to = Path(".takeout")
    args.batch_size = 50
    if not source.exists(): raise FileNotFoundError(f"Takeout folder not found: {source}")
    print("\nA browser will now open. Sign in ONLY with the destination account:")
    print(destination_email)
    return cmd_transfer(args)


def main() -> int:
    parser = build_parser()
    args = parser.parse_args()
    if args.command is None:
        return run_wizard(args)
    return args.func(args)


if __name__ == "__main__":
    sys.exit(main())
