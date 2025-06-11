# Pharma Inventory Management

This project provides a lightweight inventory management system for pharmaceutical manufacturers built with **Flask** and **SQLite**. It exposes a REST API and includes a Progressive Web App (PWA) interface for mobile devices.

## Features

- Add, edit and delete products
- Track quantity, price, expiration date, composition, packing and category
- Web interface secured by a simple username/password login
- REST API for integrations
- Export products to CSV via `/export`
- Mobile interface implemented as a PWA

## Getting Started

1. Install dependencies:
   ```bash
   pip install -r requirements.txt
   ```
 codex/remove-php-files-and-migrate-to-python
2. (Optional) import `sample_data.sql` using the SQLite shell.
3. Start the application:
   ```bash
   python app.py
   ```
4. Visit `http://localhost:5000/login` to sign in. Sample accounts:
   - Manufacturer admin: `admin` / `admin123`
   - Clearing agent: `cfa` / `cfa123`
   - Super stockists: `stockist1`..`stockist20` with passwords `stock1`..`stock20`
5. After login you are redirected to the appropriate dashboard:
   - Manufacturer dashboard at `/mobile/admin`
   - CFA dashboard at `/mobile/cfa`
   - Stockist dashboard at `/mobile/stockist/<name>`
   - Generic PWA entry point available at `/mobile` which forwards to the
     correct dashboard based on the logged in user

The database is automatically created on first run. You can reset it anytime by visiting `/init`.

## API

All product operations are available under `/api/products`.

- `GET /api/products` – list products
- `GET /api/products/<id>` – retrieve a single product
- `POST /api/products` – add a product (JSON body)
- `PUT /api/products/<id>` – update a product (JSON body)
- `DELETE /api/products/<id>` – remove a product

Responses are JSON encoded.

## Files

- `app.py` – Flask application
- `schema.sql` – Database schema
- `templates/` – HTML templates
- `static/` – CSS, JavaScript, manifest and service worker
- `sample_data.sql` – Example products for testing
