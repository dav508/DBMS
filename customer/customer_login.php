<div class="box" ><!-- box Starts -->

<div class="box-header" ><!-- box-header Starts -->

<center>

<h1>Login</h1>

<p class="lead" >Already our Customer</p>


</center>

<p class="text-muted" >
Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.

</p>




</div><!-- box-header Ends -->

<form action="checkout.php" method="post" ><!--form Starts -->

<div class="form-group" ><!-- form-group Starts -->

<label>Email</label>

<input type="text" class="form-control" name="c_email" required >

</div><!-- form-group Ends -->

<div class="form-group" ><!-- form-group Starts -->

<label>Password</label>

<input type="password" class="form-control" name="c_pass" required >

<h4 align="center">

<a href="forgot_pass.php"> Forgot Password </a>

</h4>

</div><!-- form-group Ends -->

<div class="text-center" ><!-- text-center Starts -->

<button name="login" value="Login" class="btn btn-primary" >

<i class="fa fa-sign-in" ></i> Log in


</button>

</div><!-- text-center Ends -->


</form><!--form Ends -->

<center><!-- center Starts -->

<a href="customer_register.php" >

<h3>New ? Register Here</h3>

</a>


</center><!-- center Ends -->


</div><!-- box Ends -->

<?php

if (isset($_POST['login'])) {

    $customer_email = $_POST['c_email'];
    $customer_pass = $_POST['c_pass'];

    $select_customer = "SELECT * FROM customers WHERE customer_email='$customer_email' AND customer_pass='$customer_pass'";
    $run_customer = mysqli_query($con, $select_customer);

    $get_ip = getRealUserIp();
    $check_customer = mysqli_num_rows($run_customer);

    $select_cart = "SELECT * FROM cart WHERE ip_add='$get_ip'";
    $run_cart = mysqli_query($con, $select_cart);
    $check_cart = mysqli_num_rows($run_cart);

    if ($check_customer == 0) {
        $errorMessage = 'Invalid login attempt: ' . $customer_email;
        logMessage($errorMessage, 'Login Attempt'); // Log unsuccessful login attempt
        echo "<script>alert('password or email is wrong')</script>";
        exit();
    }

    if ($check_customer == 1 AND $check_cart == 0) {
        $_SESSION['customer_email'] = $customer_email;
        $successMessage = 'User logged in: ' . $customer_email;
        logMessage($successMessage, 'Login Success'); // Log successful login
        echo "<script>alert('You are Logged In')</script>";
        echo "<script>window.open('customer/my_account.php?my_orders','_self')</script>";
    } else {
        $_SESSION['customer_email'] = $customer_email;
        $successMessage = 'User logged in with cart: ' . $customer_email;
        logMessage($successMessage, 'Login Success with Cart'); // Log successful login with cart
        echo "<script>alert('You are Logged In')</script>";
        echo "<script>window.open('checkout.php','_self')</script>";
    }
}

// Function to log messages into the audit_log table
function logMessage($details, $action = 'Login Attempt') {
    global $con;

    $user_id = ''; // Set the user ID if applicable, or leave it empty
    $timestamp = date('Y-m-d H:i:s');
    
    $insert_log = "INSERT INTO audit_log (user_id, timestamp, action, details) 
                   VALUES ('$user_id', '$timestamp', '$action', '$details')";
    mysqli_query($con, $insert_log);
}

?>
