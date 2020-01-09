<?php
/**
 * Created by PhpStorm.
 * User: EndyRoys
 * Date: 14.12.2019
 * Time: 11:01
 */

include 'func.php';

if ($_POST) {
    $login = $_POST['login'];
    $password = $_POST['password'];
    $mysqli = myConnect();
    $query = "SELECT * FROM user WHERE login='$login'";
    $result = $mysqli->query($query);
    $user = $result->fetch_all()[0];
    if (empty($user)) {
        echo json_encode(['message' => "User dosn't exist"]);
        die();
    }
    if (!validatePassworrd($password,$user[2])) {
        echo json_encode(['message' => "Incorrect password"]);
        die();
    }
    $token = bin2hex(random_bytes(32));
    $query = "UPDATE user SET token='$token' WHERE id = $user[0]";
    if($mysqli->query($query)){
        echo json_encode(['token' => $token]);
    }else {
        echo json_encode(['message' => "Error to update token"]);
    }
    $mysqli->close();
}
