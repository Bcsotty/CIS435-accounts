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
// We don't have the password or email info stored in sessions so instead we can get the results from the database.
$stmt = $con->prepare('SELECT password, email, admin FROM accounts WHERE id = ?');
// In this case we can use the account ID to get the account info.
$stmt->bind_param('i', $_SESSION['id']);
$stmt->execute();
$stmt->bind_result($password, $email, $admin);
$stmt->fetch();
$stmt->close();
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Profile Page</title>
		<link href="style.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
	</head>
	<body class="loggedin">
		<nav class="navtop">
			<div>
				<h1>Website Title</h1>
				<?php
					if ($admin == 1) {
						echo '<a href="admin.php"><i class="fas fa-eye"></i>Admin Panel</a>';
					}
				?>
				<a href="profile.php"><i class="fas fa-user-circle"></i>Profile</a>
				<a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
			</div>
		</nav>
		<div class="content">
			<h2>Profile Page</h2>
			<div>
				<p>Your account details are below:</p>
                <p>Profile Picture:</p>
                <img src="get_icon.php?id=<?=$_SESSION['id']?>" alt="pfp" style="object-fit: cover; border-radius: 50%; height: 100px; width: 100px;">
				<form action="update.php" method="post" enctype="multipart/form-data">
                    <label for="username">Username:</label>
                    <input type="text" value="<?=$_SESSION['name']?>" name="username" id="username" minlength="1" required>
                    <br>
                    <label for="password">Password:</label>
                    <input type="password" value="<?=$password?>" name="password" id="password" minlength="1" required>
                    <br>
                    <label for="email">Email:</label>
                    <input type="email" value="<?=$email?>" name="email" id="email" readonly>
                    <br>
                    <label for="img">New Icon:</label>
                    <input type="file" name="img" id="img">
                    <br>
                    <input type="submit" value="submit new info" name="btn">
				</form>
			</div>
		</div>
	</body>
</html>