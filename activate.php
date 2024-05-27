<?php
require_once 'login.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $user = new User();
    $mysqli = $user->getMysqli();

    if ($user->completeRegistration($token)) {
        $stmt = $mysqli->prepare("SELECT name FROM users WHERE token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo "You have successfully activated your profile, " . htmlspecialchars($row['name']) . "!";
        } else {
            echo "Invalid token!";
        }
    } else {
        echo "Succesdully activated!";
    }
} else {
    echo "Missing token!";
}
?>