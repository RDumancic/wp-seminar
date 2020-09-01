<?php

function checkInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$server = "localhost";
$username = "root";
$password = "";
$dbname = "wp";

$conn = new mysqli($server, $username, $password, $dbname);
if(!$conn){
    die("Connection failed: " . mysqli_connect_error());
}

function showReview($ID, $title, $author, $text, $rating, $creationTime, $user, $hidden, $read, $total, $editable) {
    $text = nl2br($text);
    $ifHidden = "";
    if($hidden) {
        $ifHidden = "text-warning";
    }
    if($editable) {
        $temp = "
            <div class='card mb-2'>
                <div class='card-header {$ifHidden}'>
                    <b>{$title}</b><br>
                    {$author}
                </div>
                <div class='card-body'>
                    <p><b>{$rating} / 10</b></p>
                    <p>{$text}</p>
                </div>
                <div class='card-footer'>
                    <div class='container-fluid'>
                        <div class='row'>
                            <div class ='col-sm-8 mt-1'>
                                Created: {$creationTime}<br>
                                By: {$user}<br>
                                Read: {$read} / {$total} chapters
                            </div>
                            <div class ='col-sm-4 ml-auto'>
                                <a class='btn btn-dark text-white' href='edit.php?id={$ID}'>Edit task</a>
                                <a class='btn btn-danger' href='deleteRev.php?id={$ID}'>Delete task</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>";
    } else {
        $temp = "
            <div class='card mb-2'>
                <div class='card-header {$ifHidden}'>
                    <b>{$title}</b><br>
                    {$author}
                </div>
                <div class='card-body'>
                    <p><b>{$rating} / 10</b></p>
                    <p>{$text}</p>
                </div>
                <div class='card-footer'>
                    <div class='container-fluid'>
                        <div class='row'>
                            <div class ='col-sm-8 mt-1 mr-auto'>
                                Created: {$creationTime}<br>
                                By: {$user}<br>
                                Read: {$read} / {$total} chapters
                            </div>
                            <div class ='col-sm-4 ml-auto'>

                            </div>
                        </div>
                    </div>
                </div>
            </div>";
    } echo $temp;
}