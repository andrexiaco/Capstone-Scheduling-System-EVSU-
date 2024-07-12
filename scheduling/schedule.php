<?php
require_once('function/functions.php');

$schedules = GetSchedulesOrder();
$profs = GetProf();
$rooms = GetRoom();

$errors = array();
session_start();

if (isset($_POST['Submit'])) {
    $date = $_POST['date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $adviser_name = $_POST['adviser_name'];
    $student_name = $_POST['student_name'];
    $capstone_title = $_POST['capstone_title'];
    $lead_panelist = $_POST['lead_panelist'];
    $panelist2 = $_POST['panelist2'];
    $panelist3 = $_POST['panelist3'];
    $room_id = $_POST['room_id'];

    $errors = [];

    if (empty($date)) {
        $errors['date'] = 'Date is required!';
    }

    if (empty($start_time)) {
        $errors['start_time'] = 'Starting time is required!';
    }

    if (empty($end_time)) {
        $errors['end_time'] = 'End time is required!';
    }

    if (empty($student_name)) {
        $errors['student_name'] = 'Students name is required!';
    }

    if (empty($capstone_title)) {
        $errors['capstone_title'] = 'Capstone Title is required!';
    }

    $panelists = array($lead_panelist, $panelist2, $panelist3);

    if ($adviser_name == $lead_panelist || $adviser_name == $panelist2 || $adviser_name == $panelist3) {
        $errors['adviser_name'] = 'Adviser cannot be the same as any of the panelists!';
    }

    if (count($panelists) !== count(array_unique($panelists))) {
        $errors['panelists'] = 'Panelists cannot be duplicated!';
    }

    if (appointmentExists($date, $start_time, $end_time, $lead_panelist, $panelist2, $panelist3)) {
        $errors['lead_panelist'] = 'The panelist you entered already has an appointment at the specified date, time!';
    }

    if (hasConflict($date, $start_time, $end_time, $adviser_name, $lead_panelist, $panelist2, $panelist3)) {
        $errors['adviser_name'] = 'The adviser you entered already has an appointment at the specified date, time!';
    }


    if (count($errors) === 0) {
        $conflict = checkScheduleConflict($date, $start_time, $end_time, $room_id);
        if ($conflict) {
            $errors['conflict'] = 'Schedule conflict detected. Please choose a different time or room.';
        } else {
            // If no conflict, insert schedule
            insertSchedules($date, $start_time, $end_time, $adviser_name, $student_name, $capstone_title, $lead_panelist, $panelist2, $panelist3, $room_id);
            $_SESSION['flash_message'] = 'Schedule is inserted successfully!';
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit();
        }
    }
}



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Saved'])) {
    $id = isset($_POST['edit_id']) ? $_POST['edit_id'] : '';
    $date = isset($_POST['edit_date']) ? $_POST['edit_date'] : '';
    $start_time = isset($_POST['edit_start_time']) ? $_POST['edit_start_time'] : '';
    $end_time = isset($_POST['edit_end_time']) ? $_POST['edit_end_time'] : '';
    $adviser_name = isset($_POST['edit_adviser_name']) ? $_POST['edit_adviser_name'] : '';
    $student_name = isset($_POST['edit_student_name']) ? $_POST['edit_student_name'] : '';
    $capstone_title = isset($_POST['edit_capstone_title']) ? $_POST['edit_capstone_title'] : '';
    $lead_panelist = isset($_POST['edit_lead_panelist']) ? $_POST['edit_lead_panelist'] : '';
    $panelist2 = isset($_POST['edit_panelist2']) ? $_POST['edit_panelist2'] : '';
    $panelist3 = isset($_POST['edit_panelist3']) ? $_POST['edit_panelist3'] : '';
    $room_id = isset($_POST['edit_room_id']) ? $_POST['edit_room_id'] : '';

    // Your validation and processing logic here


    $errors = [];

    // Validate required fields
    if (empty($date)) {
        $errors['edit_date'] = 'Date is required!';
    }
    if (empty($start_time)) {
        $errors['edit_start_time'] = 'Starting time is required!';
    }
    if (empty($end_time)) {
        $errors['edit_end_time'] = 'End time is required!';
    }
    if (empty($adviser_name)) {
        $errors['edit_adviser_name'] = 'Adviser name is required!';
    }
    if (empty($student_name)) {
        $errors['edit_student_name'] = 'Student name is required!';
    }
    if (empty($capstone_title)) {
        $errors['edit_capstone_title'] = 'Capstone Title is required!';
    }
    if (empty($room_id)) {
        $errors['edit_room_id'] = 'Room is required!';
    }
    if (empty($lead_panelist)) {
        $errors['edit_lead_panelist'] = 'Lead Panelist is required!';
    }
    if (empty($panelist2)) {
        $errors['edit_panelist2'] = 'Panelist 2 is required!';
    }
    if (empty($panelist3)) {
        $errors['edit_panelist3'] = 'Panelist 3 is required!';
    }

    $e_panelists = array($lead_panelist, $panelist2, $panelist3);

    // Check for duplicate panelists
    if (count($e_panelists) !== count(array_unique($e_panelists))) {
        $errors['panelists'] = 'Panelists cannot be duplicated!';
    }

    // Check for adviser being a panelist
    if (in_array($adviser_name, $e_panelists)) {
        $errors['edit_adviser_name'] = 'Adviser cannot be the same as any of the panelists!';
    }

    // Check for existing appointment conflicts
    if (appointmentExists($date, $start_time, $end_time, $lead_panelist, $panelist2, $panelist3)) {
        $errors['lead_panelist'] = 'The panelist you entered already has an appointment at the specified date and time!';
    }

    if (hasConflict($date, $start_time, $end_time, $adviser_name, $lead_panelist, $panelist2, $panelist3)) {
        $errors['edit_adviser_name'] = 'The adviser you entered already has an appointment at the specified date, time!';
    }

    // If no errors, check for schedule conflicts and update the schedule
    if (count($errors) === 0) {
        $conflict = checkScheduleConflict($date, $start_time, $end_time, $room_id);
        if ($conflict) {
            $errors['conflict'] = 'Schedule conflict detected. Please change the schedule to update.';
        } else {
            // If no conflict, update the schedule
            if (updateSchedules($id, $date, $start_time, $end_time, $adviser_name, $student_name, $capstone_title, $lead_panelist, $panelist2, $panelist3, $room_id)) {
                $_SESSION['flash_message'] = 'Schedule updated successfully!';
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit();
            } else {
                $errors['database'] = 'Failed to update schedule. Please try again.';
            }
        }
    }
}




if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the Delete button is clicked
    if (isset($_POST['Delete'])) {
        $id = $_POST['id'];

        $result = deleteSchedules($id);

        if ($result) {
            $_SESSION['flash_message'] = 'Deleted successfully!';

            // Redirect to the same page to avoid form resubmission on page refresh
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit();
        } else {
            // Set a flash error message in the session
            $_SESSION['flash_message'] = 'There are errors. Please fix them.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="css/schedule.css">
    <title>Schedules Data</title>
</head>

<body>
    <div class="container">
        <a href="index.php">
            <button type="submit" style="font-size: 30px; color: red; background-color: transparent;" class='bx bx-arrow-back' id="log_out"></button>
        </a>
        <h2>Schedules Data</h2>
        <?php if (empty($schedules)) : ?>
            <p>No schedules available. click <button type="button" class="add-btn" onclick="openAddModal('addModal')">Add</button> to add new schedules. </p>
        <?php else : ?>
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




                    <tr>
                        <th>Date</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Adviser</th>
                        <th>Students</th>
                        <th>Capstone Title</th>
                        <th>Lead Panelist</th>
                        <th>Panelist 2</th>
                        <th>Panelist 3</th>
                        <th>Room/Venue</th>
                        <th>Action</th>
                    </tr>
                <tbody>
                    <?php
                    usort($schedules, function ($a, $b) {
                        // Sort by date first
                        $dateComparison = strtotime($a['date']) - strtotime($b['date']);
                        if ($dateComparison !== 0) {
                            return $dateComparison;
                        }

                        // If dates are the same, sort by room_id
                        return $a['room_id'] - $b['room_id'];
                    });

                    $currentDate = ''; // Initialize current date variable  
                    $currentRoomId = ''; // Initialize current room_id variable

                    foreach ($schedules as $schedule) :
                        // Format the date
                        $formattedDate = date('F j, Y', strtotime($schedule['date'])); // Example: January 1, 2024

                        if ($currentDate !== $formattedDate || $currentRoomId !== $schedule['room_name']) :
                            // Close previous tbody if not the first iteration
                            if ($currentDate !== '' && $currentRoomId !== '') :
                                echo '</tbody>';
                            endif;

                            // Update current date and room_id
                            $currentDate = $formattedDate;
                            $currentRoomId = $schedule['room_name'];

                            echo '<tbody>';
                            echo '<tr><th colspan="11">' . $currentDate . '</th></tr>'; // Output formatted date row
                            echo '<tr><td colspan="11" style="font-weight: bold;">' . $currentRoomId . '</td></tr>'; // Output room_id row
                        endif;
                    ?>
                        <tr>

                            <td><?= $schedule['date']; ?></td>
                            <td><?= date('h:i A', strtotime($schedule['start_time'])); ?></td>
                            <td><?= date('h:i A', strtotime($schedule['end_time'])); ?></td>
                            <td><?= $schedule['adviser_name']; ?></td>
                            <td><?= $schedule['student_name']; ?></td>
                            <td><?= $schedule['capstone_title']; ?></td>
                            <td><?= $schedule['lead_panelist']; ?></td>
                            <td><?= $schedule['panelist2']; ?></td>
                            <td><?= $schedule['panelist3']; ?></td>
                            <td><?= $schedule['room_name']; ?></td>
                            <td>

                            <button type="button" class="edit-btn" onclick="openEditModal(
    <?= $schedule['id']; ?>,
    '<?= $schedule['date']; ?>',
    '<?= $schedule['start_time']; ?>',
    '<?= $schedule['end_time']; ?>',
    '<?= $schedule['adviser_name']; ?>',
    '<?= $schedule['student_name']; ?>',
    '<?= $schedule['capstone_title']; ?>',
    '<?= $schedule['lead_panelist']; ?>',
    '<?= $schedule['panelist2']; ?>',
    '<?= $schedule['panelist3']; ?>',
    '<?= $schedule['room_id']; ?>'
)">Edit</button>




                                <form action="" method="POST" onsubmit="return confirmDelete();">
                                    <input type="hidden" name="id" value="<?= $schedule['id']; ?>">
                                    <button type="submit" name="Delete" class="delete-btn">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </thead>
                </tbody>
            </table>
        <?php endif; ?>
    </div>


    <style>
        .top-error {
            font-size: 15px;
            color: red;
        }
    </style>

    <?php $dateErrormsg = isset($errors['date']) ? $errors['date'] : ''; ?>
    <?php $startErrormsg = isset($errors['start_time']) ? $errors['start_time'] : ''; ?>
    <?php $endErrormsg = isset($errors['end_time']) ? $errors['end_time'] : ''; ?>
    <?php $studentErrormsg = isset($errors['student_name']) ? $errors['student_name'] : ''; ?>
    <?php $capstoneErrormsg = isset($errors['capstone_title']) ? $errors['capstone_title'] : ''; ?>
    <?php $leadPanelistError = isset($errors['panelists']) ? $errors['panelists'] : ''; ?>
    <?php $adviserNameError = isset($errors['adviser_name']) ? $errors['adviser_name'] : ''; ?>


    <div id="addModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal('addModal')">&times;</span>
            <h3>Add Schedule</h3>
            <span class="top-error" id='lead_panelist_error'><?php echo $leadPanelistError; ?></span>
            <span class="top-error" id='adviser_name_error'><?php echo $adviserNameError; ?></span>

            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" id="addForm">
                <label for="date">Date: <span class="error-message" id='date_error'><?php echo $dateErrormsg; ?></span></label>
                <input type="date" id="date" name="date">

                <label for="start_time">Start Time: <span class="error-message" id='start_error'><?php echo $startErrormsg; ?></span></label>
                <input type="time" id="start_time" name="start_time">

                <label for="end_time">End Time: <span class="error-message" id='end_error'><?php echo $endErrormsg; ?></span></label>
                <input type="time" id="end_time" name="end_time">

                <label for="adviser_name">Adviser Name:</label>
                <select name="adviser_name">
                    <option></option>
                    <?php
                    foreach ($profs as $prof) {
                        echo "<option value='{$prof['prof_id']}'>{$prof['prof_name']}</option>";
                    }
                    ?>
                </select>

                <label for="student_name">Students: <span class="error-message" id='student_error'><?php echo $studentErrormsg; ?></span></label>
                <input type="text" id="student_name" name="student_name">

                <label for="capstone_title">Capstone Title: <span class="error-message" id='capstone_error'><?php echo $capstoneErrormsg; ?></span></label>
                <input type="text" id="capstone_title" name="capstone_title">

                <label for="lead_panelist">Lead Panelist:</label>
                <select name="lead_panelist">
                    <option></option>
                    <?php
                    foreach ($profs as $prof) {
                        echo "<option value='{$prof['prof_id']}'>{$prof['prof_name']}</option>";
                    }
                    ?>
                </select>

                <label for="panelist2">Panelist 2:</label>
                <select name="panelist2">
                    <option></option>
                    <?php
                    foreach ($profs as $prof) {
                        echo "<option value='{$prof['prof_id']}'>{$prof['prof_name']}</option>";
                    }
                    ?>
                </select>

                <label for="panelist3">Panelist 3:</label>
                <select name="panelist3">
                    <option></option>
                    <?php
                    foreach ($profs as $prof) {
                        echo "<option value='{$prof['prof_id']}'>{$prof['prof_name']}</option>";
                    }
                    ?>
                </select>


                <label for="room_id">Room Name:</label>
                <select name="room_id">
                    <option></option>
                    <?php
                    foreach ($rooms as $room) {
                        echo "<option value='{$room['room_id']}'>{$room['room_name']}</option>";
                    }
                    ?>
                </select>

                <button type="submit" class="submit-btn" name="Submit">Submit</button>
            </form>
        </div>
    </div>



    <?php $eDateError = isset($errors['edit_date']) ? $errors['edit_date'] : ''; ?>
    <?php $eStartError = isset($errors['edit_start_time']) ? $errors['edit_start_time'] : ''; ?>
    <?php $eEndError = isset($errors['edit_end_time']) ? $errors['edit_end_time'] : ''; ?>
    <?php $eStudentError = isset($errors['edit_student_name']) ? $errors['edit_student_name'] : ''; ?>
    <?php $eCapstoneError = isset($errors['edit_capstone_title']) ? $errors['edit_capstone_title'] : ''; ?>
    <?php $leadPanelistError = isset($errors['edit_panelists']) ? $errors['edit_panelists'] : ''; ?>
    <?php $adviserNameError = isset($errors['edit_adviser_name']) ? $errors['edit_adviser_name'] : ''; ?>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal('editModal')">&times;</span>
            <h3>Edit Schedule</h3>
            <span class="top-error" id="panelist_error"><?= htmlspecialchars($leadPanelistError); ?></span>
            <span class="top-error" id="adviser_name"><?= htmlspecialchars($adviserNameError); ?></span>

            <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" id="editForm">
                <input type="hidden" name="edit_id" id="edit_id">

                <label for="edit_date">Date: <span class="error-message" id="edate_error"><?= htmlspecialchars($eDateError); ?></span></label>
                <input type="date" id="edit_date" name="edit_date" value="<?= htmlspecialchars($edit_date); ?>">

                <label for="edit_start_time">Start Time: <span class="error-message" id="estart_error"><?= htmlspecialchars($eStartError); ?></span></label>
                <input type="time" id="edit_start_time" name="edit_start_time">

                <label for="edit_end_time">End Time: <span class="error-message" id="eend_error"><?= htmlspecialchars($eEndError); ?></span></label>
                <input type="time" id="edit_end_time" name="edit_end_time">

                <label for="edit_adviser_name">Adviser:</label>
                <select name="edit_adviser_name" id="edit_adviser_name">
                <?php foreach ($profs as $prof) : ?>
                        <option value="<?php echo $prof['prof_id']; ?>"><?php echo $prof['prof_name']; ?></option>
                <?php endforeach; ?>

                </select>

                <label for="edit_student_name">Students: <span class="error-message" id="estudent_error"><?= htmlspecialchars($eStudentError); ?></span></label>
                <input type="text" id="edit_student_name" name="edit_student_name">

                <label for="edit_capstone_title">Capstone Title: <span class="error-message" id="ecapstone_error"><?= htmlspecialchars($eCapstoneError); ?></span></label>
                <input type="text" id="edit_capstone_title" name="edit_capstone_title">

                <label for="edit_lead_panelist">Lead Panelist:</label>
                <select name="edit_lead_panelist" id="edit_lead_panelist">

                <?php foreach ($profs as $prof) : ?>
                        <option value="<?php echo $prof['prof_id']; ?>"><?php echo $prof['prof_name']; ?></option>
                <?php endforeach; ?>

                </select>

                <label for="edit_panelist2">Panelist 2:</label>
                <select name="edit_panelist2" id="edit_panelist2">

                <?php foreach ($profs as $prof) : ?>
                        <option value="<?php echo $prof['prof_id']; ?>"><?php echo $prof['prof_name']; ?></option>
                <?php endforeach; ?>
                </select>

                <label for="edit_panelist3">Panelist 3:</label>
                <select name="edit_panelist3" id="edit_panelist3">

                <?php foreach ($profs as $prof) : ?>
                        <option value="<?php echo $prof['prof_id']; ?>"><?php echo $prof['prof_name']; ?></option>
                <?php endforeach; ?>
                </select>

                <label for="edit_room_id">Room Name:</label>
                <select name="edit_room_id" id="edit_room_id">
                    <?php
                    foreach ($rooms as $room) {
                        echo "<option value='{$room['room_id']}'>{$room['room_name']}</option>";
                    }
                    ?>
                </select>

                <button type="submit" class="submit-btn" name="Saved">Save Changes</button>
            </form>
        </div>
    </div>

    <script src="js/schedule.js"></script>
</body>

</html>