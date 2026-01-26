<?php
require_once("settings.php");


$conn = @mysqli_connect($host, $user, $pwd, $sql_db);

if (!$conn) {
    die("Database connection failure");
}


mysqli_close($conn);
?>
