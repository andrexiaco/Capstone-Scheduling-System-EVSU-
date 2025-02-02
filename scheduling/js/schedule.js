function confirmDelete() {
            return confirm('Are you sure you want to delete?');
        }


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

        document.getElementById('edit_id').value = id;
        document.getElementById('edit_date').value = date;
        document.getElementById('edit_start_time').value = start_time;
        document.getElementById('edit_end_time').value = end_time;
        document.getElementById('edit_student_name').value = student_name;
        document.getElementById('edit_capstone_title').value = capstone_title;


        setSelectedOption('edit_adviser_name', adviser_name);
        setSelectedOption('edit_lead_panelist', lead_panelist);
        setSelectedOption('edit_panelist2', panelist2);
        setSelectedOption('edit_panelist3', panelist3);
        setSelectedOption('edit_room_id', room_id);

        document.getElementById('edit_room_id').value = room_id;
    }
}


function setSelectedOption(selectId, selectedValue) {
    var selectElement = document.getElementById(selectId);
    if (selectElement) {
        for (var i = 0; i < selectElement.options.length; i++) {
            // Compare using trimmed values to handle any extra spaces
            if (selectElement.options[i].text.trim() === selectedValue.trim()) {
                selectElement.selectedIndex = i;
                break;
            }
        }
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