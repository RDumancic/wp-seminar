<?php
require_once "connect.php";
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: index.php");
}

$noneAvailable = false;
$numFetched = 0;

$sql = "SELECT * FROM revs WHERE user = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $_SESSION["username"]);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 0){
    $noneAvailable = true;
} else {
    while($row = $result->fetch_assoc()){
        $numFetched++;
        $IDs[] = $row["id"];
        $titles[] = checkInput($row["title"]);
        $authors[] = checkInput($row["author"]);
        $texts[] = checkInput($row["text"]);
        $ratings[] = $row["rating"];
        $creationTimes[] = $row["created"];
        $users[] = $row["user"];
        $hidden[] = $row["hidden"];
        $read[] = $row["chaptersRead"];
        $total[] = $row["chaptersTotal"];
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

    <body style="background-color: grey">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark justify-content-center">
            <h4 class="text-white ml-3">Book Handler</h4>
            <div class="navbar-nav ml-5">
                <a href="index.php" class="nav-item nav-link">Home</a>
                <a href="reviews.php" class="nav-item nav-link">Reviews</a>
                <?php 
                    if(isset($_SESSION["username"])) {
                        echo "<a href='#' class='nav-item nav-link'>Profile</a>";
                    }
                ?>
            </div>
            <div class="navbar-nav ml-auto">
                <div class="nav-item">
                    <a class='btn btn-basic bg-secondary text-white' href='logout.php'>Log Out</a>"
                </div>
            </div>
        </nav>

        <div class="container p-5">
            <div class="row">
                <div class="col-xl-9">
                    <div class="card mb-2">
                        <div class="card-header"> 
                            My tasks:
                        </div>
                        <div class="card-body">
                            <?php
                                if($noneAvailable) {
                                    echo "You currently have no reviews to show :(";
                                } ?>
                        </div>
                    </div>
                    <?php 
                        for($i=0; $i < $numFetched; $i++) {
                            showReview($IDs[$i], $titles[$i], $authors[$i], $texts[$i], $ratings[$i], $creationTimes[$i], $users[$i], $hidden[$i], $read[$i], $total[$i], true);
                        }
                    ?>
                </div>
                <div class="col-xl-3">
                    <div class="card">
                        <div class="card-header">
                            <?php 
                                echo "<p class='text-info'>{$_SESSION["username"]}</p>";
                            ?>
                        </div>
                        <div class="card-body">
                            <?php
                                echo "You currently have {$numFetched} reviews.";
                            ?>
                            <br>Your hidden reviews are marked <p class='text-warning'>like this</p>
                            <a class='btn btn-basic bg-secondary text-white' href='newRev.php'>Create a review</a>
                        </div>
                        <div class="card-footer">
                            <a class='btn btn-danger' href='deleteUser.php'>Delete Account</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>