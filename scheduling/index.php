<?php
require_once('function/functions.php');

$schedules = GetSchedulesOrder();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/index.css">
    <title>Schedules</title>
</head>
<body>

<div class="navbar">
    <div class="nav-box">
        <nav>
        <h1>Capstone Scheduling System</h1>
        <div class="box">
        <a href="prof.php">Professors</a>
        <a href="rooms.php">Rooms</a>
        <a href="schedule.php">Schedules</a>
        </div>
        </nav>
    </div>
</div>

<div class="container">
    <h2>View Schedules</h2>
    <?php if (empty($schedules)) : ?>
        <p>No schedules available.</p>
    <?php else : ?>
        <button class="print-btn" onclick="window.print()">Print</button>
        
        <table>
            
           <thead>
            
            <tr>
                
                <th>Start Time</th>
                <th>End Time</th>
                <th>Adviser</th>
                <th>Students</th>
                <th>Capstone Title</th>
                <th>Lead Panelist</th>
                <th>Panelist 2</th>
                <th>Panelist 3</th>
                
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
                            echo '<tr><td colspan="11" style="font-weight: bold; color: darkblue;">Room: ' . $currentRoomId . '</td></tr>'; // Output room_id row
                        endif;
                    ?>
            <tr>
                <td><?= date('h:i A', strtotime($schedule['start_time'])); ?></td>
                <td><?= date('h:i A', strtotime($schedule['end_time'])); ?></td>
                <td><?= $schedule['adviser_name']; ?></td>
                <td><?= $schedule['student_name']; ?></td>
                <td><?= $schedule['capstone_title']; ?></td>
                <td><?= $schedule['lead_panelist']; ?></td>
                <td><?= $schedule['panelist2']; ?></td>
                <td><?= $schedule['panelist3']; ?></td>
            </tr>
            <?php endforeach; ?>
            </thead>
           </tbody>
        </table>
        <?php endif; ?>
    </div>
    <div class="hidden">
            <a href="prof.php">Professors</a>
            <a href="rooms.php">Rooms</a>
            <a href="schedule.php">Schedules</a>
        </div>
</body>
</html>