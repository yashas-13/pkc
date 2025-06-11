from flask import Flask, render_template, request, redirect, url_for, session, jsonify, Response
from flask import g
import csv
import io
import sqlite3
import os

app = Flask(__name__)
app.config['SECRET_KEY'] = 'change-this-key'
app.config['DATABASE'] = os.path.join(app.root_path, 'inventory.db')

from passlib.hash import bcrypt


def _init_db(db):
    """Create database tables and load sample data."""
    with open(os.path.join(app.root_path, 'schema.sql'), 'r') as f:
        db.executescript(f.read())
    sample_path = os.path.join(app.root_path, 'sample_data.sql')
    if os.path.exists(sample_path):
        with open(sample_path, 'r') as sf:
            db.executescript(sf.read())
    db.commit()


def get_db():
    """Return a database connection, initializing if needed."""
    if 'db' not in g:
        need_init = not os.path.exists(app.config['DATABASE'])
        g.db = sqlite3.connect(app.config['DATABASE'])
        g.db.row_factory = sqlite3.Row
        if need_init:
            _init_db(g.db)
        else:
            cur = g.db.execute(
                "SELECT name FROM sqlite_master WHERE type='table' AND name='products'"
            )
            if not cur.fetchone():
                _init_db(g.db)
    return g.db


@app.teardown_appcontext
def close_db(exception=None):
    db = g.pop('db', None)
    if db is not None:
        db.close()


def init_db():
    """Public helper to reset the database."""
    _init_db(get_db())


@app.route('/init')
def init_route():
    init_db()
    return 'Database initialized'


@app.route('/')
def index():
    if 'username' not in session:
        return redirect(url_for('login'))
    role = session.get('role')
    if role == 'manufacturer':
        return redirect(url_for('mobile_admin'))
    elif role == 'cfa':
        return redirect(url_for('mobile_cfa'))
    elif role == 'stockist':
        return redirect(url_for('mobile_stockist', name=session.get('username')))
    return render_template('index.html')


def login_required(fn):
    from functools import wraps
    @wraps(fn)
    def wrapper(*args, **kwargs):
        if 'username' not in session:
            return redirect(url_for('login'))
        return fn(*args, **kwargs)
    return wrapper




@app.route('/mobile/admin')
@login_required
def mobile_admin():
    if session.get('role') != 'manufacturer':
        return 'Forbidden', 403
    return render_template('mobile_admin.html')


@app.route('/mobile/cfa')
@login_required
def mobile_cfa():
    role = session.get('role')
    if role not in ('manufacturer', 'cfa'):
        return 'Forbidden', 403
    return render_template('mobile_cfa.html')


@app.route('/mobile/stockist/<name>')
@login_required
def mobile_stockist(name):
    role = session.get('role')
    if role == 'stockist' and name != session.get('username'):
        return 'Forbidden', 403
    return render_template('mobile_stockist.html', stockist=name)


@app.route('/login', methods=['GET', 'POST'])
def login():
    error = None
    if request.method == 'POST':
        username = request.form['username']
        password = request.form['password']
        db = get_db()
        cur = db.execute('SELECT password, role FROM users WHERE username=?', (username,))
        row = cur.fetchone()
        if row and bcrypt.verify(password, row['password']):
            session['username'] = username
            session['role'] = row['role']
            if row['role'] == 'manufacturer':
                return redirect(url_for('mobile_admin'))
            elif row['role'] == 'cfa':
                return redirect(url_for('mobile_cfa'))
            elif row['role'] == 'stockist':
                return redirect(url_for('mobile_stockist', name=username))
            return redirect(url_for('inventory'))
        error = 'Invalid credentials'
    return render_template('login.html', error=error)


@app.route('/logout')
def logout():
    session.pop('username', None)
    session.pop('role', None)
    return redirect(url_for('login'))


@app.route('/inventory')
@login_required
def inventory():
    db = get_db()
    search = request.args.get('search', '')
    if search:
        cur = db.execute(
            "SELECT * FROM products WHERE name LIKE ? OR category LIKE ?",
            (f"%{search}%", f"%{search}%")
        )
    else:
        cur = db.execute("SELECT * FROM products")
    products = cur.fetchall()
    return render_template('inventory.html', products=products, search=search)


@app.route('/add', methods=['GET', 'POST'])
@login_required
def add():
    if request.method == 'POST':
        db = get_db()
        db.execute(
            "INSERT INTO products (name, content, packing, category, quantity, price, expiration_date)"
            " VALUES (?, ?, ?, ?, ?, ?, ?)",
            (
                request.form['name'],
                request.form.get('content', ''),
                request.form.get('packing', ''),
                request.form.get('category', ''),
                int(request.form.get('quantity', 0)),
                float(request.form.get('price', 0)),
                request.form.get('expiration') or None,
            ),
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
            "UPDATE products SET name=?, content=?, packing=?, category=?, quantity=?, price=?, expiration_date=? WHERE id=?",
            (
                request.form['name'],
                request.form.get('content', ''),
                request.form.get('packing', ''),
                request.form.get('category', ''),
                int(request.form.get('quantity', 0)),
                float(request.form.get('price', 0)),
                request.form.get('expiration') or None,
                pid,
            ),
        )
        db.commit()
        return redirect(url_for('inventory'))
    cur = db.execute("SELECT * FROM products WHERE id=?", (pid,))
    product = cur.fetchone()
    if not product:
        return 'Product not found', 404
    return render_template('edit_product.html', product=product)


@app.route('/delete/<int:pid>')
@login_required
def delete(pid):
    db = get_db()
    db.execute("DELETE FROM products WHERE id=?", (pid,))
    db.commit()
    return redirect(url_for('inventory'))


# API endpoints
@app.route('/api/products', methods=['GET', 'POST'])
def api_products():
    db = get_db()
    if request.method == 'GET':
        cur = db.execute("SELECT * FROM products")
        products = [dict(row) for row in cur.fetchall()]
        return jsonify(products)
    else:  # POST
        data = request.get_json(force=True)
        cur = db.execute(
            "INSERT INTO products (name, content, packing, category, quantity, price, expiration_date)"
            " VALUES (?, ?, ?, ?, ?, ?, ?)",
            (
                data.get('name', ''),
                data.get('content', ''),
                data.get('packing', ''),
                data.get('category', ''),
                int(data.get('quantity', 0)),
                float(data.get('price', 0)),
                data.get('expiration_date'),
            ),
        )
        db.commit()
        return jsonify({'id': cur.lastrowid})


@app.route('/api/products/<int:pid>', methods=['GET', 'PUT', 'DELETE'])
def api_product(pid):
    db = get_db()
    if request.method == 'GET':
        cur = db.execute("SELECT * FROM products WHERE id=?", (pid,))
        row = cur.fetchone()
        return jsonify(dict(row) if row else {})
    elif request.method == 'PUT':
        data = request.get_json(force=True)
        db.execute(
            "UPDATE products SET name=?, content=?, packing=?, category=?, quantity=?, price=?, expiration_date=? WHERE id=?",
            (
                data.get('name', ''),
                data.get('content', ''),
                data.get('packing', ''),
                data.get('category', ''),
                int(data.get('quantity', 0)),
                float(data.get('price', 0)),
                data.get('expiration_date'),
                pid,
            ),
        )
        db.commit()
        return jsonify({'updated': True})
    else:  # DELETE
        db.execute("DELETE FROM products WHERE id=?", (pid,))
        db.commit()
        return jsonify({'deleted': True})


@app.route('/export')
@login_required
def export_csv():
    """Download all products as a CSV file."""
    db = get_db()
    cur = db.execute('SELECT * FROM products')
    output = io.StringIO()
    writer = csv.writer(output)
    writer.writerow([d[0] for d in cur.description])
    for row in cur.fetchall():
        writer.writerow(row)
    resp = Response(output.getvalue(), mimetype='text/csv')
    resp.headers['Content-Disposition'] = 'attachment; filename=products.csv'
    return resp


if __name__ == '__main__':
    app.run(debug=True)
