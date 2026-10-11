<?php
session_start();

// Redirect users based on their login session and role.
if (isset($_SESSION["user_id"]) && isset($_SESSION["role"]))
{
    if ($_SESSION["role"] == "admin")
    {
        header("Location: admin/dashboard.php");
        exit();
    }
    elseif ($_SESSION["role"] == "student")
    {
        header("Location: student/dashboard.php");
        exit();
    }
}

header("Location: login.php");
exit();
?>