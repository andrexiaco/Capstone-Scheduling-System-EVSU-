<?php
require_once('function/functions.php');

$schedules = GetSchedulesOrder();
$profs = GetProf();
$rooms = GetRoom();

session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Saved'])) {
    $id = $_POST['edit_id'];
    $date = $_POST['edit_date'];
    $start_time = $_POST['edit_start_time'];
    $end_time = $_POST['edit_end_time'];
    $adviser_name = $_POST['edit_adviser_name'];
    $student_name = $_POST['edit_student_name'];
    $capstone_title = $_POST['edit_capstone_title'];
    $lead_panelist = $_POST['edit_lead_panelist'];
    $panelist2 = $_POST['edit_panelist2'];
    $panelist3 = $_POST['edit_panelist3'];
    $room_id = $_POST['edit_room_id'];

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
        $errors['adviser_name'] = 'The adviser you entered already has an appointment at the specified date, time!';
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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Schedules</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/edit.css">
</head>
<style>
    .buttons {
        justify-content: space-between;
        display: flex;
    }
</style>

<body>

    <div class="container">
        <div class="buttons">
            <a href="schedule.php">
                <button type="submit" class="back-button">
                    <i class="fas fa-arrow-left"></i>
                </button>
            </a>
            <a href="index.php">
                <button type="submit" class="back-button">
                    <i class="fas fa-home"></i>
                </button>
            </a>
        </div>
        <h1>Edit Schedule</h1>
        <?php if (empty($schedules)) : ?>
            <p>No schedules available</p>
        <?php else : ?>
            <table>
                <thead>

                    <?php
                    // Define default styles
                    $messageStyles = [
                        'flash_message' => ['class' => 'flash_message'],
                        'error_message' => ['class' => 'error_message'],
                        'empty_message' => ['class' => 'empty_message']
                    ];

                    // Loop through each type of message
                    foreach ($messageStyles as $messageType => $style) {
                        // Check if the message exists in the session
                        if (isset($_SESSION[$messageType])) {
                            // Echo the message with appropriate styles
                            echo '<span class="message ' . $style['class'] . '">';
                            echo $_SESSION[$messageType];
                            echo '</span>';

                            // Remove the message from the session
                            unset($_SESSION[$messageType]);
                        }

                        // Check for errors and display them with the same style if they exist
                        if ($messageType === 'flash_message' && isset($errors) && count($errors) > 0) {
                            foreach ($errors as $error) {
                                echo '<span class="message error_message">';
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
                        <th>Adviser Name</th>
                        <th>Student Name</th>
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
                            <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" id="editForm">
                                <input type="hidden" name="edit_id" id="edit_id" value="<?= $schedule['id']; ?>">
                                <td>
                                    <input type="date" name="edit_date" value="<?= $schedule['date']; ?>">
                                </td>
                                <td>
                                    <input type="time" name="edit_start_time" value="<?= $schedule['start_time']; ?>">
                                </td>
                                <td>
                                    <input type="time" name="edit_end_time" value="<?= $schedule['end_time']; ?>">
                                </td>
                                <td>
                                    <select name="edit_adviser_name" data-toggle="modal">
                                        <?php foreach ($profs as $prof) : ?>
                                            <option value="<?= $prof['prof_id']; ?>" <?= ($prof['prof_name'] == $schedule['adviser_name']) ? 'selected' : ''; ?>>
                                                <?= htmlspecialchars($prof['prof_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="edit_student_name" value="<?= $schedule['student_name']; ?>" data-toggle="modal">
                                </td>
                                <td>
                                    <input type="text" name="edit_capstone_title" value="<?= $schedule['capstone_title']; ?>" data-toggle="modal">
                                </td>
                                <td>
                                    <select name="edit_lead_panelist" data-toggle="modal">
                                        <?php foreach ($profs as $prof) : ?>
                                            <option value="<?= $prof['prof_id']; ?>" <?= ($prof['prof_name'] == $schedule['lead_panelist']) ? 'selected' : ''; ?>>
                                                <?= htmlspecialchars($prof['prof_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <select name="edit_panelist2" data-toggle="modal">
                                        <?php foreach ($profs as $prof) : ?>
                                            <option value="<?= $prof['prof_id']; ?>" <?= ($prof['prof_name'] == $schedule['panelist2']) ? 'selected' : ''; ?>>
                                                <?= htmlspecialchars($prof['prof_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <select name="edit_panelist3" data-toggle="modal">
                                        <?php foreach ($profs as $prof) : ?>
                                            <option value="<?= $prof['prof_id']; ?>" <?= ($prof['prof_name'] == $schedule['panelist3']) ? 'selected' : ''; ?>>
                                                <?= htmlspecialchars($prof['prof_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <select name="edit_room_id" data-toggle="modal">
                                        <?php foreach ($rooms as $room) : ?>
                                            <option value="<?= $room['room_id']; ?>" <?= ($room['room_name'] == $schedule['room_name']) ? 'selected' : ''; ?>>
                                                <?= htmlspecialchars($room['room_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <button type="submit" name="Saved">Save Changes</button>
                                </td>
                            </form>
                        </tr>
                    <?php endforeach; ?>
                    </thead>
                </tbody>
            </table>
        <?php endif; ?>
    </div>


    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeBtn">&times;</span>
            <div id="modalContent"></div>
            <button id="saveBtn">Ok</button>
        </div>

        <script src="js/edit.js"></script>
    </div>
</body>

</html>