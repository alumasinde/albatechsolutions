from __future__ import annotations

import sqlite3
from datetime import datetime, timezone
from pathlib import Path


class TransferDB:
    def __init__(self, path: Path):
        self.path = path
        self.path.parent.mkdir(parents=True, exist_ok=True)
        self.conn = sqlite3.connect(str(path))
        self.conn.row_factory = sqlite3.Row
        self.conn.execute("PRAGMA journal_mode=WAL")
        self.conn.execute("PRAGMA foreign_keys=ON")
        self._init_schema()

    def _init_schema(self) -> None:
        self.conn.executescript(
            """
            CREATE TABLE IF NOT EXISTS transfers (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                file_path TEXT NOT NULL,
                file_name TEXT NOT NULL,
                file_hash TEXT NOT NULL UNIQUE,
                file_size INTEGER NOT NULL,
                mime_type TEXT NOT NULL,
                status TEXT NOT NULL DEFAULT 'pending'
                    CHECK(status IN ('pending','uploading','success','failed')),
                upload_token TEXT,
                media_item_id TEXT,
                error_message TEXT,
                retry_count INTEGER NOT NULL DEFAULT 0,
                created_at TEXT NOT NULL,
                updated_at TEXT NOT NULL
            );
            CREATE INDEX IF NOT EXISTS idx_transfers_status ON transfers(status);
            CREATE INDEX IF NOT EXISTS idx_transfers_path ON transfers(file_path);
            """
        )
        self.conn.commit()

    def close(self) -> None:
        self.conn.close()

    def upsert_pending(self, *, file_path: str, file_name: str, file_hash: str, file_size: int, mime_type: str) -> None:
        now = datetime.now(timezone.utc).isoformat()
        self.conn.execute(
            """
            INSERT INTO transfers (file_path,file_name,file_hash,file_size,mime_type,status,created_at,updated_at)
            VALUES (?,?,?,?,?,'pending',?,?)
            ON CONFLICT(file_hash) DO UPDATE SET
                file_path=excluded.file_path,
                file_name=excluded.file_name,
                file_size=excluded.file_size,
                mime_type=excluded.mime_type,
                updated_at=excluded.updated_at
            """,
            (file_path, file_name, file_hash, file_size, mime_type, now, now),
        )
        self.conn.commit()

    def get_by_hash(self, file_hash: str):
        return self.conn.execute("SELECT * FROM transfers WHERE file_hash=?", (file_hash,)).fetchone()

    def mark_uploading(self, file_hash: str) -> None:
        self._update(file_hash, status="uploading", error_message=None)

    def mark_success(self, file_hash: str, media_item_id: str | None = None) -> None:
        self._update(file_hash, status="success", media_item_id=media_item_id, error_message=None)

    def mark_failed(self, file_hash: str, error_message: str) -> None:
        self.conn.execute(
            "UPDATE transfers SET status='failed', error_message=?, retry_count=retry_count+1, updated_at=? WHERE file_hash=?",
            (error_message[:4000], datetime.now(timezone.utc).isoformat(), file_hash),
        )
        self.conn.commit()

    def reset_stale_uploads(self) -> int:
        cur = self.conn.execute("UPDATE transfers SET status='pending', updated_at=? WHERE status='uploading'", (datetime.now(timezone.utc).isoformat(),))
        self.conn.commit()
        return cur.rowcount

    def counts(self) -> dict[str, int]:
        rows = self.conn.execute("SELECT status, COUNT(*) AS n FROM transfers GROUP BY status").fetchall()
        result = {"pending": 0, "uploading": 0, "success": 0, "failed": 0}
        result.update({row["status"]: row["n"] for row in rows})
        return result

    def iter_pending(self):
        return self.conn.execute("SELECT * FROM transfers WHERE status IN ('pending','failed') ORDER BY id")

    def _update(self, file_hash: str, **values) -> None:
        values["updated_at"] = datetime.now(timezone.utc).isoformat()
        columns = ", ".join(f"{key}=?" for key in values)
        params = list(values.values()) + [file_hash]
        self.conn.execute(f"UPDATE transfers SET {columns} WHERE file_hash=?", params)
        self.conn.commit()
