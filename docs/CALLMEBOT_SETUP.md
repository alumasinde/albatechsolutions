# Temporary CallMeBot Setup

CallMeBot support is for temporary **personal internal admin alerts only**. Do not use it as AlbaTech's permanent customer messaging provider.

## Configure

1. Activate CallMeBot for the WhatsApp number that will receive alerts and obtain the personal API key.
2. Set:

    WHATSAPP_ENABLED=true
    WHATSAPP_PROVIDER=callmebot
    CALLMEBOT_API_KEY=your_personal_key
    ADMIN_NOTIFICATION_WHATSAPP=254792159806

3. Restart/reload the application configuration.
4. Submit a test assistance request.

The AlbaTech notification pipeline remains unchanged. Requests are saved first, then notifications are attempted and logged.

## Switch to Meta later

Change:

    WHATSAPP_PROVIDER=meta

and configure:

    WHATSAPP_ACCESS_TOKEN=
    WHATSAPP_PHONE_NUMBER_ID=
    WHATSAPP_GRAPH_VERSION=v23.0

No assistance-request code changes are required.

## Security

- Keep CALLMEBOT_API_KEY out of Git.
- Use CallMeBot only for your own activated alert number.
- Do not rely on it for customer delivery, sensitive documents, or permanent business messaging.
