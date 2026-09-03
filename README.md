# Google Photos Transfer

A simple local wizard for transferring Google Photos **from a Google Takeout export** into another Google account without Partner Sharing.

## Simple flow

Run:

```powershell
photo-transfer
```

The wizard asks for:

1. Source Google email (for identification)
2. Google Takeout folder containing that account's photos/videos
3. Destination Google email
4. Confirmation

It then opens the **official Google sign-in page in your system browser**. Enter the destination account password and complete 2FA **only on Google's page**. The application never asks for, sees, or stores your Google password.

## Why the source account still uses Takeout

The application cannot safely promise that entering two Google emails will allow it to copy an arbitrary existing Google Photos library directly. Current Google Photos API access does not provide a general-purpose unrestricted read/export API for a newly built app.

The supported practical design is:

```text
Source Google account
       |
       | Google Takeout
       v
Local Takeout folder
       |
       v
Photo Transfer wizard
       |
       | Secure Google OAuth in browser
       v
Destination Google account
```

## First setup

The current source-code version needs one OAuth desktop client configuration for the application itself:

- Create a Google Cloud project.
- Enable the required Google Photos API.
- Create a **Desktop app** OAuth client.
- Download its JSON once as `client_secret.json`.

This is **application configuration**, not a user's Google password. A packaged public release can bundle the application's public desktop OAuth configuration so ordinary users do not perform this setup individually.

## Install

```powershell
git clone https://github.com/alumasinde/photo-transfer.git
cd photo-transfer
python -m venv .venv
.\.venv\Scripts\Activate.ps1
pip install -r requirements.txt
```

## Run the wizard

```powershell
python -m photo_transfer
```

or, after installing the package:

```powershell
photo-transfer
```

The destination authentication browser opens automatically.

## Security

- Never enter your Google password into this CLI.
- Password and 2FA are handled by Google's official browser sign-in.
- OAuth tokens are stored locally and are gitignored.
- Source media is never deleted.
- Destination media is never deleted by this application.
- SQLite tracks progress so interrupted transfers can resume.

## Advanced commands

The existing `auth`, `scan`, `transfer`, and `status` commands remain available for automation and troubleshooting.

## Official references

- Google OAuth for desktop applications
- Google API Console OAuth setup
