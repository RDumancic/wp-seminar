<?php
session_start();
if(isset($_SESSION["username"])){
    header("Location: index.php");
}
require_once "connect.php";

$error = false;
$errormsg = "";
$successmsg = "";

if(!$_SERVER["REQUEST_METHOD"] === "POST") {
    header("Location: index.php");
} else {
    $username = checkInput($_POST["username"]);
    $password = checkInput($_POST["password"]);

    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows === 0) {
        $error = true;
        $errormsg .= "Username doesn't exist";
    } else {
        $row = $result->fetch_assoc();
        $hashed = $row["password"];
        $id = $row["id"];

        if(password_verify($password, $hashed)) {
            $successmsg .= "Login successful, Returning...";
            $_SESSION["username"] = $username;
            $_SESSION["password"] = $password;
            $_SESSION["id"] = $id;
            header("refresh:1; url=index.php");
        } else {
            $error = true;
            $errormsg .= "Login failed, please check your password";
        }
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Book Handler</title>
        <link rel="stylesheet" href="//stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
    </head>

    <body style="background-color: dimgrey">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark justify-content-center mr-auto">
            <h4 class="text-white ml-3">Book Handler</h4>
            <div class="navbar-nav ml-5">
                <a href="index.php" class="nav-item nav-link">Home</a>
                <a href="reviews.php" class="nav-item nav-link">Reviews</a>
            </div>
            <div class="navbar-nav ml-auto"></div>
        </nav>

        <div class="container p-5">
            <div class="row">
                <div class="col-xl-5 mx-auto">
                    <div class="card">
                        <div class="card-header">
                            Login
                        </div>
                        <div class="card-body">
                            <?php
                                if($error) {
                                    $action = htmlspecialchars($_SERVER['PHP_SELF']);
                                    $temp = '<form action="' . $action . '" method="post">
                                            <div class="form-group">
                                                <label for="username">Username:</label>
                                                <input class="form-control" type="text" name="username" id="inputUsername">
                                            </div>
                                            <div class="form-group">
                                                <label for="password">Password:</label>
                                                <input class="form-control" type="password" name="password" id="inputPassword">
                                            </div>
                                            <button class="btn btn-primary" type="submit">Log in</button>
                                        </form>';
                                    echo $temp;
                                } else {
                                    echo "<p>Login successful!</p>";
                                }
                            ?>
                        </div>

                        <div class="card-footer">
                            <?php
                                if($error) {
                                    echo '<p class="text-danger">' . $errormsg . '</p>';
                                } else {
                                    echo '<p class="text-success">' . $successmsg . '</p>';
                                }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>