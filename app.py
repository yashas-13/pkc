import sys
import sqlite3
from flask import Flask, g, render_template, request, redirect, url_for, session, jsonify

app = Flask(__name__)
app.secret_key = 'change-me'
DATABASE = 'inventory.db'
ADMIN_USERNAME = 'admin'
ADMIN_PASSWORD = 'admin123'


def get_db():
    if 'db' not in g:
        g.db = sqlite3.connect(DATABASE)
        g.db.row_factory = sqlite3.Row
    return g.db


@app.teardown_appcontext
def close_db(exception=None):
    db = g.pop('db', None)
    if db is not None:
        db.close()


def init_db():
    db = get_db()
    with open('schema.sql') as f:
        db.executescript(f.read())
    db.commit()


@app.cli.command('initdb')
def initdb_command():
    """Initialize the database."""
    init_db()
    print('Initialized the database.')


@app.route('/login', methods=['GET', 'POST'])
def login():
    if request.method == 'POST':
        if request.form['username'] == ADMIN_USERNAME and request.form['password'] == ADMIN_PASSWORD:
            session['user'] = ADMIN_USERNAME
            return redirect(url_for('index'))
        return render_template('login.html', error='Invalid credentials')
    return render_template('login.html')


@app.route('/logout')
def logout():
    session.pop('user', None)
    return redirect(url_for('login'))


def login_required(func):
    from functools import wraps
    @wraps(func)
    def wrapped(*args, **kwargs):
        if 'user' not in session:
            return redirect(url_for('login'))
        return func(*args, **kwargs)
    return wrapped


@app.route('/')
@login_required
def index():
    return render_template('index.html')


@app.route('/inventory')
@login_required
def inventory():
    db = get_db()
    products = db.execute('SELECT * FROM products').fetchall()
    return render_template('inventory.html', products=products)


@app.route('/add', methods=['GET', 'POST'])
@login_required
def add():
    if request.method == 'POST':
        db = get_db()
        db.execute(
            'INSERT INTO products (name, quantity, price, content, packing, category) VALUES (?, ?, ?, ?, ?, ?)',
            (
                request.form['name'],
                request.form.get('quantity', 0),
                request.form.get('price', 0),
                request.form.get('content', ''),
                request.form.get('packing', ''),
                request.form.get('category', '')
            )
        )
        db.commit()
        return redirect(url_for('inventory'))
    return render_template('add_product.html')


@app.route('/edit/<int:pid>', methods=['GET', 'POST'])
@login_required
def edit(pid):
    db = get_db()
    if request.method == 'POST':
        db.execute(
            'UPDATE products SET name=?, quantity=?, price=?, content=?, packing=?, category=? WHERE id=?',
            (
                request.form['name'],
                request.form.get('quantity', 0),
                request.form.get('price', 0),
                request.form.get('content', ''),
                request.form.get('packing', ''),
                request.form.get('category', ''),
                pid
            )
        )
        db.commit()
        return redirect(url_for('inventory'))
    product = db.execute('SELECT * FROM products WHERE id=?', (pid,)).fetchone()
    if product is None:
        return redirect(url_for('inventory'))
    return render_template('edit_product.html', p=product)


@app.route('/delete/<int:pid>')
@login_required
def delete(pid):
    db = get_db()
    db.execute('DELETE FROM products WHERE id=?', (pid,))
    db.commit()
    return redirect(url_for('inventory'))


@app.route('/api/products', methods=['GET', 'POST'])
def api_products():
    db = get_db()
    if request.method == 'POST':
        data = request.get_json()
        db.execute(
            'INSERT INTO products (name, quantity, price, content, packing, category) VALUES (?, ?, ?, ?, ?, ?)',
            (
                data.get('name'),
                data.get('quantity', 0),
                data.get('price', 0),
                data.get('content'),
                data.get('packing'),
                data.get('category')
            )
        )
        db.commit()
        pid = db.execute('SELECT last_insert_rowid()').fetchone()[0]
        product = db.execute('SELECT * FROM products WHERE id=?', (pid,)).fetchone()
        return jsonify(dict(product)), 201
    else:
        products = db.execute('SELECT * FROM products').fetchall()
        return jsonify([dict(p) for p in products])


@app.route('/api/products/<int:pid>', methods=['GET', 'PUT', 'DELETE'])
def api_product(pid):
    db = get_db()
    if request.method == 'GET':
        product = db.execute('SELECT * FROM products WHERE id=?', (pid,)).fetchone()
        if product:
            return jsonify(dict(product))
        return jsonify({'error': 'not found'}), 404
    elif request.method == 'PUT':
        data = request.get_json()
        db.execute(
            'UPDATE products SET name=?, quantity=?, price=?, content=?, packing=?, category=? WHERE id=?',
            (
                data.get('name'),
                data.get('quantity', 0),
                data.get('price', 0),
                data.get('content'),
                data.get('packing'),
                data.get('category'),
                pid
            )
        )
        db.commit()
        product = db.execute('SELECT * FROM products WHERE id=?', (pid,)).fetchone()
        return jsonify(dict(product))
    else:  # DELETE
        db.execute('DELETE FROM products WHERE id=?', (pid,))
        db.commit()
        return jsonify({'status': 'deleted'})


if __name__ == '__main__':
    if len(sys.argv) > 1 and sys.argv[1] == 'initdb':
        with app.app_context():
            init_db()
        print('Database initialized.')
    else:
        app.run(debug=True)
