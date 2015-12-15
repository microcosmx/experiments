<?php

    $host = 'localhost';
    $dbName = 'cosmo'; # Database name
    $username = 'root';
    $password = '';
    $prefix = 'cosmo_'; // e.g. cosmo_
    define('FOLDER', ''); // e.g. subfolder/
    $salt = 'Q`4iWDTRxG/2clqmm&G1c?5oE}`L6\k`4tW`K!!w#O!iFQ.M(bl|v@>!{5!`b*13P.4xJr:|HkvkGXds.zqlR/7"?5yQuE;f\IQG_Z\CR5+$*D4efP09Jz>XyT#7Ny<';
    $developerMode = false; // Switching this to true prevents minification/combination of JS/CSS files for better error reporting

    $pdo = new PDO("mysql:host=$host;dbname=$dbName;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $username = null;

?>