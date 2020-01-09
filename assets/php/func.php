<?php

function encode($unencoded, $key)
{//Шифруем
    $newstr = "";
    $string = base64_encode($unencoded);//Переводим в base64

    $arr = array();//Это массив
    $x = 0;
    while ($x++ < strlen($string)) {//Цикл
        $arr[$x - 1] = md5(md5($key . $string[$x - 1]) . $key);//Почти чистый md5
        $newstr = $newstr . $arr[$x - 1][3] . $arr[$x - 1][6] . $arr[$x - 1][1] . $arr[$x - 1][2];//Склеиваем символы
    }
    return $newstr;//Вертаем строку
}

function decode($encoded, $key)
{//расшифровываем
    $strofsym = "qwertyuiopasdfghjklzxcvbnm1234567890QWERTYUIOPASDFGHJKLZXCVBNM=";//Символы, с которых состоит base64-ключ
    $x = 0;
    while ($x++ <= strlen($strofsym)) {//Цикл
        $tmp = md5(md5($key . $strofsym[$x - 1]) . $key);//Хеш, который соответствует символу, на который его заменят.
        $encoded = str_replace($tmp[3] . $tmp[6] . $tmp[1] . $tmp[2], $strofsym[$x - 1], $encoded);//Заменяем №3,6,1,2 из хеша на символ
    }
    return base64_decode($encoded);//Вертаем расшифрованную строку
}

function generatePasswordHash($password)
{
    return encode($password, 'abc');;
}

function validatePassworrd($password, $hash)
{
    $passdecode = decode($hash, 'abc');
    return ($passdecode == $password);
}

function myConnect()
{
    return new mysqli('127.0.0.1', 'root', '', 'hakaton');
}

function checkAuthToken()
{
    if ($_POST) {
        $token = $_POST['token'];
        if (empty($token)) {
            return false;
        }
        $mysqli = myConnect();
        $query = "SELECT * FROM user WHERE token='$token'";
        $result = $mysqli->query($query);
        if ($result->num_rows != 1) {
            return false;
        }
        return true;
    }
}

function getNewValues()
{
    $answer = [];
    $mysqli = myConnect();
    $query = "SELECT * FROM topanel";
    $result = $mysqli->query($query);
    while ($row = $result->fetch_assoc()) {
        $rowid = $row['id'];
        $rowstr = $row['str'];
        // delete
        $query = "DELETE FROM topanel WHERE id=$rowid";
        $mysqli->query($query);
        // parse device id & value
        // then add to state
        $rowstr = explode(';', $rowstr);
        $deviceid = $rowstr[0];
        switch ($deviceid) {
            case 8:
                $deviceid = "14";
                $devicevalue = $rowstr[1];
                $answer[$deviceid] = $devicevalue;
                $deviceid = "15";
                $devicevalue = $rowstr[2];
                $answer[$deviceid] = $devicevalue;
                break;
            case 12:
                $deviceid = "12";
                $devicevalue = $rowstr[1];
                $answer[$deviceid] = $devicevalue;
                $deviceid = "13";
                $devicevalue = $rowstr[2];
                $answer[$deviceid] = $devicevalue;
                break;
            case 100:
                $devicevalue = $rowstr[1];
                $answer[$deviceid] = $devicevalue;
                break;
            default:
                $devicevalue = $rowstr[1];
                $answer[$deviceid] = $devicevalue;
                break;
        }
    }
    $mysqli->close();
    return $answer;
}

function sendCurrentClimate()
{
    $mysqli = myConnect();

    $query = "SELECT * FROM state WHERE id=8";
    $d8 = (($mysqli->query($query))->fetch_row())[1];
    $query = "SELECT * FROM state WHERE id=10";
    $d10 = (($mysqli->query($query))->fetch_row())[1];
    $str = "8;$d8;$d10";
    $query = "INSERT INTO toarduino (`id`, `str`) VALUES (NULL, '$str')";
    $mysqli->query($query);

    $mysqli->close();
}

function getCurrentState()
{
    $answer = [];
    $mysqli = myConnect();
    $query = "SELECT * FROM state";
    $result = $mysqli->query($query);
    while ($row = $result->fetch_assoc()) {
        $id = $row['id'];
        $value = $row['value'];
        $answer[$id] = $value;
    }
    $mysqli->close();
    return $answer;
}

function setNewValue($id, $value)
{
    $mysqli = myConnect();
    $query = "UPDATE state SET `value`='$value' WHERE `id`='$id';";
    $mysqli->query($query);
    $mysqli->close();
}

function setNewCommand($id, $value)
{
    $mysqli = myConnect();
    $str = "";

    if ($id == 8) {
        $query = "SELECT * FROM state WHERE id=10";
        $result = (($mysqli->query($query))->fetch_row())[1];
        $str = "$id;$value;$result";
    } else if ($id == 10) {
        $query = "SELECT * FROM state WHERE id=8";
        $result = (($mysqli->query($query))->fetch_row())[1];
        $str = "8;$result;$value";
    } else {
        $str = "$id;$value";
    }

    $query = "INSERT INTO toarduino (`id`, `str`) VALUES (NULL, '$str')";
    $mysqli->query($query);

    $mysqli->close();
}

function isArduinoConnect()
{
    $mysqli = myConnect();
    $query = "SELECT * FROM state WHERE id=100";
    $result = (($mysqli->query($query))->fetch_row())[1];
    $mysqli->close();
    return $result;
}