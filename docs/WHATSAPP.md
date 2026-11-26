# WhatsApp setup and operations

How the WhatsApp Cloud API integration works and what to configure in production.

## How a message flows

1. Meta POSTs to `/webhooks/whatsapp`. The request **must** carry a valid `X-Hub-Signature-256`
   made with `WHATSAPP_APP_SECRET`; anything else gets 403.
2. The raw body is stored in `whatsapp_events` and Meta gets `200` immediately.
3. After the response is sent, the event is processed: delivery statuses are applied (never
   moving backwards), inbound messages are stored once (deduplicated by Meta message id),
   customer files are downloaded to `storage/whatsapp-media/` (outside the web root), the team
   is emailed on the first message of a burst, and the automatic reply flow runs.
4. `bin/whatsapp-worker.php` (cron, every minute) finishes any event that did not complete,
   retries temporary send failures with backoff and applies data retention.

## Automatic replies

- **New chat:** a greeting with three buttons: *Compliance help*, *Website / system*,
  *Talk to a person*. The choice is saved as the contact's topic and shown in the inbox.
- **Talk to a person**, or **any reply you send from the inbox**, pauses automation for that
  chat for 24 hours. Use "Resume automatic replies" in the thread to switch it back on.
- **Typed questions** (instead of tapping a button) get at most one keyword link per hour.
- **Outside business hours** the after-hours note is added to automatic replies.
- **STOP** opts a number out of automatic and template messages. You still get alerted, and
  you can still reply personally while the 24-hour window is open. **START** opts back in.

## Production checklist

1. `.env`: set `WHATSAPP_APP_SECRET`, `WHATSAPP_VERIFY_TOKEN`, `WHATSAPP_PHONE_NUMBER_ID`,
   `WHATSAPP_BUSINESS_ACCOUNT_ID`, `WHATSAPP_ACCESS_TOKEN`. Leave `WHATSAPP_ALLOW_UNSIGNED`
   unset or false.
2. Access token: create a **System User** in Meta Business Settings, give it only the WhatsApp
   account, generate a token with `whatsapp_business_messaging` and
   `whatsapp_business_management`, and rotate it if it is ever exposed.
3. Cron (cPanel → Cron Jobs), every minute:
   `/usr/local/bin/php /home/USER/albatechsolutions/bin/whatsapp-worker.php --quiet`
4. Admin → Site settings → WhatsApp: business hours (e.g. `Mon-Fri 08:00-18:00; Sat 09:00-13:00`),
   after-hours note, follow-up template name, retention days.
5. Delete the old raw log `storage/logs/whatsapp-webhook.log` if it exists. It holds full
   message payloads from before this change and is no longer written.
6. Check Admin → WhatsApp → Health: "Last webhook received" should update when you message
   the business number.

## Templates to create in Meta (category: Utility)

Free text only works within 24 hours of the customer's last message. Outside that window you
need approved templates. Suggested set:

| Name | Use | Example body |
| --- | --- | --- |
| `enquiry_received` | Enquiry form acknowledgment (Site settings → Template name) | Hi {{customer_name}}, thanks for your enquiry about {{service}}. We will reply here shortly. |
| `follow_up` | Inbox "Send follow-up template" when the window is closed | Hi {{customer_name}}, following up on your request with AlbaTech Solutions. Reply here to continue. |
| `quote_ready` | Quote ready | Hi {{customer_name}}, your quote is ready. Reply here and we will send the details. |
| `documents_needed` | Missing documents | Hi {{customer_name}}, we need a few documents to continue your request. Reply here to see the list. |
| `task_complete` | Work done | Hi {{customer_name}}, your request is complete. Reply here if you need anything else. |

The inbox follow-up sends `{{customer_name}}` only, so that template must have exactly that one variable.

## Data retention and deletion

- Raw Meta payloads on messages are cleared after *Keep raw message data* days (default 30).
- Processed webhook events are deleted after at most 7 days.
- Customer files are deleted after *Keep customer files* days (default 30).
- "Delete conversation" in a thread removes messages, files, contact details and consent
  records for that number (use it for data-deletion requests). The opt-out is kept so the
  number is never messaged automatically again.

## Consent

Ticking the WhatsApp box on `/get-help` stores the number, time, reference code (`AT-XXXXXX`,
also in the enquiry email subject) and the exact consent wording in `whatsapp_consents`. The
latest consent is shown in the inbox thread.

## Local testing

With `APP_ENV=local`, set `WHATSAPP_ALLOW_UNSIGNED=true` to post test payloads without a
signature, or set a test `WHATSAPP_APP_SECRET` and sign the body with
`openssl dgst -sha256 -hmac <secret>`. Without API credentials nothing is sent.
