<?php
// Include the document handlers file
include('doc_handler.php');

// Check if the user is already logged in, redirect to home page if true
if (isset($_SESSION['user_id'])) {
    header("Location: ./home.php");
    exit();
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user input
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Call the authenticateUser function
    if (authenticateUser($email, $password)) {
        // Redirect to home page
        header("Location: ./home.php");
        exit();
    } else {
        // Authentication failed
        $error_message = "Invalid email or password";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style.css">
    <title>Login</title>
</head>

<body>
    <?php
    // Display error message if authentication failed
    if (isset($error_message)) {
        echo "<p style='color: red;'>$error_message</p>";
    }
    ?>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label>Email:</label>
        <input type="text" name="email" required><br>

        <label>Password:</label>
        <input type="password" name="password" required><br>

        <input type="submit" value="Login">
    </form>

</body>

</html>
