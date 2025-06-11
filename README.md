# Pharma Inventory Management

This project provides a basic PHP-based inventory management system for pharmaceutical manufacturers.

## Features

- Add, edit, and delete products
 gsamsq-codex/create-inventory-management-software-for-pharma-manufacturer
- Track product quantity, price, expiration date, composition, packing, and category
- Simple web interface using PHP and MySQL
- REST API for mobile and other integrations



## Getting Started

1. Create a MySQL database (e.g., `inventory`).
2. Import `db.sql` to create the required tables.
 gsamsq-codex/create-inventory-management-software-for-pharma-manufacturer
3. (Optional) Import `sample_data.sql` to populate products with example entries.
4. Update the database settings in `config.php`.
5. Upload all files to your Hostinger PHP hosting account.
6. Visit `index.php` in your browser to begin managing inventory.
7. (Optional) Use `mobile.html` for a simple mobile-friendly interface.

## API

All product operations are also exposed through a REST API located at `api/products.php`.

- `GET /api/products.php` - list products
- `GET /api/products.php?id=ID` - retrieve a single product
- `POST /api/products.php` - add a product (JSON body)
- `PUT /api/products.php?id=ID` - update a product (JSON body)
- `DELETE /api/products.php?id=ID` - remove a product

Responses are JSON encoded.


## Files

- `config.php` - Database connection setup
- `db.sql` - Database schema
- `index.php` - Main menu
- `inventory.php` - List of products
- `add_product.php` - Add new products
- `edit_product.php` - Update existing products
- `delete_product.php` - Delete a product
- `style.css` - Basic styling
 gsamsq-codex/create-inventory-management-software-for-pharma-manufacturer
- `sample_data.sql` - Example products for testing
- `api/products.php` - REST endpoints
- `mobile.html`/`mobile.js` - Simple mobile client


This is a minimal example and can be extended to meet specific business needs.
