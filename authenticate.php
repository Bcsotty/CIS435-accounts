<?php
session_start();
// Change this to your connection info.
$DATABASE_HOST = '127.0.0.1';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'phplogin';
$TIMEOUT = 300;
// Try and connect using the info above.
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if ( mysqli_connect_errno() ) {
	// If there is an error with the connection, stop the script and display the error.
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}
// Now we check if the data from the login form was submitted, isset() will check if the data exists.
if ( !isset($_POST['username'], $_POST['password']) ) {
	// Could not get the data that should have been sent.
	exit('Please fill both the username and password fields!');
}
// Prepare our SQL, preparing the SQL statement will prevent SQL injection.
if ($stmt = $con->prepare('SELECT id, password, first_failed_login, failed_login_count FROM accounts WHERE username = ?')) {
	// Bind parameters (s = string, i = int, b = blob, etc), in our case the username is a string so we use "s"
	$stmt->bind_param('s', $_POST['username']);
	$stmt->execute();
	// Store the result so we can check if the account exists in the database.
	$stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $password, $first_failed_login, $failed_login_count);
        $stmt->fetch();
        if (($failed_login_count >= 3) && (time() - $first_failed_login < $TIMEOUT)) {
            $format = 'Account locked out for %d seconds.';
            echo sprintf($format, ($TIMEOUT - (time() - $first_failed_login)));
            exit;
        }
        // Account exists, now we verify the password.
        // Note: remember to use password_hash in your registration file to store the hashed passwords.
        if (password_verify($_POST['password'], $password)) {
            // Verification success! User has logged-in!
            // Create sessions, so we know the user is logged in, they basically act like cookies but remember the data on the server.
            session_regenerate_id();
            $_SESSION['loggedin'] = TRUE;
            $_SESSION['name'] = $_POST['username'];
            $_SESSION['id'] = $id;
            header('Location: home.php');
            exit;
        } else {
            if (time() - $first_failed_login > $TIMEOUT) {
                $first_failed_login = time();
                $failed_login_count = 1;
            } else {
                $failed_login_count++;
            }
            $newstmt = $con->prepare('UPDATE accounts SET first_failed_login=?,failed_login_count=? WHERE id=?');
            $newstmt->bind_param('sis', $first_failed_login, $failed_login_count, $id);
            $newstmt->execute();
            // Incorrect password
            echo 'Incorrect username and/or password!';
        }
    } else {
        // Incorrect username
        echo 'Incorrect username and/or password';
    }
	$stmt->close();
}
?>