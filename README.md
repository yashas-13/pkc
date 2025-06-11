# Pharma Inventory System

This project is a simplified Python Flask application for managing pharmaceutical inventory. It uses SQLite for storage and provides basic CRUD operations via a web interface and a small REST API.

## Setup

1. Install dependencies:
   ```bash
   pip install -r requirements.txt
   ```
2. Initialize the database (first time only):
   ```bash
   python app.py initdb
   ```
3. Run the application:
   ```bash
   python app.py
   ```
4. Visit `http://localhost:5000` in your browser. Default login credentials are **admin/admin123**.

## API Endpoints

- `GET /api/products` – list products
- `POST /api/products` – add a product (JSON)
- `PUT /api/products/<id>` – update a product
- `DELETE /api/products/<id>` – delete a product

All responses are JSON encoded.

This is a minimal starting point and can be extended to meet further requirements.
