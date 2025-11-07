
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
const editOverlay = document.getElementById('edit-overlay');
const closeEdit = document.getElementById('cancel-edit');
const editTaskBtn = document.querySelectorAll('.edit-task');
const editTitle = document.getElementById('edit_title');
const editTaskId = document.getElementById('edit_task_id');
const doneBox = document.getElementById('done-box');
const editIsDone = document.getElementById('edit_is_done');

let isDone = false;



closeEdit.addEventListener('click', () => {
    editOverlay.classList.remove('show');
});

editTaskBtn.forEach(button => {
    button.addEventListener('click', () => {
        const taskTitle = button.getAttribute('data-title');
        editTitle.value = taskTitle;

        const taskId = button.getAttribute('data-id'); 
        editTaskId.value = taskId;

        const currentDone = button.closest('tr').querySelector('td').classList.contains('done-task');
        isDone = currentDone;
        doneBox.textContent = isDone ? 'X' : '';
        editIsDone.value = isDone ? '1' : '0';


        editOverlay.classList.add('show');
    });
});



doneBox.addEventListener('click', () => {
    isDone = !isDone;
    doneBox.textContent = isDone ? 'X' : '';
    editIsDone.value = isDone ? '1' : '0';
});

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