import os
import tempfile
import json
import sqlite3
import unittest

from app import app, _init_db

class APITestCase(unittest.TestCase):
    def setUp(self):
        self.db_fd, self.db_path = tempfile.mkstemp()
        app.config['DATABASE'] = self.db_path
        app.config['TESTING'] = True
        with app.app_context():
            db = sqlite3.connect(self.db_path)
            _init_db(db)
            db.close()
        self.client = app.test_client()

    def tearDown(self):
        os.close(self.db_fd)
        os.unlink(self.db_path)

    def test_list_products(self):
        rv = self.client.get('/api/products')
        self.assertEqual(rv.status_code, 200)
        data = rv.get_json()
        self.assertTrue(len(data) > 0)

    def test_crud_flow(self):
        # create
        new_data = {
            'name': 'Test',
            'content': '',
            'packing': '',
            'category': '',
            'quantity': 1,
            'price': 1.0,
            'expiration_date': None
        }
        rv = self.client.post('/api/products', json=new_data)
        self.assertEqual(rv.status_code, 200)
        pid = rv.get_json()['id']
        # retrieve
        rv = self.client.get(f'/api/products/{pid}')
        self.assertEqual(rv.status_code, 200)
        self.assertEqual(rv.get_json()['name'], 'Test')
        # update
        rv = self.client.put(f'/api/products/{pid}', json={'name': 'Updated'})
        self.assertEqual(rv.status_code, 200)
        rv = self.client.get(f'/api/products/{pid}')
        self.assertEqual(rv.get_json()['name'], 'Updated')
        # delete
        rv = self.client.delete(f'/api/products/{pid}')
        self.assertEqual(rv.status_code, 200)
        rv = self.client.get(f'/api/products/{pid}')
        self.assertEqual(rv.get_json(), {})

if __name__ == '__main__':
    unittest.main()
