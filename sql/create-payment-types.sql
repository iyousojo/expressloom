CREATE TABLE payment_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255) DEFAULT NULL
);

-- Insert default payment methods
INSERT INTO payment_types (name, description) VALUES
('credit_card', 'Pay using a credit card'),
('paypal', 'Pay using PayPal'),
('cash_on_delivery', 'Pay in cash upon delivery');
