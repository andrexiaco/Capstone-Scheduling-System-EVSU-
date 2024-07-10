function openAddModal() {
    var addModal = document.getElementById('addModal');
    if (addModal) {
        addModal.style.display = 'flex';
    }
}

function openEditModal(room_id, room_name) {
    var editModal = document.getElementById('editModal');
    if (editModal) {
        editModal.style.display = 'flex';
        // Set the values of the fields based on the selected product
        document.getElementById('edit_room_id').value = room_id;
        document.getElementById('edit_room_name').value = room_name;
    }
}

function closeModal(modalId) {
    var modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
    }
}

window.onclick = function (event) {

    var addModal = document.getElementById('addModal');
    var editModal = document.getElementById('editModal');

    if (event.target == editModal) {
        closeModal('editModal');
    }

    if (event.target == addModal) {
        closeModal('addModal');
    }
}