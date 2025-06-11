# Pharma Inventory Management

This project provides a basic PHP-based inventory management system for pharmaceutical manufacturers.

## Features

- Add, edit, and delete products

- Track product quantity, price, expiration date, composition, packing, and category

- Simple web interface using PHP and MySQL

## Getting Started

1. Create a MySQL database (e.g., `inventory`).
2. Import `db.sql` to create the required tables.

3. (Optional) Import `sample_data.sql` to populate products with example entries.
4. Set the following environment variables for database access before running the app:
   - `DB_HOST` - Database host
   - `DB_NAME` - Database name
   - `DB_USER` - Database username
   - `DB_PASS` - Database password
5. Upload all files to your Hostinger PHP hosting account.
6. Visit `index.php` in your browser to begin managing inventory.



## Files

- `config.php` - Database connection setup
- `db.sql` - Database schema
- `index.php` - Main menu
- `inventory.php` - List of products
- `add_product.php` - Add new products
- `edit_product.php` - Update existing products
- `delete_product.php` - Delete a product
- `style.css` - Basic styling

- `sample_data.sql` - Example products for testing


This is a minimal example and can be extended to meet specific business needs.
