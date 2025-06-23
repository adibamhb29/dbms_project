<?php
// Include database connection
include 'DBconnect.php';
session_start();

// Sample user ID (this would normally come from the session or login)
$faculty_id = $_SESSION['user_id'];
//$user_id = '22301612';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Midterm and Final Marks</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 400px;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="number"] {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #FFC300;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #FFC300;
        }

        .success {
            color: green;
            text-align: center;
        }

        .error {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Assign Midterm and Final Marks</h1>
        <form method="POST" action="">
            <label for="student_id">Student ID:</label>
            <input type="number" name="student_id" id="student_id" required>

            <label for="course_code">Course Code:</label>
            <input type="text" name="course_code" id="course_code" maxlength="8" required>

            <label for="section_number">Section Number:</label>
            <input type="number" name="section_number" id="section_number" required>

            <label for="mid_mark">Mid Mark:</label>
            <input type="number" name="mid_mark" id="mid_mark" min="0" max="100">

            <label for="final_mark">Final Mark:</label>
            <input type="number" name="final_mark" id="final_mark" min="0" max="100">


            <input type="submit" name="submit" value="Assign Marks">
        </form>
    </div>
    <?php
if (isset($_POST['submit'])) {
    // Example faculty ID
    //$conn = new mysqli('localhost', 'root', '', '_project');
    //if ($conn->connect_error) {
    //    die("Connection failed: " . $conn->connect_error);
    //}
    $faculty_id = $_SESSION['user_id'];
    $student_id = $_POST['student_id'];
    $course_code = $_POST['course_code'];
    $section_number = $_POST['section_number'];
    
    // Check if mid_mark or final_mark is empty and assign 0 if it is
    $mid_mark = !empty($_POST['mid_mark']) ? $_POST['mid_mark'] : 0;
    $final_mark = !empty($_POST['final_mark']) ? $_POST['final_mark'] : 0;

    $checkEnrollment = "SELECT * FROM enrolled_courses WHERE ID = ? AND Course_Code = ? AND Section_Number = ?";
    $stmt = $conn->prepare($checkEnrollment);
    $stmt->bind_param("isi", $student_id, $course_code, $section_number);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $checkMarks = "SELECT * FROM marks WHERE ID = ? AND Course_Code = ? AND Section_Number = ?";
        $stmt2 = $conn->prepare($checkMarks);
        $stmt2->bind_param("isi", $student_id, $course_code, $section_number);
        $stmt2->execute();
        $result2 = $stmt2->get_result();
        
        if ($result2->num_rows > 0) {
            // Update marks if record already exists
            $updateQuery = "UPDATE marks SET Mid_Mark = ?, Final_Mark = ? WHERE ID = ? AND Course_Code = ? AND Section_Number = ?";
            $stmt3 = $conn->prepare($updateQuery);
            $stmt3->bind_param("iiisi", $mid_mark, $final_mark, $student_id, $course_code, $section_number);
            if ($stmt3->execute()) {
                echo "<p class='success'>Marks updated successfully.</p>";
            } else {
                echo "<p class='error'>Failed to update marks.</p>";
            }
        } else {
            // Insert marks if record does not exist
            $insertQuery = "INSERT INTO marks (ID, Course_Code, Section_Number, Assigned_by, Mid_Mark, Final_Mark) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt3 = $conn->prepare($insertQuery);
            $stmt3->bind_param("isiiii", $student_id, $course_code, $section_number, $faculty_id, $mid_mark, $final_mark);
            if ($stmt3->execute()) {
                echo "<p class='success'>Marks assigned successfully.</p>";
            } else {
                echo "<p class='error'>Failed to assign marks.</p>";
            }
        }
    } else {
        echo "<p class='error'>Student is not enrolled in the course/section.</p>";
    }

    $stmt->close();
    $conn->close();
}
?>
