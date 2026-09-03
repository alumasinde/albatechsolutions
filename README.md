# Google Photos Transfer

A local Python CLI for transferring media from one Google Photos account to another without Partner Sharing.

## Architecture

The source account is exported with Google Takeout. The tool then extracts/scans the Takeout files locally and uploads supported photos/videos to the destination Google account through the Google Photos Library API.

```text
Source Google account
        |
        v
   Google Takeout
        |
        v
 ZIP / extracted files
        |
        v
 Photo Transfer CLI
   |     |      |
   |     |      +--> SQLite transfer state
   |     +---------> SHA-256 duplicate detection
   +---------------> Google Photos upload API
                         |
                         v
                  Destination account
```

## Important API limitation

This project deliberately uses Takeout as the source. Google Photos API access has changed over time; the supported write flow is to upload media bytes and then create media items. The project does not depend on old scripts that try to crawl an entire existing library through unsupported OAuth behavior.

Uploads use the `photoslibrary.appendonly` scope. Google documents a two-step upload (`uploads` then `mediaItems:batchCreate`), with at most 50 media items per batch creation request and serial batch creation per user. See the official Google documentation linked below.

## Requirements

- Python 3.11+
- A Google Cloud project
- Google Photos Library API enabled
- OAuth 2.0 Desktop application credentials
- Google Takeout export containing Google Photos

## Setup

### 1. Create OAuth credentials

In Google Cloud Console:

1. Create/select a project.
2. Enable **Google Photos Library API**.
3. Configure the OAuth consent screen.
4. Create an OAuth client of type **Desktop app**.
5. Download the JSON credentials and save them as `client_secret.json` in the project directory.

Do **not** commit `client_secret.json`, `token.json`, or `transfer.db`.

### 2. Install dependencies

```powershell
python -m venv .venv
.\.venv\Scripts\Activate.ps1
pip install -r requirements.txt
```

### 3. Prepare your Takeout files

Example:

```text
D:\GooglePhotosMigrator\takeout\
    takeout-001.zip
    takeout-002.zip
```

The tool can also accept an already extracted directory.

### 4. Authenticate the destination account

Run:

```powershell
python -m photo_transfer auth
```

A browser window opens. Sign in to **the destination Google account**. The token is saved locally in `token.json`.

### 5. Scan

```powershell
python -m photo_transfer scan --source D:\GooglePhotosMigrator\takeout
```

Scanning is local. JSON sidecar metadata is not uploaded as media.

### 6. Transfer

```powershell
python -m photo_transfer transfer --source D:\GooglePhotosMigrator\takeout
```

The SQLite database records each file by SHA-256 so an interrupted run can be resumed.

## Safety

- Credentials are local and gitignored.
- No source-account password is stored by this application.
- The source account is represented by your downloaded Takeout data; the OAuth flow is performed against the destination account.
- The tool never deletes source files or destination media.
- Failed files remain retryable.

## Official documentation

- Google Photos upload media: https://developers.google.com/photos/library/legacy/guides/upload-media
- Google API OAuth scopes: https://developers.google.com/identity/protocols/oauth2/scopes
- Google Photos API: https://developers.google.com/photos
