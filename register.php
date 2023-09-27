<?php 
session_start();
$data = json_decode(file_get_contents('php://input'), true);
$DATABASE_HOST = '127.0.0.1';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'phplogin';
// Try and connect using the info above.
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if ( mysqli_connect_errno() ) {
	// If there is an error with the connection, stop the script and display the error.
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}
// Now we check if the data from the login form was submitted, isset() will check if the data exists.
if (empty($data)) {
	// Could not get the data that should have been sent.
	exit('Please fill the username, password, and email fields!');
}
// Prepare our SQL, preparing the SQL statement will prevent SQL injection.
if ($stmt = $con->prepare('SELECT id FROM accounts WHERE username = ?')) {
	// Bind parameters (s = string, i = int, b = blob, etc), in our case the username is a string so we use "s"
	$stmt->bind_param('s', $data['username']);
	$stmt->execute();
	// Store the result so we can check if the account exists in the database.
	$stmt->store_result();
    if ($stmt->num_rows > 0) {
        $errors = array("errors" => "Username taken!");
        header("Content-Type: application/json");
        echo json_encode($errors);
        exit();
    }
    else {
        $username = $data['username'];
        $password = password_hash($data['password'], PASSWORD_DEFAULT);
        $email = test_input($data['email']);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors = array("errors" => "Invalid Email!");
            header("Content-Type: application/json");
            echo json_encode($errors);
            exit();
        }
        $new_stmt = $con->prepare('INSERT INTO accounts (username, password, email) VALUES (?, ?, ?)');
        $new_stmt->bind_param('sss', $username, $password, $email);
        if ($new_stmt->execute()) {
            session_regenerate_id();
            $_SESSION['loggedin'] = TRUE;
            $_SESSION['name'] = $username;
            $stmt->execute();
            $stmt->bind_result($id);
            $stmt->fetch();
            $_SESSION['id'] = $id;
            $stmt->close();

            // Insert default pfp
            $default_pfp = file_get_contents("default.png");

            if (strlen($default_pfp) === 0) {
                die("Couldn't read default image :((((");
            }

            $newer_stmt = $con->prepare('INSERT INTO icons (id, image) VALUES (?, ?)');
            $newer_stmt->bind_param("ib", $id, $default_pfp);
            $newer_stmt->execute();

            header('Location: home.php');
            exit();
        }
    }
	$stmt->close();
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>

