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

// Parse form data
$new_usr = $_POST['username'];
$new_pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

// Prep update query
$prep = $con->prepare("update phplogin.accounts
set accounts.username=?,
    accounts.password=?
where accounts.id=?;");
$prep->bind_param('ssi', $new_usr, $new_pass, $_SESSION['id']);
$prep->execute();
$prep->close();

// Update icon if sent
if (isset($_FILES['img'])) {
    $path = $_FILES['img']['tmp_name'];
    $image = base64_encode(file_get_contents($path));

    $prep2 = $con->prepare("update phplogin.icons
set image=?
where id=?;");
    $prep2->bind_param("is", $_SESSION['id'], $image);
    $prep2->execute();
}

// Force user to log back in
session_destroy();
header("location: home.php");