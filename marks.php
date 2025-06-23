<?php
// Include database connection
include 'DBconnect.php';
session_start();

// Sample user ID (this would normally come from the session or login)
$faculty_id = $_SESSION['user_id'];
//$user_id = '22301612';
?>
<header>
    <a href="marks_quiz.php" class="button"> Add Quiz Marks for Student <br> </a>
    <a href="marks_assi.php" class="button"> Add Assignment Marks for Student <br> </a>
    <a href="midfinalmarks.php" class="button"> Add Midterm and Final Marks for Student <br> </a>
</header>

<style>
    header {
        text-align: center;
        margin-top: 20px;
    }

    .button {
        display: block; /* Changed to block for vertical stacking */
        padding: 10px 20px;
        margin: 10px auto; /* Centered with auto margin */
        font-size: 16px;
        text-decoration: none;
        color: white;
        background-color: #FFC300; /* Updated background color */
        border-radius: 5px;
        border: none;
        cursor: pointer;
        width: 250px; /* Adjust the width as needed */
    }

    .button:hover {
        background-color: #efb100; /* Darker shade of yellow on hover */
    }
</style>

