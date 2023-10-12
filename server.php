<?php
session_start();

// initializing variables
$Username = "";
$Email = "";
$errors = array();

// connect to the database
$db = mysqli_connect('localhost', 'root', '', 'project');

// REGISTER USER
if (isset($_POST['reg_user'])) {
  // receive all input values from the form
  $Username = mysqli_real_escape_string($db, $_POST['Username']);
  $Email = mysqli_real_escape_string($db, $_POST['Email']);
  $Password = mysqli_real_escape_string($db, $_POST['Password']);
  $Confirmpassword = mysqli_real_escape_string($db, $_POST['Confirmpassword']);

  // form validation
  if (empty($Username)) {
    array_push($errors, "Username is required");
  }
  if (empty($Email)) {
    array_push($errors, "Email is required");
  }
  if (empty($Password)) {
    array_push($errors, "Password is required");
  }
  if ($Password != $Confirmpassword) {
    array_push($errors, "The two passwords do not match");
  }

  // Check if the user already exists in the database
  $user_check_query = "SELECT * FROM users WHERE username='$Username' OR email='$Email' LIMIT 1";
  $result = mysqli_query($db, $user_check_query);
  $user = mysqli_fetch_assoc($result);

  if ($user) { // if user exists
    if ($user['username'] === $Username) {
      array_push($errors, "Username already exists");
    }
    if ($user['email'] === $Email) {
      array_push($errors, "Email already exists");
    }
  }

  // Register the user if there are no errors
  if (count($errors) == 0) {
    $password = password_hash($Password, PASSWORD_BCRYPT); // Securely hash the password

    $query = "INSERT INTO users (username, email, password) 
              VALUES('$Username', '$Email', '$password')";
    mysqli_query($db, $query);

    $_SESSION['username'] = $Username;
    $_SESSION['success'] = "You are now registered and logged in";
    header('location: index.php'); // Redirect to the index page after successful registration
  }
}
?>
