<?php
/**
 * config/environnement.php
 */

define('ENVIRONNEMENT', 'developpement'); 

if (ENVIRONNEMENT === 'production') {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', __DIR__ . '/../logs/erreurs.log');
} else {
    ini_set('display_errors', '1');
}

error_reporting(E_ALL);
