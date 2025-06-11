CREATE TABLE IF NOT EXISTS products (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    quantity INTEGER DEFAULT 0,
    price REAL DEFAULT 0,
    content TEXT,
    packing TEXT,
    category TEXT
);
