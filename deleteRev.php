<?php
require_once "connect.php";
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: index.php");
}
$successmsg = "";
$review = null;

if($_SERVER["REQUEST_METHOD"] === "POST"){
    $id = checkInput($_POST["id"]);
    $sql = "SELECT * FROM revs WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        exit("No such task found");
    } else {
        $review = $result->fetch_assoc();
        $stmt->close();
        if ($_SESSION["username"] != $review["user"]) {
            header("Location: index.php");
        } else {
            $sql = "DELETE FROM revs WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            if($stmt->affected_rows === 0){
                $stmt->close();
                exit("Error");
            } else {
                $stmt->close();
                $successmsg .= "Review deleted, Returning...";
                header("refresh:1; url=profile.php");
            }
        }
    }
} else if($_SERVER["REQUEST_METHOD"] === "GET"){
    $id = checkInput($_GET["id"]);
    $sql = "SELECT * FROM revs WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        exit("No review found");
    } else {
        $review = $result->fetch_assoc();
        $stmt->close();
        if ($_SESSION["username"] != $review["user"]) {
            header("Location: index.php");
        }
    }
} else {
    header("Location: index.php");
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
                                <?php
                                    if($_SERVER["REQUEST_METHOD"] === "GET") {
                                        $isHidden = "No";
                                        if($review["hidden"]) {
                                            $isHidden = "Yes";
                                        }
                                        $temp = "
                                            {$review["title"]} by {$review["author"]} <br>
                                            Created: {$review["created"]} <br>
                                            Hidden: {$isHidden} <br>
                                            <button type='submit' name='id' value='{$review["id"]}' class='btn btn-danger ml-3'>Yes</button>";
                                        echo $temp; 
                                    } else {
                                        echo "<div class='card'><p class='text-success'>{$successmsg}</p></div>";
                                    }
                                ?>
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