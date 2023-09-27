<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
    header('Location: index.html');
    exit;
}
$DATABASE_HOST = '127.0.0.1';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'phplogin';
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

// Fetch users icon
$stmt2 = $con->prepare('SELECT image FROM icons WHERE id = ?');
$stmt2->bind_param('i', $_SESSION['id']);
$stmt2->bind_result($icon);
$stmt2->execute();
$stmt2->fetch();
$stmt2->close();

header("Content-type: image/png");
echo $icon;