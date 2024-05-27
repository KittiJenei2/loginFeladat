<?php

require_once 'login.php';

$dataBase = new User();

/*echo '<form method = "post" action = "">
        <button type = "submit" id = "createTab" name = "createTab">Create Table</button>';*/

echo '<header><h1>Registration</h1><br>
        <form method = "post" action = "">
        <button type = "submit" id = "login" name = "login">Create profile</button>
        </form>';

echo '<form method = "post" action = "raktarIndex.php">
        <button type = "submit" name = "homeButt">Back to starter page</button>
        </header>';

if(isset($_POST['createTab']))
{
    $dataBase->createTableUsers();
}

if (isset($_POST['login'])) 
{
    echo '<form method="post" action="">
            <label for="name">Name:</label><br>
            <input type="text" id="name" name="name" required><br><br>
            
            <label for="email">Email:</label><br>
            <input type="email" id="email" name="email" required><br><br>
            
            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" required><br><br>
            
            <label for="confirm_password">Confirm Password:</label><br>
            <input type="password" id="confirm_password" name="confirm_password" required><br><br>
            
            <button type="submit" id="register" name="register">Register</button>
          </form>';
}

if (isset($_POST['register'])) {
    if (isset($_POST['name'], $_POST['email'], $_POST['password'], $_POST['confirm_password'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        if ($password === $confirm_password) {
            $token = bin2hex(random_bytes(32));
            $tokenValid = date('Y-m-d H:i:s', strtotime('+10 minutes'));
            $dataBase->registerUser($name, $email, $password, $token, $tokenValid);

            $dataBase->sendEmail($email, $token, $tokenValid);
        } else {
            echo "The passwords are not the same!";
        }
    } else {
        echo "Missing data!";
    }

    
}