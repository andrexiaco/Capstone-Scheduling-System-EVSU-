function confirmDelete() {
    return confirm('Are you sure you want to delete?');
}


function openAddModal() {
    var addModal = document.getElementById('addModal');
    if (addModal) {
        addModal.style.display = 'flex';
    }
}


function closeModal(modalId) {
    var modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
    }
}

window.onclick = function(event) {

    var addModal = document.getElementById('addModal');
    var editModal = document.getElementById('editModal');

    if (event.target == editModal) {
        closeModal('editModal');
    }

    if (event.target == addModal) {
        closeModal('addModal');
    }
}