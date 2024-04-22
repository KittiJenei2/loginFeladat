<?php

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

    private function createTableUsers()
    {
        $this->mysqli->query('CREATE TABLE IF NOT EXISTS users(
            id INT PRIMARY KEY auto_increment,
            is_active TINYINT DEFAULT false,
            name VARCHAR(50) NOT NULL,
            email VARCHAR(25) NOT NULL UNIQUE,
            password VARCHAR(50) NOT NULL,
            token VARCHAR(100)
            token_valid_until DATETIME,
            created_at DATETIME DEFAULT NOW(),
            registered_at DATETIME,
            picture VARCHAR(50),
            deleted_at DATETIME)');
    }

    private function registerUser($name, $email, $password)
    {
        $hashPassword = password_hash($password, PASSWORD_DEFAULT);
        $token = bin2hex(random_bytes(32));
        $tokenValid = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        $stmt = $this->db->prepare("INSERT INTO users (name, email, password, token, token_valid_until) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $hashPassword, $token, $tokenValid]);

        $subject = "Registration confirmation";
        $message = "Dear $name,\n\n Thank you for your registration! Please click on the following link to complete your registration http://localhost:84/loginFeladat?token=$token\n\nThis link will expire on $tokenValid\n\n Best regards, \nYour application team";
        $headers = "From: jeneikitti@gmail.com";

        mail($email, $subject, $message, $headers);
    }

    public function completeRegistration($token) 
    {
        $stmt = $this->db->prepare("UPDATE users SET is_active = 1, token = NULL, token_valid_until = NULL, registered_at = NOW() WHERE token = ? AND token_valid_until > NOW()");
        $stmt->execute([$token]);
        if ($stmt->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function login($email, $password) 
    {
        $stmt = $this->db->prepare("SELECT * FROM Users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password']) && $user['is_active'] == 1) {
            echo "Welcome, {$user['name']}!";
        } else {
            echo "Invalid email or password.";
        }
    }
}

?>