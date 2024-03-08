<?php
session_start();

// initializing variables
$name = "";
$username = "";
$usn = "";
$email    = "";
$errors = array();
$reg_date = date("Y/m/d");

// connect to the database
$db = mysqli_connect('localhost', 'root', '', 'shop');

// REGISTER USER
if (isset($_POST['reg_user'])) {
    // receive all input values from the form
    $username = mysqli_real_escape_string($db, $_POST['admin_name']);
    $email = mysqli_real_escape_string($db, $_POST['admin_email']);
    $password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
    $password_2 = mysqli_real_escape_string($db, $_POST['password_2']);

    // form validation: ensure that the form is correctly filled ...
    // by adding (array_push()) corresponding error unto $errors array
    if (empty($username)) { array_push($errors, "Username is required"); }
    if (empty($email)) { array_push($errors, "Email is required"); }
    if (empty($password_1)) { array_push($errors, "Password is required"); }
    if ($password_1 != $password_2) {
        array_push($errors, "The passwords do not match");
    }

    // first check the database to make sure
    // a user does not already exist with the same username and/or email
    $user_check_query = "SELECT * FROM admin_info WHERE admin_name='$username' OR admin_email='$email' LIMIT 1";
    $result = mysqli_query($db, $user_check_query);
    $user = mysqli_fetch_assoc($result);

    if ($user) { // if user exists
        if ($user['admin_name'] === $username) {
            array_push($errors, "Username already exists");
        }

        if ($user['admin_email'] === $email) {
            array_push($errors, "Email already exists");
        }
    }

    // Finally, register user if there are no errors in the form
    if (count($errors) == 0) {
        $hashed_password = password_hash($password_1, PASSWORD_DEFAULT);//encrypt the password before saving in the database

        $query = "INSERT INTO admin_info (admin_name, admin_email, admin_password)
                  VALUES('$username', '$email', '$hashed_password')";
        mysqli_query($db, $query);
        $_SESSION['admin_name'] = $username;
        $_SESSION['admin_email'] = $email;

        $_SESSION['success'] = "You are now logged in";
        header('location: ./admin/');
    }
}

// LOGIN USER
if (isset($_POST['login_admin'])) {
    $admin_username = mysqli_real_escape_string($db, $_POST['admin_username']);
    $password = mysqli_real_escape_string($db, $_POST['password']);

    if (empty($admin_username)) {
        array_push($errors, "Username is required");
    }
    if (empty($password)) {
        array_push($errors, "Password is required");
    }

    if (count($errors) == 0) {
        $query = "SELECT * FROM admin_info WHERE admin_email='$admin_username'";
        $results = mysqli_query($db, $query);
        if ($results && mysqli_num_rows($results) == 1) {
            $user = mysqli_fetch_assoc($results);
            if (password_verify($password, $user['admin_password'])) {
                $_SESSION['admin_email'] = $user['admin_email'];
                $_SESSION['admin_name'] = $admin_username;
                $_SESSION['success'] = "You are now logged in";
                header('location: ./admin/');
            } else {
                array_push($errors, "Wrong password");
            }
        } else {
            array_push($errors, "User not found");
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- ... (head section remains the same) ... -->
</head>
<body>

    <div class="main" style="padding-top: 90px;">

        <!-- Sign up form -->
        <section class="signup">
            <div class="container">
                <div class="signup-content">
                    <!-- ... (your HTML form content remains the same) ... -->
                </div>
            </div>
        </section>

    </div>

    <!-- JS -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>
