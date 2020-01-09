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

    if ( !empty($login) and !empty($password)) {
        $mysqli = myConnect();
        $password = generatePasswordHash($_POST['password']);
        $query = "INSERT INTO user (`id`, `login`, `password`, `token`) VALUES (NULL, '$login','$password',NULL)";
        $result = $mysqli->query($query);
        echo $result->num_rows;
        $mysqli->close();
    }
}
