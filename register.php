<?php
session_start();
if (isset($_SESSION["username"])) {
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
            </div>
            <div class="navbar-nav ml-auto"></div>
        </nav>

        <div class="container p-5">
            <div class="row">
                <div class="col-xl-7 mx-auto">
                    <div class="card">
                        <div class="card-header">
                            Register
                        </div>
                        <div class="card-body">
                            <form action="registerHandler.php" method="post">
                                <div class="form-group">
                                    <label for="username">Username:</label>
                                    <input class="form-control" type="text" name="username" id="usernameReg">
                                    <p class="text-muted">*Username must contain between 8 to 20 characters and can only contain letters, numbers and underscores.</p>
                                </div>
                                <div class="form-group">
                                    <label for="password">Password:</label>
                                    <input class="form-control" type="password" name="password" id="passwordReg">
                                    <p class="text-muted">*Password has to be atleast 8 characters long.</p>
                                </div>
                                <button class="btn btn-secondary" type="submit" id="buttonSubmit">Register</button>
                            </form>
                        </div>

                        <div class="card-footer">
                            <p class="text-warning" id="errormsg"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>

    <script>
        $(document).ready(function() {
        let usnm = document.querySelector("#usernameReg");
        let psw = document.querySelector("#passwordReg");
        let errormsg = document.querySelector("#errormsg");
        let regBtn = document.querySelector("#buttonSubmit");

        let usnmSet = false;
        let passwordSet = false;

        regBtn.disabled = true;

        usnm.addEventListener("keyup", (e) => {
            var illegalChars = /\W/;
            if (usnm.value == "") {
                errormsg.innerHTML = "Username cannot be left empty!";
                usnmSet = false;
                regBtn.disabled = true;
            } else if ((usnm.value.length < 8) || (usnm.value.length >= 20)) {
                errormsg.innerHTML = "Breaking character limit";
                usnmSet = false;
                regBtn.disabled = true;
            } else if (illegalChars.test(usnm.value)) {
                errormsg.innerHTML = "Contains unsupported characters";
                usnmSet = false;
                regBtn.disabled = true;
            } else {
                errormsg.innerHTML = "";
                usnmSet = true;
                checkInputs(usnmSet, passwordSet);
            }
        });

        psw.addEventListener("keyup", (e) => {
            if(psw.value == ""){
                errormsg.innerHTML = "Password cannot be left empty!";
                passwordSet = false;
                regBtn.disabled = true;
            } else if((psw.value.length < 8) || (psw.value.length > 50)){
                errormsg.innerHTML = "Breaking character limit";
                passwordSet = false;
                regBtn.disabled = true;
            } else {
                errormsg.innerHTML = "";
                passwordSet = true;
                checkInputs(usnmSet, passwordSet);
            }
        });

        function checkInputs (userSet, passSet){
            if(userSet && passSet){
                regBtn.disabled = false;
            } else {
                regBtn.disabled = true;
            }
        }
    });
    </script>
</html>