function openAddModal() {
    var addModal = document.getElementById('addModal');
    if (addModal) {
        addModal.style.display = 'flex';
    }
}

function openEditModal(id, date, start_time, end_time, adviser_name, student_name, capstone_title, lead_panelist, panelist2, panelist3, room_id) {
    var editModal = document.getElementById('editModal');
    if (editModal) {
        editModal.style.display = 'flex';
        // Set the values of the fields based on the selected product
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_date').value = date;
        document.getElementById('edit_start_time').value = start_time;
        document.getElementById('edit_end_time').value = end_time;
        document.getElementById('edit_adviser_name').value = adviser_name;
        document.getElementById('edit_student_name').value = student_name;
        document.getElementById('edit_capstone_title').value = capstone_title;
        document.getElementById('edit_lead_panelist').value = lead_panelist;
        document.getElementById('edit_panelist2').value = panelist2;
        document.getElementById('edit_panelist3').value = panelist3;
        document.getElementById('edit_room_id').value = room_id;
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