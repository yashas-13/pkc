// Base URL for the Flask REST API
const API = '/api/products';

function loadProducts() {
    fetch(API)
        .then(r => r.json())
        .then(renderProducts);
}

function renderProducts(data) {
    const list = document.getElementById('list');
    list.innerHTML = '';
    data.forEach(p => {
        const div = document.createElement('div');
        div.className = 'product';
        div.innerHTML = `<strong>${p.name}</strong> 
            <button onclick="editProduct(${p.id})">Edit</button>
            <button onclick="deleteProduct(${p.id})">Delete</button>
            <p>${p.content}</p>
            <p>${p.packing} - ${p.category}</p>
            <p>Qty: ${p.quantity} Price: ${p.price}</p>
            <p>Expiration: ${p.expiration_date || ''}</p>`;
        list.appendChild(div);
    });
}

function editProduct(id) {
    fetch(`${API}/${id}`)
        .then(r => r.json())
        .then(fillForm);
}

function deleteProduct(id) {
    if (!confirm('Delete this product?')) return;
    fetch(`${API}/${id}`, { method: 'DELETE' })
        .then(loadProducts);
}

function fillForm(p) {
    document.getElementById('form-title').textContent = 'Edit Product';
    document.getElementById('prod-id').value = p.id;
    document.getElementById('name').value = p.name;
    document.getElementById('content').value = p.content;
    document.getElementById('packing').value = p.packing;
    document.getElementById('category').value = p.category;
    document.getElementById('quantity').value = p.quantity;
    document.getElementById('price').value = p.price;
    document.getElementById('expiration').value = p.expiration_date || '';
}

function resetForm() {
    document.getElementById('form-title').textContent = 'Add Product';
    document.getElementById('product-form').reset();
    document.getElementById('prod-id').value = '';
}

document.getElementById('cancel').addEventListener('click', resetForm);

document.getElementById('product-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const id = document.getElementById('prod-id').value;
    const data = {
        name: document.getElementById('name').value,
        content: document.getElementById('content').value,
        packing: document.getElementById('packing').value,
        category: document.getElementById('category').value,
        quantity: parseInt(document.getElementById('quantity').value) || 0,
        price: parseFloat(document.getElementById('price').value) || 0,
        expiration_date: document.getElementById('expiration').value
    };
    const options = {
        method: id ? 'PUT' : 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    };
    const url = id ? `${API}/${id}` : API;
    fetch(url, options).then(() => {
        resetForm();
        loadProducts();
    });
});

loadProducts();
