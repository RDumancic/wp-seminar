<?php
require_once "connect.php";
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: index.php");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if(isset($_POST["action"])){
        $sql = "DELETE FROM revs WHERE user = ?";
        $stmtrevs = $conn->prepare($sql);
        $stmtrevs->bind_param("s", $_SESSION["username"]);
        $stmtrevs->execute();
        $stmtrevs->close();

        $sql = "DELETE FROM users WHERE username = ?";
        $stmtUser = $conn->prepare($sql);
        $stmtUser->bind_param("s", $_SESSION["username"]);
        $stmtUser->execute();
        if($stmtUser->affected_rows === 0){
            $stmtUser->close();
            exit("No user affected");
        }
        $stmtUser->close();

        session_destroy();
        header("Location: index.php");
    }
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
                <?php 
                    if(isset($_SESSION["username"])) {
                        echo "<a href='profile.php' class='nav-item nav-link'>Profile</a>";
                    }
                ?>
            </div>
            <div class="navbar-nav ml-auto"></div>
        </nav>

        <div class="container p-5">
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method="post">
                <div class="row">
                    <div class="col-xl-5 mx-auto">
                        <div class="card">
                            <div class="card-header">
                                Are you sure?
                            </div>
                            <div class="card-body">
                                <button type="submit" name="action" value="delet" class="btn btn-danger ml-3">Yes</button>
                            </div>
                            <div class="card-footer">
                                <p class="text-muted">If you are not sure, you can return using the navigation bar.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </body>
</html>