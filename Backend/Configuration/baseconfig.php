<?php
    // Define absolut path
    define('APPLICATION_DIRECTORY', realpath(__DIR__ . DIRECTORY_SEPARATOR . '..'));

    // Include other configs
    require APPLICATION_DIRECTORY . DIRECTORY_SEPARATOR . 'Configuration' . DIRECTORY_SEPARATOR . 'logging.php';
    require APPLICATION_DIRECTORY . DIRECTORY_SEPARATOR . 'Configuration' . DIRECTORY_SEPARATOR . 'uncatchable-errors.php';
    require APPLICATION_DIRECTORY . DIRECTORY_SEPARATOR . 'Configuration' . DIRECTORY_SEPARATOR . 'database.php';
    require APPLICATION_DIRECTORY . DIRECTORY_SEPARATOR . 'Configuration' . DIRECTORY_SEPARATOR . 'proxy.php';
    require APPLICATION_DIRECTORY . DIRECTORY_SEPARATOR . 'Configuration' . DIRECTORY_SEPARATOR . 'json.php';
    require APPLICATION_DIRECTORY . DIRECTORY_SEPARATOR . 'Configuration' . DIRECTORY_SEPARATOR . 'userlevel.php';
    require APPLICATION_DIRECTORY . DIRECTORY_SEPARATOR . 'Configuration' . DIRECTORY_SEPARATOR . 'passwords.php';
    require APPLICATION_DIRECTORY . DIRECTORY_SEPARATOR . 'Configuration' . DIRECTORY_SEPARATOR . 'mail.php';
