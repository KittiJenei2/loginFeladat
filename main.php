<?php
session_start();

require_once 'login.php';

$dataBase = new User();

echo '<header><h1>Registration</h1><br>
        <form method = "post" action = "">
        <button type = "submit" id = "login" name = "login">Create profile</button>';