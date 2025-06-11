const API = '/api/products';

function loadProducts() {
    fetch(API)
        .then(r => r.json())
        .then(data => {
            const list = document.getElementById('list');
            list.innerHTML = '';
            data.forEach(p => {
                const div = document.createElement('div');
                div.className = 'product';
                div.innerHTML = `<strong>${p.name}</strong>
                    <p>${p.content}</p>
                    <p>${p.packing} - ${p.category}</p>
                    <p>Qty: ${p.quantity} Price: ${p.price}</p>
                    <p>Expiration: ${p.expiration_date || ''}</p>`;
                list.appendChild(div);
            });
        });
}

loadProducts();
