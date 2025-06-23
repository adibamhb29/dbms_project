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
    <title>Assign Assignment Marks</title>
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
            background-color: #efb100;
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
        <h1>Assign Assignment Marks</h1>
        <form method="POST" action="">
            <label for="student_id">Student ID:</label>
            <input type="number" name="student_id" id="student_id" required>

            <label for="course_code">Course Code:</label>
            <input type="text" name="course_code" id="course_code" maxlength="8" required>

            <label for="section_number">Section Number:</label>
            <input type="number" name="section_number" id="section_number" required>

            <label for="assignment1">Assignment 1 Mark:</label>
            <input type="number" name="assignment1" id="assignment1" min="0" max="100">

            <label for="assignment2">Assignment 2 Mark:</label>
            <input type="number" name="assignment2" id="assignment2" min="0" max="100">

            <label for="assignment3">Assignment 3 Mark:</label>
            <input type="number" name="assignment3" id="assignment3" min="0" max="100">

            <label for="assignment4">Assignment 4 Mark:</label>
            <input type="number" name="assignment4" id="assignment4" min="0" max="100">

            <input type="submit" name="submit" value="Assign Assignment Marks">
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

        // Loop through each assignment
        for ($i = 1; $i <= 4; $i++) {
            // Check if the user provided a mark for this assignment
            if (isset($_POST["assignment$i"]) && $_POST["assignment$i"] !== "") {
                $assignment_mark = $_POST["assignment$i"];
                
                // Check if a record for the assignment already exists
                $checkAssignment = "SELECT * FROM marks_assignement WHERE ID = ? AND Course_Code = ? AND Section_Number = ? AND Assignment_No = ?";
                $stmt = $conn->prepare($checkAssignment);
                $stmt->bind_param("isii", $student_id, $course_code, $section_number, $i);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    // Update assignment mark if it already exists
                    $updateAssignment = "UPDATE marks_assignement SET Mark = ?, Assigned_by = ? WHERE ID = ? AND Course_Code = ? AND Section_Number = ? AND Assignment_No = ?";
                    $stmt2 = $conn->prepare($updateAssignment);
                    $stmt2->bind_param("iiisii", $assignment_mark, $faculty_id, $student_id, $course_code, $section_number, $i);
                    $stmt2->execute();
                } else {
                    // Insert a new assignment mark if it doesn't exist
                    $insertAssignment = "INSERT INTO marks_assignement (ID, Course_Code, Section_Number, Assignment_No, Mark, Assigned_by) VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt2 = $conn->prepare($insertAssignment);
                    $stmt2->bind_param("isiiii", $student_id, $course_code, $section_number, $i, $assignment_mark, $faculty_id);
                    $stmt2->execute();
                }
            }
        }

        echo "<p class='success'>Assignment marks entered successfully.</p>";

        $stmt->close();
        $conn->close();
    }
    ?>
</body>
</html>
