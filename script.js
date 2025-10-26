
const overlay = document.getElementById('profile-overlay');
const openBtn = document.getElementById('profile-btn');
const closeBtn = document.getElementById('close-card');
const formBox = document.getElementById('form-container');
const tableBox = document.getElementById('todo-table');

const miniName  = document.getElementById('popup-name');
const miniEmail = document.getElementById('popup-email');
const editNameBtn  = document.getElementById('edit-name');
const editEmailBtn = document.getElementById('edit-email');

function showOverlay() {
    overlay.classList.add('show');
    formBox.classList.add('hidden');
    tableBox.classList.add('hidden');
}

function hideOverlay() {
    overlay.classList.remove('show');
    formBox.classList.remove('hidden');
    tableBox.classList.remove('hidden');
    hideMini('popup-name');
    hideMini('popup-email');
}

function hideMini(id) {
    const el = document.getElementById(id);
    if (el) el.classList.remove('show');
}

function showMini(id) {
    const el = document.getElementById(id);
    if (el) el.classList.add('show');
}

if (openBtn) openBtn.addEventListener('click', showOverlay);
if (closeBtn) closeBtn.addEventListener('click', hideOverlay);
if (overlay) overlay.addEventListener('click', (e) => {
    if (e.target === overlay) hideOverlay();
});
if (editNameBtn) editNameBtn.addEventListener('click', () => showMini('popup-name'));
if (editEmailBtn) editEmailBtn.addEventListener('click', () => showMini('popup-email'));




