UPDATE services
SET pricing_label = 'Contact for price'
WHERE pricing_type = 'quote'
  AND pricing_label IN ('Request a quote', 'Ask about pricing');
