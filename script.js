
const profileOverlay = document.getElementById('profile-overlay');
const profileBtn = document.getElementById('profile-btn');
const profileCloseBtn = document.getElementById('close-card');
const taskFormBox = document.getElementById('form-container');
const tableBox = document.getElementById('todo-table');
const miniName  = document.getElementById('popup-name');
const editNameBtn  = document.getElementById('edit-name');
const addTaskBtn = document.getElementById('add-task');
const logoutBtn = document.getElementById('logoutBtn');
const profIcon = document.getElementById('prof-icon');
const dashboard = document.getElementById('dashboard');


profileBtn.addEventListener('click', () => {
    profileOverlay.classList.add('show');
    addTaskBtn.classList.add('hidden');
    tableBox.classList.add('hidden');
});

profileCloseBtn.addEventListener('click', () => {
    profileOverlay.classList.remove('show');
    addTaskBtn.classList.remove('hidden');
    tableBox.classList.remove('hidden');
    miniName.classList.remove('show');
});

editNameBtn.addEventListener('click', () => {
    miniName.classList.add('show');
});

addTaskBtn.addEventListener('click', () => {
    taskFormBox.classList.add('show');
    addTaskBtn.classList.add('hidden');
    tableBox.classList.add('hidden');
});

logoutBtn.addEventListener('click', ()=>{
    window.location.href = 'logout.php';
});

profIcon.addEventListener('click', () => {
    dashboard.classList.toggle('show');
});