-- Phase 2: tracked internal notifications for new assistance requests.
-- WhatsApp and email are both stored as normal notification records so retries,
-- attempts and failures are visible in the admin system.

INSERT INTO assistance_notification_templates (event, channel, template_name, language, subject_template, body_template) VALUES
('admin_new_request','email',NULL,NULL,'New AlbaTech request — {{reference}}','New AlbaTech assistance request\n\nReference: {{reference}}\nService: {{service}}\nName: {{name}}\nPhone: {{phone}}\nEmail: {{email}}\nCategory: {{category}}\nPreferred task: {{message}}\n\nOpen request: {{url}}'),
('admin_new_request','whatsapp',NULL,'en_US',NULL,'🔔 New AlbaTech request\n\nReference: {{reference}}\nService: {{service}}\nName: {{name}}\nPhone: {{phone}}\n\n{{message}}\n\nOpen: {{url}}')
ON DUPLICATE KEY UPDATE subject_template=VALUES(subject_template), body_template=VALUES(body_template), language=VALUES(language), enabled=1;
