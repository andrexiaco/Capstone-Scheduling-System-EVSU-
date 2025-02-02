<?php
require_once('function/functions.php');

$profs = GetProf();
$errors = array();
session_start();

if (isset($_POST['Submit'])) {
    $prof_name = $_POST['prof_name'];

    if (empty($prof_name)) {
        $errors['prof_name'] = 'The Professors name is required!';
    }

    if (checkProfExists($prof_name)) {
        $errors['prof_name'] = 'The professor name you entered is already existing!';
    }

    if (count($errors) === 0) {
        insertProf($prof_name);
        $_SESSION['flash_message'] = 'Professors name is added successfully!';

        // Redirect to the same page to avoid form resubmission on page refresh
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    } 
}

if (isset($_POST['Save'])) {
    $prof_id = $_POST['edit_prof_id'];
    $prof_name = $_POST['edit_prof_name'];

    if (empty($prof_name)) {
        $errors['prof_name'] = 'The Professors name is required!';
    }

    if (checkProfExists($prof_name)) {
        $errors['edit_prof_name'] = 'The professor name you entered is already existing!';
    }

    if (count($errors) === 0) {
        updateProf($prof_id, $prof_name);
        $_SESSION['flash_message'] = 'Professors name is updated successfully!';

        // Redirect to the same page to avoid form resubmission on page refresh
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    } 
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the Delete button is clicked
    if (isset($_POST['Delete'])) {
        $prof_id = $_POST['prof_id'];

        // Attempt to delete professor
        $result = deleteProf($prof_id);

        if ($result === true) {
            $_SESSION['flash_message'] = 'Professor deleted successfully!';
        } else {
            $_SESSION['error_message'] = 'Failed to delete professor. This professor is referenced in active schedules.';
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
    <title>Professors Page</title>
</head>

<body>
    <div class="container">
        <a href="index.php">
            <button type="submit" style="font-size: 30px; color: red; background-color: transparent;" class='bx bx-arrow-back' id="log_out"></button>
        </a>
        <h2>Professors Table</h2>
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
                    <th>Professor Name</th>
                    <th>Actions</th>
                </tr>
            <tbody>
                <?php foreach ($profs as $prof) : ?>
                    <tr>
                        <td><?= $prof['prof_name']; ?></td>
                        <td><button type="button" class="edit-btn" onclick="openEditModal(
        <?= $prof['prof_id']; ?>,
        '<?= $prof['prof_name']; ?>',
    )">Edit</button>
                            <form action="" method="POST" onsubmit="return confirmDelete();">
                                <input type="hidden" name="prof_id" value="<?= $prof['prof_id']; ?>">
                                <button type="submit" name="Delete" class="delete-btn">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </thead>
            </tbody>
        </table>
    </div>


    <?php $profErrormsg = isset($errors['prof_name']) ? $errors['prof_name'] : ''; ?>
    <div id="addModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal('addModal')">&times;</span>
            <h3>Add Professor</h3>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" id="addForm">
                <label for="prof_name">Professor Name: <span class="error-message" id='prof_error'><?php echo $profErrormsg; ?></span></label>
                <input type="text" id="prof_name" name="prof_name">
                <button type="submit" class="submit-btn" name="Submit">Submit</button>
            </form>
        </div>
    </div>

    <?php $eprofError = isset($errors['edit_prof_name']) ? $errors['edit_prof_name'] : ''; ?>
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal('editModal')">&times;</span>
            <h3>Edit Professor</h3>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" id="editForm">
                <!-- You can dynamically populate these fields with the selected product's data -->
                <input type="hidden" name="edit_prof_id" id="edit_prof_id">
                <label for="edit_prof_name">Professors Name: <span class="error-message" id='eprof_error'><?php echo $eprofError; ?></span></label>
                <input type="text" id="edit_prof_name" name="edit_prof_name">

                <button type="submit" class="submit-btn" name="Save">Save Changes</button>
            </form>
        </div>
    </div>
    <script src="js/prof.js"></script>
</body>

</html>
