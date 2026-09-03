from pathlib import Path

from photo_transfer.media import scan_media, sha256_file


def test_scan_media_ignores_takeout_json(tmp_path: Path):
    photo = tmp_path / "Google Photos" / "album" / "image.jpg"
    sidecar = tmp_path / "Google Photos" / "album" / "image.jpg.json"
    video = tmp_path / "Google Photos" / "album" / "clip.mp4"
    text = tmp_path / "Google Photos" / "album" / "notes.txt"
    photo.parent.mkdir(parents=True)
    photo.write_bytes(b"photo")
    sidecar.write_text("{}", encoding="utf-8")
    video.write_bytes(b"video")
    text.write_text("ignore", encoding="utf-8")

    found = list(scan_media(tmp_path))
    assert [item.file_name for item in found] == ["clip.mp4", "image.jpg"]
    assert all(item.mime_type for item in found)


def test_sha256_is_stable(tmp_path: Path):
    path = tmp_path / "x.jpg"
    path.write_bytes(b"hello world")
    assert sha256_file(path) == "b94d27b9934d3e08a52e52d7da7dabfac484efe37a5380ee9088f7ace2efcde9"
