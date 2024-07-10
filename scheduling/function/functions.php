<?php

function getDatabaseConnection()
{

    $host = 'localhost';
    $dbname = 'schedule';
    $user = 'root';
    $password = '';
    $dsn = "mysql:dbname=$dbname;host=$host";

    try {
        $conn = new PDO($dsn, $user, $password);
        return $conn;
    } catch (PDOException $e) {
        echo 'Connection Failed!: ' . $e->getMessage();
    }
}



function insertProf($prof_name)
{

    $conn = getDatabaseConnection();

    $stmt = $conn->prepare("INSERT INTO professors (prof_name)
                            VALUES (:prof_name)");

    $stmt->bindParam(':prof_name', $prof_name);

    $respose = $stmt->execute();

    if ($respose) {
        return $conn->lastInsertId();
    } else {
        return FALSE;
    }
}

function updateProf($prof_id, $prof_name)
{
    $conn = getDatabaseConnection();

    $stmt = $conn->prepare("UPDATE professors
                            SET prof_name = :prof_name
                            WHERE prof_id = :prof_id");

    $stmt->bindParam(':prof_id', $prof_id);
    $stmt->bindParam(':prof_name', $prof_name);

    $respose = $stmt->execute();

    if ($respose) {
        return TRUE;
    } else {
        return FALSE;
    }
}

function deleteProf($prof_id)
{

    $conn = getDatabaseConnection();

    $stmt = $conn->prepare("DELETE FROM professors WHERE prof_id = :prof_id");
    $stmt->bindParam(':prof_id', $prof_id);
    $respose = $stmt->execute();

    if ($respose) {
        return TRUE;
    } else {
        return FALSE;
    }
}


function insertSchedules($date, $start_time, $end_time, $adviser_name, $student_name, $capstone_title, $lead_panelist, $panelist2, $panelist3, $room_id)
{
    $conn = getDatabaseConnection();


    $stmt = $conn->prepare("INSERT INTO schedules (date, start_time, end_time, adviser_name, student_name, capstone_title, lead_panelist, panelist2, panelist3, room_id)
                            VALUES (:date, :start_time, :end_time, :adviser_name, :student_name, :capstone_title, :lead_panelist, :panelist2, :panelist3, :room_id)");

    $stmt->bindParam(':date', $date);
    $stmt->bindParam(':start_time', $start_time);
    $stmt->bindParam(':end_time', $end_time);
    $stmt->bindParam(':adviser_name', $adviser_name);
    $stmt->bindParam(':student_name', $student_name);
    $stmt->bindParam(':capstone_title', $capstone_title);
    $stmt->bindParam(':lead_panelist', $lead_panelist);
    $stmt->bindParam(':panelist2', $panelist2);
    $stmt->bindParam(':panelist3', $panelist3);
    $stmt->bindParam(':room_id', $room_id);

    $response = $stmt->execute();

    if ($response) {
        return $conn->lastInsertId();
    } else {
        return FALSE;
    }
}



function appointmentExists($date, $start_time, $end_time, $lead_panelist, $panelist2, $panelist3) {
    try {
        $conn = getDatabaseConnection();
        
        // Check if lead_panelist exists in any role
        $query_lead = "SELECT * FROM schedules 
                       WHERE date = :date 
                       AND (
                           (start_time < :end_time AND end_time > :start_time) 
                           OR (start_time = :start_time AND end_time = :end_time)
                       )
                       AND (
                           lead_panelist = :lead_panelist 
                           OR panelist2 = :lead_panelist 
                           OR panelist3 = :lead_panelist
                       )";
        
        $stmt_lead = $conn->prepare($query_lead);
        $stmt_lead->bindParam(':date', $date);
        $stmt_lead->bindParam(':start_time', $start_time);
        $stmt_lead->bindParam(':end_time', $end_time);
        $stmt_lead->bindParam(':lead_panelist', $lead_panelist);
        
        $stmt_lead->execute();
        
        $result_lead = $stmt_lead->fetch(PDO::FETCH_ASSOC);
        
        // Check if panelist2 exists in any role
        $query_panelist2 = "SELECT * FROM schedules 
                            WHERE date = :date 
                            AND (
                                (start_time < :end_time AND end_time > :start_time) 
                                OR (start_time = :start_time AND end_time = :end_time)
                            )
                            AND (
                                lead_panelist = :panelist2 
                                OR panelist2 = :panelist2 
                                OR panelist3 = :panelist2
                            )";
        
        $stmt_panelist2 = $conn->prepare($query_panelist2);
        $stmt_panelist2->bindParam(':date', $date);
        $stmt_panelist2->bindParam(':start_time', $start_time);
        $stmt_panelist2->bindParam(':end_time', $end_time);
        $stmt_panelist2->bindParam(':panelist2', $panelist2);
        
        $stmt_panelist2->execute();
        
        $result_panelist2 = $stmt_panelist2->fetch(PDO::FETCH_ASSOC);
        
        // Check if panelist3 exists in any role
        $query_panelist3 = "SELECT * FROM schedules 
                            WHERE date = :date 
                            AND (
                                (start_time < :end_time AND end_time > :start_time) 
                                OR (start_time = :start_time AND end_time = :end_time)
                            )
                            AND (
                                lead_panelist = :panelist3 
                                OR panelist2 = :panelist3 
                                OR panelist3 = :panelist3
                            )";
        
        $stmt_panelist3 = $conn->prepare($query_panelist3);
        $stmt_panelist3->bindParam(':date', $date);
        $stmt_panelist3->bindParam(':start_time', $start_time);
        $stmt_panelist3->bindParam(':end_time', $end_time);
        $stmt_panelist3->bindParam(':panelist3', $panelist3);
        
        $stmt_panelist3->execute();
        
        $result_panelist3 = $stmt_panelist3->fetch(PDO::FETCH_ASSOC);
        
        // Return true if any result is found
        return ($result_lead || $result_panelist2 || $result_panelist3) ? true : false;
        
    } catch (PDOException $e) {
        // Handle database error
        error_log("Database error: " . $e->getMessage());
        return false;
    }
}








function checkScheduleConflict($date, $start_time, $end_time, $room_id)
{
    $conn = getDatabaseConnection();

    // Query to check for overlapping schedules
    $stmt = $conn->prepare("SELECT * FROM schedules WHERE date = :date AND room_id = :room_id AND (
        (:start_time < end_time AND :end_time > start_time)
    )");
    $stmt->bindParam(':date', $date);
    $stmt->bindParam(':start_time', $start_time);
    $stmt->bindParam(':end_time', $end_time);
    $stmt->bindParam(':room_id', $room_id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // If there is a result, it means there's a conflict
    return ($result !== false); // Returns true if conflict exists, false otherwise
}





function updateSchedules($id, $date, $start_time, $end_time, $adviser_name, $student_name, $capstone_title, $lead_panelist, $panelist2, $panelist3, $room_id)
{
    $conn = getDatabaseConnection();

    try {
        $stmt = $conn->prepare("UPDATE schedules
                                SET date = :date, start_time = :start_time, end_time = :end_time, adviser_name = :adviser_name, student_name = :student_name, capstone_title = :capstone_title, lead_panelist = :lead_panelist, panelist2 = :panelist2, panelist3 = :panelist3, room_id = :room_id
                                WHERE id = :id");

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':start_time', $start_time);
        $stmt->bindParam(':end_time', $end_time);
        $stmt->bindParam(':adviser_name', $adviser_name);
        $stmt->bindParam(':student_name', $student_name);
        $stmt->bindParam(':capstone_title', $capstone_title);
        $stmt->bindParam(':lead_panelist', $lead_panelist);
        $stmt->bindParam(':panelist2', $panelist2);
        $stmt->bindParam(':panelist3', $panelist3);
        $stmt->bindParam(':room_id', $room_id);

        // Execute the statement
        $stmt->execute();

        // Check if rows were affected
        if ($stmt->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    } catch (PDOException $e) {
        // Handle exceptions
        echo "Error: " . $e->getMessage();
        return false;
    }
}

function deleteSchedules($id)
{

    $conn = getDatabaseConnection();

    $stmt = $conn->prepare("DELETE FROM schedules WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $respose = $stmt->execute();

    if ($respose) {
        return TRUE;
    } else {
        return FALSE;
    }
}



function groupSchedulesByDate($schedules) {
    $grouped = [];
    foreach ($schedules as $schedule) {
        $date = $schedule['date'];
        if (!isset($grouped[$date])) {
            $grouped[$date] = [];
        }
        $grouped[$date][] = $schedule;
    }
    return $grouped;
}





function getProf() {
    $conn = getDatabaseConnection();
    $stmt = $conn->prepare("SELECT prof_id, prof_name FROM professors ORDER BY prof_name ASC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function GetSchedules()
{
    $conn = getDatabaseConnection();

    $stmt = $conn->prepare("SELECT
    s.id,
    s.date,
    s.start_time,
    s.end_time,
    p1.prof_name AS adviser_name,
    s.student_name,
    s.capstone_title,
    p2.prof_name AS lead_panelist,
    p3.prof_name AS panelist2,
    p4.prof_name AS panelist3,
    r.room_id,
    r.room_name
FROM
    schedules s
    JOIN professors p1 ON s.adviser_name = p1.prof_id
    JOIN professors p2 ON s.lead_panelist = p2.prof_id
    JOIN professors p3 ON s.panelist2 = p3.prof_id
    JOIN professors p4 ON s.panelist3 = p4.prof_id
    JOIN rooms r ON s.room_id = r.room_id");


    $stmt->execute();

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $result;
}


function GetSchedulesOrder()
{
    $conn = getDatabaseConnection();

    $stmt = $conn->prepare("
        SELECT
            s.id,
            s.date,
            s.start_time,
            s.end_time,
            p1.prof_name AS adviser_name,
            s.student_name,
            s.capstone_title,
            p2.prof_name AS lead_panelist,
            p3.prof_name AS panelist2,
            p4.prof_name AS panelist3,
            r.room_id,
            r.room_name
        FROM
            schedules s
            JOIN professors p1 ON s.adviser_name = p1.prof_id
            JOIN professors p2 ON s.lead_panelist = p2.prof_id
            JOIN professors p3 ON s.panelist2 = p3.prof_id
            JOIN professors p4 ON s.panelist3 = p4.prof_id
            JOIN rooms r ON s.room_id = r.room_id
        ORDER BY
            s.date ASC, s.start_time ASC
    ");

    $stmt->execute();

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $result;
}












function insertRoom($room_name)
{

    $conn = getDatabaseConnection();

    $stmt = $conn->prepare("INSERT INTO rooms (room_name)
                            VALUES (:room_name)");

    $stmt->bindParam(':room_name', $room_name);

    $respose = $stmt->execute();

    if ($respose) {
        return $conn->lastInsertId();
    } else {
        return FALSE;
    }
}


function updateRoom($room_id, $room_name)
{
    $conn = getDatabaseConnection();

    $stmt = $conn->prepare("UPDATE rooms
                            SET room_name = :room_name
                            WHERE room_id = :room_id");

    $stmt->bindParam(':room_id', $room_id);
    $stmt->bindParam(':room_name', $room_name);

    $respose = $stmt->execute();

    if ($respose) {
        return TRUE;
    } else {
        return FALSE;
    }
}

function deleteRoom($room_id)
{

    $conn = getDatabaseConnection();

    $stmt = $conn->prepare("DELETE FROM rooms WHERE room_id = :room_id");
    $stmt->bindParam(':room_id', $room_id);
    $respose = $stmt->execute();

    if ($respose) {
        return TRUE;
    } else {
        return FALSE;
    }
}


function GetRoom()
{

    $conn = getDatabaseConnection();

    $stmt = $conn->prepare("SELECT * FROM rooms");
    $stmt->execute();

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $result;
}


function GetProfs()
{

    $conn = getDatabaseConnection();

    $stmt = $conn->prepare("SELECT * FROM professors");
    $stmt->execute();

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $result;
}



function hasConflict($date, $start_time, $end_time, $adviser_name, $lead_panelist, $panelist2, $panelist3)
{
    $conn = getDatabaseConnection(); // Assuming this function returns a valid PDO connection

    $stmt = $conn->prepare("SELECT COUNT(*) FROM schedules 
                            WHERE date = :date 
                            AND (
                                (adviser_name = :adviser_name AND (:start_time < end_time AND :end_time > start_time))
                                OR (lead_panelist = :adviser_name AND (:start_time < end_time AND :end_time > start_time))
                                OR (panelist2 = :adviser_name AND (:start_time < end_time AND :end_time > start_time))
                                OR (panelist3 = :adviser_name AND (:start_time < end_time AND :end_time > start_time))
                            )");

    // Bind parameters
    $stmt->bindParam(':date', $date);
    $stmt->bindParam(':start_time', $start_time);
    $stmt->bindParam(':end_time', $end_time);
    $stmt->bindParam(':adviser_name', $adviser_name);

    // Execute the query
    $stmt->execute();

    // Fetch the count of conflicting schedules
    $conflictCount = $stmt->fetchColumn();

    // Return TRUE if there is a conflict, FALSE otherwise
    return ($conflictCount > 0);
}



?>