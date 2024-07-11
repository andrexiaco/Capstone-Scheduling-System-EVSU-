let editableField = null; // Variable to store the currently editable field

// Function to show modal with full text
function showModal(text, field) {
    const modal = document.getElementById('myModal');
    const modalContent = document.getElementById('modalContent');

    // Clear any previous content
    modalContent.innerHTML = '';

    // Create an input or select element based on the field type
    let editElement;
    if (field.tagName === 'INPUT' && field.type === 'text') {
        editElement = document.createElement('input');
        editElement.type = 'text';
        editElement.value = text;
    } else if (field.tagName === 'SELECT') {
        editElement = document.createElement('select');
        const options = field.options;
        for (let i = 0; i < options.length; i++) {
            const option = document.createElement('option');
            option.value = options[i].value;
            option.textContent = options[i].textContent;
            if (options[i].value === text) {
                option.selected = true;
            }
            editElement.appendChild(option);
        }
    }

    // Append the edit element to modal content
    modalContent.appendChild(editElement);

    // Store the current editable field
    editableField = field;

    // Display the modal
    modal.style.display = 'block';
}

// Function to handle saving edited text
function saveModalChanges() {
    if (editableField) {
        let newText;
        if (editableField.tagName === 'INPUT' && editableField.type === 'text') {
            newText = modalContent.querySelector('input[type="text"]').value;
        } else if (editableField.tagName === 'SELECT') {
            newText = modalContent.querySelector('select').value;
        }
        editableField.value = newText; // Update the input/select field with new text
        closeModal(); // Close the modal after saving changes
    }
}

// Function to close the modal
function closeModal() {
    const modal = document.getElementById('myModal');
    modal.style.display = 'none';
    editableField = null; // Clear the editable field variable
}

// When the DOM is fully loaded
document.addEventListener('DOMContentLoaded', function () {
    // Get all input and select elements that show modal on click
    const elements = document.querySelectorAll('input[type="text"][data-toggle="modal"], select[data-toggle="modal"]');

    // Attach click event listener to elements
    elements.forEach(element => {
        element.addEventListener('click', function () {
            showModal(element.value, element); // Show modal with current value and element reference
        });
    });
});

// Close the modal when clicking on close button or outside the modal
document.addEventListener('click', function (event) {
    const modal = document.getElementById('myModal');
    if (event.target === modal) {
        closeModal();
    }
});

// Close modal when clicking on the close button
document.getElementById('closeBtn').addEventListener('click', closeModal);

// Save changes when clicking the save button in modal
document.getElementById('saveBtn').addEventListener('click', saveModalChanges);