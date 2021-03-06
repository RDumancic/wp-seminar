<?php
session_start();
if(isset($_SESSION["username"])){
    header("Location: index.php");
}
require_once "connect.php";
$errorMsg = "";
$regMsg = "";
if(!$_SERVER["REQUEST_METHOD"] === "POST"){
    header("Location: index.php");
} else {
    $username = checkInput($_POST["username"]);
    $password = checkInput($_POST["password"]);

    $sql = "SELECT username FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // check if username exists
    if($result->num_rows > 0){
        $errorMsg .= "Username is already in use, returning in 3 seconds";
        header( "refresh:3; url=register.php" );
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username, password) VALUES (?, ?);";
        $stmtReg = $conn->prepare($sql);
        $stmtReg->bind_param("ss", $username, $hash);
        $stmtReg->execute();
        if($stmtReg->affected_rows === 0){
            exit("Error");
        }
        $stmtReg->close();
        $regMsg .= "Register completed, returning...";
        header( "refresh:1; url=index.php" );
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
                            
                        </div>
                        <div class="card-body">
                            <p>
                                <?php echo $regMsg; ?>                            
                            </p>
                            <p class="text-danger">
                                <?php echo $errorMsg; ?>       
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>