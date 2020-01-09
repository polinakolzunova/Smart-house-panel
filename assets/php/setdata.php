<?php
/**
 * Created by PhpStorm.
 * User: EndyRoys
 * Date: 16.12.2019
 * Time: 15:15
 */

include 'func.php';

if (checkAuthToken()) {

    $id = $_POST['id'];
    $value = $_POST['value'];

    if($value=="true") $value = "1";
    if($value=="false") $value = "0";
    setNewValue($id, $value);
    setNewCommand($id, $value);

    echo json_encode([$id, $value]);

} else {
    echo json_encode(['message' => "Unathorized"]);
}