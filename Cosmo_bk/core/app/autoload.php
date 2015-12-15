<?php

    $host = 'localhost';
    $dbName = 'cosmo'; # Database name
    $username = 'root';
    $password = 'root';
    $prefix = 'cosmo_'; // e.g. cosmo_
    define('FOLDER', 'workspace/experiments/Cosmo/'); // e.g. subfolder/
    $salt = 'k#.93yW{,aC44wn*h=a"-*TiX_J"="/z54L-ogYUY,Q0{6W(a;cvx==wRE*Rrh`!4ue!NlES7S{\jVy.,{/e$1w^_}T}UDE(_,gBvOi"b7/P`5yBdnTA=nQfSp:{l7!i';
    $developerMode = false; // Switching this to true prevents minification/combination of JS/CSS files for better error reporting

    $pdo = new PDO("mysql:host=$host;dbname=$dbName;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $username = null;

?>