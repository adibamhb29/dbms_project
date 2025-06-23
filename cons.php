<?php
// Include database connection
include 'DBconnect.php';
session_start();

// Sample user ID (this would normally come from the session or login)
$student_id = $_SESSION['user_id'];
//$user_id = '22301612';
?>
<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Consultation Booking</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 900px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        button {
            padding: 10px 15px;
            background-color: #FFC300;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
        .search-bar {
            margin-bottom: 20px;
            text-align: center;
        }
        .search-bar input[type="text"] {
            padding: 10px;
            width: 300px;
            font-size: 16px;
        }
        .search-bar button {
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Book Faculty Consultation</h1>
        <div class="search-bar">
            <form action="cons.php" method="GET">
                <input type="text" name="search" placeholder="Search by Faculty Name or Initial" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                <button type="submit">Search</button>
            </form>
        </div>
        <?php
        //$conn = new mysqli("localhost", "root", "", "_project");
        //if ($conn->connect_error) {
        //    die("Connection failed: " . $conn->connect_error);
        //}

        // Check if booking action was performed
        if (isset($_POST['book'])) {
            $consultation_id = $_POST['book'];
            $sql = "UPDATE f_consultation SET is_booked = 1 WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $consultation_id);
            if ($stmt->execute()) {
                echo "<script>alert('Consultation successfully booked!');</script>";
            } else {
                echo "<script>alert('Failed to book consultation. Please try again.');</script>";
            }
            $stmt->close();
        }

        // Check if unbooking action was performed
        if (isset($_POST['unbook'])) {
            $consultation_id = $_POST['unbook'];
            $sql = "UPDATE f_consultation SET is_booked = 0 WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $consultation_id);
            if ($stmt->execute()) {
                echo "<script>alert('Consultation successfully unbooked!');</script>";
            } else {
                echo "<script>alert('Failed to unbook consultation. Please try again.');</script>";
            }
            $stmt->close();
        }

        // Handle search query
        $search = isset($_GET['search']) ? $_GET['search'] : '';
        $search_query = "";
        if (!empty($search)) {
            $search_query = "WHERE u.f_name LIKE '%$search%' OR u.l_name LIKE '%$search%' OR f.initial LIKE '%$search%'";
        }

        // Adjusted SQL query to include search filter
        $sql = "
        SELECT c.id AS consultation_id, c.day, c.time, c.is_booked, 
               u.id AS faculty_id, u.f_name, u.l_name, u.mail, 
               f.initial, f.room, up.phone 
        FROM f_consultation c
        JOIN faculty f ON c.id = f.id  
        JOIN user u ON u.id = f.id
        LEFT JOIN user_phone up ON u.id = up.id
        $search_query
        ";

        // Execute query and check for errors
        $result = $conn->query($sql);
        if (!$result) {
            die("Query failed: " . $conn->error);
        }
        ?>
        <form action="cons.php" method="POST">
            <table>
                <thead>
                    <tr>
                        
                        <th>Faculty Name</th>
                        <th>Initial</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Room</th>
                        <th>Day</th>
                        <th>Time</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $faculty_id = $row['faculty_id'];
                            $name = $row['f_name'] . " " . $row['l_name'];
                            $initial = $row['initial'];
                            $email = $row['mail'];
                            $phone = $row['phone'] ? $row['phone'] : 'N/A';
                            $room = $row['room'];
                            $day = isset($row['day']) ? $row['day'] : 'N/A';
                            $time = isset($row['time']) ? $row['time'] : 'N/A';
                            $is_booked = isset($row['is_booked']) ? $row['is_booked'] : 0;

                            echo "<tr>
                                
                                <td>{$name}</td>
                                <td>{$initial}</td>
                                <td>{$email}</td>
                                <td>{$phone}</td>
                                <td>{$room}</td>
                                <td>{$day}</td>
                                <td>{$time}</td>
                                <td>";
                            if ($is_booked == 1) {
                                echo "<button type='submit' name='unbook' value='{$row['consultation_id']}'>Unbook</button>";
                            } else {
                                echo "<button type='submit' name='book' value='{$row['consultation_id']}'>Book</button>";
                            }
                            echo "</td>
                              </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='9'>No consultations available</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </form>
    </div>
    
    <?php
    $conn->close();
    ?>
</body>
</html>