<?php
    $dbuser = "u535931328_mjr";
    $dbpass = "#dP9$433Rta";
    $host = "localhost"; 
    $db = "u535931328_rposystem";

    $mysqli = new mysqli($host, $dbuser, $dbpass, $db);

    // Check connection
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

 
?>