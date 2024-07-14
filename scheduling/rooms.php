<?php
require_once('function/functions.php');

$rooms = GetRoom();
$errors = array();
session_start();

if (isset($_POST['Submit'])) {
    $room_name = $_POST['room_name'];

    if (empty($room_name)) {
        $errors['room_name'] = 'The Room name is required!';
    }

    if (checkRoomExists($room_name)) {
        $errors['room_name'] = 'The Room you entered is already existing!';
    }

    if (count($errors) === 0) {
        insertRoom($room_name);
        $_SESSION['flash_message'] = 'Room name is added successfully!';

        // Redirect to the same page to avoid form resubmission on page refresh
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
}


if (isset($_POST['Save'])) {
    $room_id = $_POST['edit_room_id'];
    $room_name = $_POST['edit_room_name'];

    if (empty($room_name)) {
        $errors['edit_room_name'] = 'The Room name is required!';
    }

    if (checkRoomExists($room_name)) {
        $errors['edit_room_name'] = 'The Room you entered is already existing!';
    }

    if (count($errors) === 0) {
        updateRoom($room_id, $room_name);
        $_SESSION['flash_message'] = 'Rooms name is updated successfully!';

        // Redirect to the same page to avoid form resubmission on page refresh
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the Delete button is clicked
    if (isset($_POST['Delete'])) {
        $room_id = $_POST['room_id'];

        // Attempt to delete professor
        $result = deleteRoom($room_id);

        if ($result === true) {
            $_SESSION['flash_message'] = 'Room deleted successfully!';
        } else {
            $_SESSION['error_message'] = 'Failed to delete room. This room is referenced in active schedules.';
        }

        // Redirect to the same page to avoid form resubmission on page refresh
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="css/prof.css">
    <title>Rooms Page</title>
</head>

<body>
    <div class="container">
        <a href="index.php">
            <button type="submit" style="font-size: 30px; color: red; background-color: transparent;" class='bx bx-arrow-back' id="log_out"></button>
        </a>
        <h2>Rooms Table</h2>
        <table>
            <thead>
                <button type="button" class="add-btn" onclick="openAddModal('addModal')">Add</button>

                <?php
                // Define default styles
                $messageStyles = [
                    'flash_message' => ['color' => (isset($errors) && count($errors) > 0) ? 'red' : 'green', 'background-color' => '#dfd'],
                    'error_message' => ['color' => 'red', 'background-color' => '#fdd'],
                    'empty_message' => ['color' => 'orange', 'background-color' => '#ffe']
                ];

                // Loop through each type of message
                foreach ($messageStyles as $messageType => $styles) {
                    // Check if the message exists in the session
                    if (isset($_SESSION[$messageType])) {
                        // Echo the message with appropriate styles
                        echo '<span style="color: ' . $styles['color'] . '; float: right; text-align: center; background-color: ' . $styles['background-color'] . '; border-radius: 10px; padding: 10px; margin: 10px;">';
                        echo $_SESSION[$messageType];
                        echo '</span>';

                        // Remove the message from the session
                        unset($_SESSION[$messageType]);
                    }

                    // Check for errors and display them with the same style if they exist
                    if ($messageType === 'flash_message' && isset($errors) && count($errors) > 0) {
                        foreach ($errors as $error) {
                            echo '<span style="color: red; float: right; text-align: center; background-color: #fdd; border-radius: 10px; padding: 10px; margin: 10px;">';
                            echo $error;
                            echo '</span>';
                        }
                    }
                }

                ?>

                <?php if (isset($_SESSION['flash_message'])) : ?>
                    <span style="color: <?php echo (isset($errors) && count($errors) > 0) ? 'red' : 'green'; ?>; float: right; text-align: center; background-color: #dfd; border-radius: 10px; padding: 10px; margin: 10px;">
                        <?php echo $_SESSION['flash_message']; ?>
                    </span>
                    <?php unset($_SESSION['flash_message']); // Remove the flash message from the session 
                    ?>
                <?php endif; ?>

                <tr>
                    <th>Room Name</th>
                    <th>Actions</th>
                </tr>
            <tbody>
                <?php foreach ($rooms as $room) : ?>
                    <tr>
                        <td><?= $room['room_name']; ?></td>
                        <td><button type="button" class="edit-btn" onclick="openEditModal(
        <?= $room['room_id']; ?>,
        '<?= $room['room_name']; ?>',
    )">Edit</button>
                            <form action="" method="POST" onsubmit="return confirmDelete();">
                                <input type="hidden" name="room_id" value="<?= $room['room_id']; ?>">
                                <button type="submit" name="Delete" class="delete-btn">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </thead>
            </tbody>
        </table>
    </div>


    <?php $roomErrormsg = isset($errors['room_name']) ? $errors['room_name'] : ''; ?>
    <div id="addModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal('addModal')">&times;</span>
            <h3>Add Professor</h3>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" id="addForm">
                <label for="room_name">Room Name: <span class="error-message" id='room_error'><?php echo $roomErrormsg; ?></span></label>
                <input type="text" id="room_name" name="room_name">
                <button type="submit" class="submit-btn" name="Submit">Submit</button>
            </form>
        </div>
    </div>

    <?php $eroomError = isset($errors['edit_room_name']) ? $errors['edit_room_name'] : ''; ?>
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal('editModal')">&times;</span>
            <h3>Edit Rooms</h3>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" id="editForm">
                <!-- You can dynamically populate these fields with the selected product's data -->
                <input type="hidden" name="edit_room_id" id="edit_room_id">
                <label for="edit_room_name">Rooms Name: <span class="error-message" id='eroom_error'><?php echo $eroomError; ?></span></label>
                <input type="text" id="edit_room_name" name="edit_room_name">

                <button type="submit" class="submit-btn" name="Save">Save Changes</button>
            </form>
        </div>
    </div>
    <script src="js/room.js"></script>
</body>

</html>
