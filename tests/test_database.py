from pathlib import Path

from photo_transfer.database import TransferDB


def test_transfer_database_tracks_state(tmp_path: Path):
    db = TransferDB(tmp_path / "transfer.db")
    try:
        db.upsert_pending(
            file_path="/tmp/a.jpg",
            file_name="a.jpg",
            file_hash="abc",
            file_size=3,
            mime_type="image/jpeg",
        )
        assert db.counts()["pending"] == 1
        db.mark_uploading("abc")
        assert db.counts()["uploading"] == 1
        db.mark_success("abc", "media-1")
        assert db.counts()["success"] == 1
        assert db.get_by_hash("abc")["media_item_id"] == "media-1"
    finally:
        db.close()
