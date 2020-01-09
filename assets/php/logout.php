<?php
/**
 * Created by PhpStorm.
 * User: EndyRoys
 * Date: 16.12.2019
 * Time: 17:20
 */

include 'func.php';

if (checkAuthToken()) {
    $mysqli = myConnect();
    $query = "UPDATE user SET token='' WHERE token = '$token'";
    if($mysqli->query($query)){
        echo json_encode(['status' => true]);
    } else {
        echo json_encode(['message' => "Error to logout"]);
    }
    $mysqli->close();
} else {
    echo json_encode(['message' => "Unathorized"]);
}