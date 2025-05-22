-- Add the payment_method_id column
ALTER TABLE parcels
ADD COLUMN payment_method_id INT(10) UNSIGNED DEFAULT NULL AFTER price;

-- Set a default payment_method_id for existing rows
UPDATE parcels
SET payment_method_id = 1 -- Default to 'PayPal' (or any valid ID from the payment_method table)
WHERE payment_method_id IS NULL;

-- Modify the column to NOT NULL after updating existing rows
ALTER TABLE parcels
MODIFY COLUMN payment_method_id INT(10) UNSIGNED NOT NULL;

-- Add the foreign key constraint
ALTER TABLE parcels
ADD CONSTRAINT fk_payment_method FOREIGN KEY (payment_method_id) REFERENCES payment_method(id) ON DELETE CASCADE;
