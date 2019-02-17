<?php
    // Define absolut path for autoloader
    define('APPLICATION_DIRECTORY', realpath(__DIR__ . DIRECTORY_SEPARATOR . '..'));

    // Include configs
    require APPLICATION_DIRECTORY . DIRECTORY_SEPARATOR . 'Configuration' . DIRECTORY_SEPARATOR . 'api.php';
    require APPLICATION_DIRECTORY . DIRECTORY_SEPARATOR . 'Configuration' . DIRECTORY_SEPARATOR . 'session.php';
    require APPLICATION_DIRECTORY . DIRECTORY_SEPARATOR . 'Configuration' . DIRECTORY_SEPARATOR . 'template.php';
    require APPLICATION_DIRECTORY . DIRECTORY_SEPARATOR . 'Configuration' . DIRECTORY_SEPARATOR . 'userlevel.php';
    require APPLICATION_DIRECTORY . DIRECTORY_SEPARATOR . 'Configuration' . DIRECTORY_SEPARATOR . 'keys.php';
    require APPLICATION_DIRECTORY . DIRECTORY_SEPARATOR . 'Configuration' . DIRECTORY_SEPARATOR . 'security.php';
