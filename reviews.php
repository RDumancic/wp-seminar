<?php
require_once "connect.php";
session_start();

$noneAvailable = false;
$maxToShow = 15;
$numFetched = 0;

$sql = "SELECT * FROM revs WHERE hidden='0' LIMIT ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $maxToShow);
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
                <a href="#" class="nav-item nav-link">Reviews</a>
                <?php 
                    if(isset($_SESSION["username"])) {
                        echo "<a href='profile.php' class='nav-item nav-link'>Profile</a>";
                    }
                ?>
            </div>
            <div class="navbar-nav ml-auto">
                <div class="nav-item">
                    <?php
                        if(isset($_SESSION["username"])) {
                            echo "
                                <a class='btn btn-basic bg-secondary text-white' href='logout.php'>Log Out</a>";
                        } else {
                            echo "
                                <form class='form-inline' action='login.php' method='post'>
                                    <div class='form-group'>
                                        <label class='text-white pr-2' for='username'>Username</label>
                                        <input class='form-control bg-secondary text-white' type='text' name='username' id='username'>
                                    </div>
                                    <div class='form-group'>
                                        <label class='text-white px-2' for='password'>Password</label>
                                        <input class='form-control bg-secondary' type='password' name='password' id='password'>
                                    </div>
                                    <button class='btn btn-basic bg-secondary text-white ml-3' type='submit'>Login</button>
                                    <a class='btn btn-basic bg-secondary text-white ml-3' href='register.php'>Register</a>
                                </form>";
                        } ?>
                </div>
            </div>
        </nav>

        <div class="container p-5">
            <div class="row">
                <div class="col-xl-9">
                    <div class="card">
                        <div class="card-header">
                            Public reviews
                        </div>
                        <div class="card-body">
                            <?php
                                if($noneAvailable) {
                                    echo "There are currently no public reviews to show :(";
                                } else {
                                    for($i=0; $i < $numFetched; $i++) {
                                        showReview($IDs[$i], $titles[$i], $authors[$i], $texts[$i], $ratings[$i], $creationTimes[$i], $users[$i], $hidden[$i], $read[$i], $total[$i], false);
                                    }
                                }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <?php
                                if(isset($_SESSION["username"])) {
                                    echo "
                                        You are logged in as: <p class='text-info'>{$_SESSION["username"]}</p>
                                        View your profile <a href='profile.php'>here</a>";
                                } else {
                                    echo "
                                        You aren't logged in! <br>
                                        You need to login to post your own Reviews. <br>
                                        Don't have an account? <br>
                                        <a href='register.php'>Make one now!</a>";
                                } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>