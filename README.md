# Pharma Inventory Management

This project provides a basic Python (Flask)-based inventory management system for pharmaceutical manufacturers.

## Features

- Add, edit, and delete products
- Track product quantity, price, expiration date, composition, packing, and category
- Simple web interface using Flask and SQLite
- REST API for mobile and other integrations
- Basic username/password login

## Getting Started

1. Install dependencies: `pip install -r requirements.txt`.
2. Run `python app.py` once with `/init` appended to initialize the SQLite database.
3. (Optional) Import `sample_data.sql` into the database using the SQLite shell.
4. Visit `http://localhost:5000/login` to sign in with the admin credentials.
5. After logging in you can manage products via the web interface (`/inventory`).
6. (Optional) Use `/mobile` for a simple mobile-friendly interface.

## Login

The system uses a very basic login mechanism. The default credentials are set in `app.py`:

```python
ADMIN_USERNAME = 'admin'
ADMIN_PASSWORD = 'admin123'
```

Run the application and navigate to `/login` to sign in. Once logged in you can add, edit, or delete products via the web UI.

## API

All product operations are also exposed through a REST API under `/api/products`.

- `GET /api/products` - list products
- `GET /api/products/<id>` - retrieve a single product
- `POST /api/products` - add a product (JSON body)
- `PUT /api/products/<id>` - update a product (JSON body)
- `DELETE /api/products/<id>` - remove a product

Responses are JSON encoded.

### Testing with `curl`

You can interact with the API from the command line once it is uploaded or running locally:

```bash
# List products
curl http://localhost:5000/api/products

# Create a product
curl -X POST -H "Content-Type: application/json" \
  -d '{"name":"Sample","quantity":10}' \
  http://localhost:5000/api/products

# Update a product
curl -X PUT -H "Content-Type: application/json" \
  -d '{"price":5.50}' \
  http://localhost:5000/api/products/1

# Delete a product
curl -X DELETE http://localhost:5000/api/products/1
```

## Files

- `app.py` - Flask application
- `schema.sql` - Database schema
- `templates/` - HTML templates
- `static/style.css` - Basic styling
- `static/mobile.js` - Mobile client script
- `sample_data.sql` - Example products for testing

This is a minimal example and can be extended to meet specific business needs.
