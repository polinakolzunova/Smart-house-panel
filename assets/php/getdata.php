<?php
/**
 * Created by PhpStorm.
 * User: EndyRoys
 * Date: 16.12.2019
 * Time: 17:20
 */

include 'func.php';

if(!isArduinoConnect()){
    echo json_encode(['message' => "Not connecting"]);
    die();
}

if (checkAuthToken()) {

    $firstconnect = false;
    $commands = getNewValues();
    foreach ($commands as $id => $value) {
        if($id=="100") $firstconnect=true;
        setNewValue($id, $value);
    }
    if($firstconnect) sendCurrentClimate();
    $data = getCurrentState();
    echo json_encode($data);

} else {
    echo json_encode(['message' => "Unathorized"]);
}