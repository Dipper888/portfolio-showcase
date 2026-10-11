<?php
session_start();

// Clear the user session to prevent access to protected pages after logout.
session_unset();
session_destroy();

header("Location: login.php");
exit();
?>
