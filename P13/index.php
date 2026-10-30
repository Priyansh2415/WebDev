<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard - AJAX CRUD & RBAC</title>

<style>
body {
    font-family: Arial, sans-serif;
    margin: 20px;
    background: #f4f6f9;
}

header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
    background: #343a40;
    padding: 12px 20px;
    color: #fff;
    border-radius: 5px;
}

nav ul {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}

nav a {
    color: #fff;
    text-decoration: none;
    font-weight: bold;
}

.container {
    margin-top: 20px;
    display: flex;
    gap: 20px;
}

.card {
    background: #fff;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.form-card {
    flex: 1;
}

.table-card {
    flex: 2;
    overflow-x: auto;
}

form input,
form button {
    width: 100%;
    padding: 10px;
    margin-bottom: 10px;
    box-sizing: border-box;
}

button {
    background: #28a745;
    color: #fff;
    border: none;
    cursor: pointer;
    border-radius: 3px;
    font-weight: bold;
    padding: 9px;
}

button.btn-danger {
    background: #dc3545;
}

button.btn-edit {
    background: #ffc107;
    color: #000;
}

button.btn-cancel {
    background: #6c757d;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

th, td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: left;
}

th {
    background: #007bff;
    color: white;
}

.message {
    margin-top: 10px;
    overflow-wrap: anywhere;
}

@media (max-width: 800px) {
    .container {
        flex-direction: column;
    }

    .form-card,
    .table-card {
        width: auto;
    }
}
</style>
</head>

<body>

<header>
    <h2>Admin Dashboard</h2>

    <nav>
        <ul id="dynamicMenu"></ul>
    </nav>

    <div>
        <label for="roleSelector">Active Role:</label>
        <select id="roleSelector">
            <option value="user">User</option>
            <option value="editor">Editor</option>
            <option value="admin" selected>Admin</option>
        </select>
    </div>
</header>

<div class="container">

    <div class="card form-card">
        <h3 id="formTitle">Add Item</h3>

        <form id="itemForm">
            <input type="hidden" id="itemId">

            <input type="text" id="itemName"
                   placeholder="Item Name" required>

            <input type="text" id="itemCategory"
                   placeholder="Category" required>

            <input type="number" step="0.01" min="0"
                   id="itemPrice" placeholder="Price" required>

            <button type="submit" id="submitBtn">Save Item</button>

            <button type="button" id="cancelBtn"
                    class="btn-cancel" hidden>Cancel</button>
        </form>

        <p id="message" class="message"></p>
    </div>

    <div class="card table-card">
        <h3>Inventory List</h3>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody id="itemsTableBody"></tbody>
        </table>
    </div>

</div>

<script>
const API_URL = 'api.php';

const roleSelector = document.getElementById('roleSelector');
const menu = document.getElementById('dynamicMenu');
const form = document.getElementById('itemForm');
const tbody = document.getElementById('itemsTableBody');
const message = document.getElementById('message');

let items = [];

function showMessage(text) {
    message.textContent = text;
}

async function apiRequest(action, options = {}) {
    const role = roleSelector.value;

    const response = await fetch(
        `${API_URL}?action=${encodeURIComponent(action)}&role=${encodeURIComponent(role)}`,
        options
    );

    const result = await response.json();

    if (!response.ok || result.status !== 'success') {
        throw new Error(result.message || 'Request failed.');
    }

    return result;
}

// 1. Load menu according to role
async function loadMenu() {
    menu.replaceChildren();

    try {
        const result = await apiRequest('get_menu');

        result.data.forEach(item => {
            const li = document.createElement('li');
            const link = document.createElement('a');

            link.textContent = item.title;
            link.href = item.link;

            li.appendChild(link);
            menu.appendChild(li);
        });
    } catch (error) {
        showMessage(error.message);
    }
}

// 2. Read items
async function loadItems() {
    tbody.replaceChildren();

    try {
        const result = await apiRequest('read');
        items = result.data;

        if (items.length === 0) {
            const row = tbody.insertRow();
            const cell = row.insertCell();
            cell.colSpan = 5;
            cell.textContent = 'No items found.';
            return;
        }

        items.forEach(item => {
            const row = tbody.insertRow();

            [item.id, item.name, item.category,
                '$' + Number(item.price).toFixed(2)]
                .forEach(value => {
                    const cell = row.insertCell();
                    cell.textContent = value;
                });

            const actions = row.insertCell();

            if (roleSelector.value === 'admin' ||
                roleSelector.value === 'editor') {
                const editButton = document.createElement('button');
                editButton.type = 'button';
                editButton.className = 'btn-edit';
                editButton.textContent = 'Edit';
                editButton.addEventListener('click', () => {
                    editItem(item.id);
                });
                actions.appendChild(editButton);
            }

            if (roleSelector.value === 'admin') {
                const deleteButton = document.createElement('button');
                deleteButton.type = 'button';
                deleteButton.className = 'btn-danger';
                deleteButton.textContent = 'Delete';
                deleteButton.addEventListener('click', () => {
                    deleteItem(item.id);
                });
                actions.appendChild(deleteButton);
            }
        });
    } catch (error) {
        const row = tbody.insertRow();
        const cell = row.insertCell();
        cell.colSpan = 5;
        cell.textContent = 'Unable to load items: ' + error.message;
    }
}

// 3. Add or update item
async function saveItem(event) {
    event.preventDefault();

    const id = document.getElementById('itemId').value;
    const name = document.getElementById('itemName').value.trim();
    const category = document.getElementById('itemCategory').value.trim();
    const price = document.getElementById('itemPrice').value;

    if (roleSelector.value === 'user') {
        showMessage('Users cannot add or update items.');
        return;
    }

    if (!name || !category || price === '' ||
        !Number.isFinite(Number(price)) || Number(price) < 0) {
        showMessage('Please enter valid item details.');
        return;
    }

    const action = id ? 'update' : 'create';
    const payload = { name, category, price: Number(price) };

    if (id) {
        payload.id = Number(id);
    }

    try {
        const result = await apiRequest(action, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        resetForm();
        showMessage(result.message);
        await loadItems();
    } catch (error) {
        showMessage(error.message);
    }
}

// 4. Edit item
function editItem(id) {
    const item = items.find(value => Number(value.id) === Number(id));

    if (!item) {
        showMessage('Item not found.');
        return;
    }

    document.getElementById('itemId').value = item.id;
    document.getElementById('itemName').value = item.name;
    document.getElementById('itemCategory').value = item.category;
    document.getElementById('itemPrice').value = item.price;

    document.getElementById('formTitle').textContent = 'Edit Item';
    document.getElementById('submitBtn').textContent = 'Update Item';
    document.getElementById('cancelBtn').hidden = false;

    showMessage('');
}

// 5. Delete item
async function deleteItem(id) {
    if (roleSelector.value !== 'admin') {
        showMessage('Only admins can delete items.');
        return;
    }

    if (!confirm('Are you sure you want to delete this item?')) {
        return;
    }

    try {
        const result = await apiRequest('delete', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: Number(id) })
        });

        showMessage(result.message);
        await loadItems();
    } catch (error) {
        showMessage(error.message);
    }
}

// 6. Reset form
function resetForm() {
    form.reset();
    document.getElementById('itemId').value = '';
    document.getElementById('formTitle').textContent = 'Add Item';
    document.getElementById('submitBtn').textContent = 'Save Item';
    document.getElementById('cancelBtn').hidden = true;
}

// 7. Change role
roleSelector.addEventListener('change', async () => {
    resetForm();
    await loadMenu();
    await loadItems();
});

form.addEventListener('submit', saveItem);
document.getElementById('cancelBtn').addEventListener('click', resetForm);

// Start dashboard
loadMenu();
loadItems();
</script>

</body>
</html>
