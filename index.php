<?php

include 'assets/php/func.php';
$arduino_connect = isArduinoConnect();

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Authorization | Smart House</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/media-style.css">
    <link rel="shortcut icon" href="/assets/img/favicon.ico" type="image/x-icon">
</head>
<body>
<div id="app">
    <?php if ( !$arduino_connect): ?>
        <div class="centering auth-screen">
            <h1>Smart House</h1>
            <p>
                Unable to connect to arduino<br>
                or to the middleware<br>
            </p>
            <p>
                Check the connection<br>
                or contact your administrator
            </p>
        </div>
    <?php endif; ?>
</div>
<script src="assets/js/jquery.js"></script>
<script src="assets/js/jquery.mobile-1.4.5.js"></script>
<script src="assets/js/cookie.js"></script>
<?php if ($arduino_connect): ?>
    <script src="assets/js/script.js"></script>
    <script>
        $(()=>{
            setInterval(()=>{
                $.ajax({
                    url: "/assets/php/checkconnect.php",
                    success(data){
                        if(data=="0"){
                            document.location.reload();
                        }
                    },
                    error(data){
                        console.error(data);
                    }
                });
            },1000);
        });
    </script>
<?php else: ?>
    <script>
        $(()=>{
            setInterval(()=>{
                $.ajax({
                    url: "/assets/php/checkconnect.php",
                    success(data){
                        if(data=="1"){
                            document.location.reload();
                        }
                    },
                    error(data){
                        console.error(data);
                    }
                });
            },1000);
        });
    </script>
<?php endif; ?>
</body>
</html>