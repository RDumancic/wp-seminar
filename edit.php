<?php
require_once "connect.php";
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: index.php");
}
$successmsg = "";
$errormsg = "";

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    if (isset($_GET["id"])) {
        $id = checkInput($_GET["id"]);
    } else {
        header("Location: index.php");
    }

    $sql = "SELECT * FROM revs WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        $errorInfo .= "Error";
        die();
    }
    $row = $result->fetch_assoc();
    if ($row["user"] != $_SESSION["username"]) {
        header("Location: index.php");
    }
    $hidden = $row["hidden"];
    $title = checkInput($row["title"]);
    $author = checkInput($row["author"]);
    $text = checkInput($row["text"]);
    $rating = checkInput($row["rating"]);
    $chaptersRead = checkInput($row["chaptersRead"]);
    $chaptersTotal = checkInput($row["chaptersTotal"]);
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST["id"];
    $title = checkInput($_POST["title"]);
    $author = checkInput($_POST["author"]);
    $text = checkInput($_POST["text"]);
    $rating = checkInput($_POST["rating"]);
    $chaptersRead = checkInput($_POST["chaptersRead"]);
    $chaptersTotal = checkInput($_POST["chaptersTotal"]);
    $hidden = 0;
    if (isset($_POST["hidden"])) {
        $hidden = 1;
    }

    $sql = "UPDATE revs SET title=?, author=?, text=?, rating=?, hidden=?, chaptersRead=?, chaptersTotal=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssiiiii", $title, $author, $text, $rating, $hidden, $chaptersRead, $chaptersTotal, $id);
    $stmt->execute();
    if ($stmt->affected_rows === 0) {
        $errormsg .= "Something went wrong";
        die();
    } else {
        $successmsg .= "Edit saved";
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
                <?php 
                    if(isset($_SESSION["username"])) {
                        echo "<a href='profile.php' class='nav-item nav-link'>Profile</a>";
                    }
                ?>
            </div>
            <div class="navbar-nav ml-auto"></div>
        </nav>

        <div class="container p-5">
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                <div class="row">
                    <div class="col-xl-9">
                        <div class="card">
                            <div class="card-header">
                                Edit the review:
                            </div>

                            <div class="card-body">
                                <div class="form-group">
                                    <label for="title">Book title: </label>
                                    <input class="form-control" type="text" name="title" id="title" value="<?php echo $title; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="author">Author: </label>
                                    <input class="form-control" type="text" name="author" id="author" value="<?php echo $author; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="rating">Rating: </label>
                                    <input class="form-control" type="number" min="1" max="10" name="rating" id="rating" value="<?php echo $rating; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="text">Review: </label>
                                    <textarea class="form-control" name="text" id="text" rows="10"><?php echo $text; ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="chaptersRead">Chapters read so far: </label>
                                    <input class="form-control" type="number" min="0" maxlength="5" name="chaptersRead" id="chaptersRead" value="<?php echo $chaptersRead; ?>"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="chaptersTotal">Number of chapter the book has: </label>
                                    <input class="form-control" type="number" min="1" maxlength="5" name="chaptersTotal" id="chaptersTotal" value="<?php echo $chaptersTotal; ?>"></textarea>
                                </div>                               
                            </div>

                            <div class="card-footer">
                                <div class="form-check">
                                    <label class="form-check-label" for="hidden">
                                        <input class="form-check-input" type="checkbox" name="hidden" id="hidden" <?php
                                            if($hidden == 1) { echo "checked";} ?>>Make review hidden
                                    </label>
                                </div>
                                <button type="submit" class="btn btn-primary" name="id" value="<?php echo $id; ?>" id="buttonSubmit">Save Edits</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3">
                        <div class="card">
                            <div class="card-header">
                                <p class="text-success">
                                    <?php
                                        echo $successmsg;
                                    ?>
                                </p>
                                <p class="text-danger">
                                    <?php
                                        echo $errormsg;
                                    ?>
                                </p>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">
                                    *Rating must be a number between 1 to 10. <br>
                                    *Number of chapters the book has must be at least 1. <br>
                                    *Number of read chapters cannot exceed total number of chapters that book has. <br>
                                    *Rating and the review itself aren't obligatory.
                                </p>
                            </div>
                            <div class="card-footer">
                                <p class="text-warning" id="warningText">#</p>
                            </div>                        
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </body>

    <script>
        $(document).ready(function() {
            let titleInput = document.querySelector("#title");
            let authorInput = document.querySelector("#author");
            let textInput = document.querySelector("#text");
            let readInput = document.querySelector("#chaptersRead");
            let totalInput = document.querySelector("#chaptersTotal");
            let warningText = document.querySelector("#warningText");
            let buttonSubmit = document.querySelector("#buttonSubmit");

            buttonSubmit.disabled = false;
            let checkTitle = false;
            let checkAuthor = false;
            let checkText = true;
            let checkRead = false;
            let checkTotal = false;

            titleInput.addEventListener("keyup", (e) => {
                if (titleInput.value == "") {
                    warningText.innerHTML = "Title may not be empty";
                    buttonSubmit.disabled = true;
                    checkTitle = false;
                } else if (titleInput.value.length >= 240) {
                    warningText.innerHTML = "Title can only contain 240 characters or less";
                    buttonSubmit.disabled = true;
                    checkTitle = false;
                } else {
                    warningText.innerHTML = "";
                    checkTitle = true;
                    checkSubmit();
                }
            });

            authorInput.addEventListener("keyup", (e) => {
                if (authorInput.value == "") {
                    warningText.innerHTML = "Author may not be empty";
                    buttonSubmit.disabled = true;
                    checkAuthor = false;
                } else if (authorInput.value.length >= 240) {
                    warningText.innerHTML = "Author can only contain 120 characters or less";
                    buttonSubmit.disabled = true;
                    checkAuthor = false;
                } else {
                    warningText.innerHTML = "";
                    checkAuthor = true;
                    checkSubmit();
                }
            });

            textInput.addEventListener("keyup", (e) => {
                if (textInput.value.length >= 4000) {
                    warningText.innerHTML = "Text can only contain 4000 characters or less";
                    buttonSubmit.disabled = true;
                    checktext = false;
                } else {
                    warningText.innerHTML = "";
                    checktext = true;
                    checkSubmit();
                }
            });

            readInput.addEventListener("keyup", (e) => {
                if (readInput.value == "") {
                    warningText.innerHTML = "Number of read chapters may not be empty";
                    buttonSubmit.disabled = true;
                    checkRead = false;
                } else if (parseInt(readInput.value) > parseInt(totalInput.value)) {
                    warningText.innerHTML = "Number of read chapters may not be more than total chapters";
                    buttonSubmit.disabled = true;
                    checkRead = false;
                } else {
                    warningText.innerHTML = "";
                    checkRead = true;
                    checkSubmit();
                }
            });

            totalInput.addEventListener("keyup", (e) => {
                if (totalInput.value == "") {
                    warningText.innerHTML = "Number of total chapters may not be empty";
                    buttonSubmit.disabled = true;
                    checkTotal = false;
                } else if (parseInt(totalInput.value) < parseInt(readInput.value)) {
                    warningText.innerHTML = "Number of read chapters may not be more than total chapters";
                    buttonSubmit.disabled = true;
                    checkTotal = false;
                } else {
                    warningText.innerHTML = "";
                    checkTotal = true;
                    checkSubmit();
                }
            });

            function checkSubmit() {
                if(checkTitle && checkAuthor  && checkText && checkRead && checkTotal) {
                    buttonSubmit.disabled = false;
                }
            }
        });
    </script>
</html>