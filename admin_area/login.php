<?php

session_start();

// Set the password to access the page
$page_password = "database_security2023";

if (isset($_POST['page_password'])) {
    // Check if the entered password is correct
    $entered_password = $_POST['page_password'];
    if ($entered_password == $page_password) {
        // Password is correct, set a session variable to indicate authentication
        $_SESSION['authenticated'] = true;
    } else {
        // Password is incorrect, display an error message
        echo "<script>alert('Incorrect password. Please try again.')</script>";
    }
}

// Check if the user is authenticated before displaying the login form
if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
    include("includes/db.php");

    // Maximum number of attempts before lockout
    $max_attempts = 3;

    // Lockout time in seconds
    $lockout_time = 300; // 5 minutes

    // Get IP address
    $ip_address = $_SERVER['REMOTE_ADDR'];

    // Initialize attempts_left
    $attempts_left = -1;

    // Check if there is an attempt count for the current user
    if (isset($_POST['admin_login'])) {
        $admin_email = mysqli_real_escape_string($con, $_POST['admin_email']);
        $admin_pass = mysqli_real_escape_string($con, $_POST['admin_pass']);

        // Check if email and password are correct
        $get_admin = "SELECT * FROM admins WHERE admin_email='$admin_email' AND admin_pass='$admin_pass'";
        $run_admin = mysqli_query($con, $get_admin);
        $count = mysqli_num_rows($run_admin);

        // Get number of login attempts in the last lockout time
        $time = time() - $lockout_time;
        $query = "SELECT COUNT(*) AS count FROM login_logs WHERE admin_email='$admin_email' AND login_status='Failure' AND login_time > FROM_UNIXTIME($time)";
        $result = mysqli_query($con, $query);
        $row = mysqli_fetch_assoc($result);
        $attempts = $row['count'];

        // Check if account is locked out
        if ($attempts >= $max_attempts) {
            $remaining_time = $lockout_time + $time - time();
            echo "<script>alert('Too many failed login attempts. Please try again in $remaining_time seconds.')</script>";
            $attempts_left = 0;
        } else {
            if ($count == 1) {
                // Successful login
                $_SESSION['admin_email'] = $admin_email;
                echo "<script>alert('You are Logged in into admin panel')</script>";
                echo "<script>window.open('index.php?dashboard','_self')</script>";
            } else {
                // Insert login attempt into the login_logs table
                $insert_log = "INSERT INTO login_logs (admin_email, login_time, login_status) VALUES ('$admin_email', NOW(), 'Failure')";
                mysqli_query($con, $insert_log);

                // Check if account should be locked out
                if ($attempts + 1 >= $max_attempts) {
                    $remaining_time = $lockout_time;
                    echo "<script>alert('Too many failed login attempts. Account locked. Please try again in $remaining_time seconds.')</script>";
                    $attempts_left = 0;
                } else {
                    // Calculate remaining attempts
                    $attempts_left = $max_attempts - $attempts - 1;
                    echo "<script>alert('Email or Password is Wrong. $attempts_left attempts remaining.')</script>";
                }
            }
        }
    }
    ?>

    <!DOCTYPE HTML>
    <html>

    <head>

        <title>Admin Login</title>

        <link rel="stylesheet" href="css/bootstrap.min.css">

        <link rel="stylesheet" href="css/login.css">

    </head>

    <body>

    <div class="container"><!-- container Starts -->

        <form class="form-login" action="" method="post"><!-- form-login Starts -->

            <h2 class="form-login-heading">Admin Login</h2>

            <input type="text" class="form-control" name="admin_email" id="admin_email" placeholder="Email Address">
            <input type="password" class="form-control" name="admin_pass" id="admin_pass" placeholder="Password">

            <button class="btn btn-lg btn-primary btn-block" type="submit" name="admin_login">

                Log in

            </button>

            <?php
            if ($attempts_left >= 0) {
                if ($attempts_left > 0) {
                    echo "<p>Attempts left: $attempts_left</p>";
                } elseif (isset($remaining_time) && $remaining_time > 0) {
                    echo "<p>Account locked. Try again in $remaining_time seconds.</p>";
                }
            }
            ?>

        </form><!-- form-login Ends -->

    </div><!-- container Ends -->

    <script>
    // Disable text boxes when the attempts reach zero
    <?php if ($attempts_left >= 0 && $attempts_left <= 0) { ?>
    document.getElementById("admin_email").disabled = true;
    document.getElementById("admin_pass").disabled = true;
    <?php } ?>
</script>



    </body>

    </html>

    <?php

} else {
    // If not authenticated, display password entry form
    ?>

    <!DOCTYPE HTML>
    <html>

    <head>

        <title>Enter Password</title>

    </head>

    <body>

    <div>

        <form method="post" action="">
            <label for="page_password">Enter Password to Access:</label>
            <input type="password" id="page_password" name="page_password" required>
            <button type="submit">Submit</button>
        </form>

    </div>

    </body>

    </html>

    <?php
}

?>
