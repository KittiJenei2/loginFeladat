<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';
class User 
{
    protected $mysqli;
    private $db;

    function __construct($host = 'localhost', $user = 'root', $password = null, $db = 'user')
    {
        $this->mysqli = mysqli_connect($host, $user, $password, $db);
        if ($this->mysqli->connect_errno)
        {
            die("Connection failed: ". mysqli_connect_error());
        }
    }

    public function getMysqli()
    {
        return $this->mysqli;
    }


    public function createTableUsers()
    {
        $this->mysqli->query('CREATE TABLE IF NOT EXISTS users(
            id INT PRIMARY KEY auto_increment,
            is_active TINYINT DEFAULT false,
            name VARCHAR(50) NOT NULL,
            email VARCHAR(25) NOT NULL UNIQUE,
            password VARCHAR(250) NOT NULL,
            token VARCHAR(100),
            token_valid_until DATETIME,
            created_at DATETIME DEFAULT NOW(),
            registered_at DATETIME,
            picture VARCHAR(50),
            deleted_at DATETIME)');
    }

    public function registerUser($name, $email, $password, $token, $tokenValid)
    {
        $stmt_check_email = $this->mysqli->prepare("SELECT * FROM users WHERE email = ?");
        $stmt_check_email->bind_param("s", $email);
        $stmt_check_email->execute();
        $result = $stmt_check_email->get_result();

        if ($result->num_rows > 0) {
            echo "Ez az e-mail cím már regisztrálva van!";
        } else {
            $hashPassword = password_hash($password, PASSWORD_DEFAULT);
    

            $stmt = $this->mysqli->prepare("INSERT INTO users (name, email, password, token, token_valid_until) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $email, $hashPassword, $token, $tokenValid);

            if ($stmt->execute()) {
                /*$subject = "Registration confirmation";
                $message = "Dear $name,\n\n Thank you for your registration! Please click on the following link to complete your registration http://localhost:84/loginFeladat?token=$token\n\nThis link will expire on     $tokenValid\n\n Best regards, \nYour application team";
                $headers = "From: jeneikitti@gmail.com";*/

                //mail($email, $subject, $message, $headers);

                echo "Sikeres regisztráció!";
                var_dump($hashPassword);
            } else {
                echo "Hiba történt a regisztráció során!";
            }
        }

    }

    public function completeRegistration($token) 
    {
        /*$stmt = $this->mysqli->prepare("UPDATE users SET is_active = 1, token = NULL, token_valid_until = NULL, registered_at = NOW() WHERE token = ? AND token_valid_until > NOW()");
        $stmt->execute([$token]);
        if ($stmt->rowCount() > 0) {
            return true;
        } else {
            return false;
        }*/
        $stmt = $this->mysqli->prepare("UPDATE users SET is_active = 1, token = NULL, token_valid_until = NULL, registered_at = NOW() WHERE token = ? AND token_valid_until > NOW()");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    public function login($email, $password) 
    {
        $stmt = $this->mysqli->prepare("SELECT * FROM Users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0){
            $user = $result->fetch_assoc();
        }

        if (password_verify($password, $user['password']) && $user['is_active'] == 1) {
            echo "Welcome, {$user['name']}!";
        } else {
            echo "Invalid email or password.";
        }
    }

    function sendEmail($email, $token, $tokenValid)
    {
        $mail = new PHPMailer(true);
        try
        {
            $mail->isSMTP();
            $mail->Host = "localhost";
            $mail->SMTPAuth = false;
            $mail->Port = 1025;

            $mail->setFrom('jenei.kitty@gmail.com', 'Jenei Kitti');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Registration confirmation';
            $mail->Body = "Dear User, <br>Thank you for you registration! Please click on the following link to complete your registration: <a href='http://localhost:84/RaktarProjekt/activate.php?token=$token'>Complete registration</a> . <br>This link will expire on $tokenValid. <br>Best regards, Kitti.";

            $mail->send();
            echo 'Email sent!';
        }catch (Exception $e){
            echo 'Email could not be sent.';
        }
    }
}

?>