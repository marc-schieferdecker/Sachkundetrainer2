<?php
    /**
     * Settings for logging
     */
    // Log file on a daily base
    define('APPLICATION_LOG_FILE', APPLICATION_DIRECTORY . DIRECTORY_SEPARATOR . 'Log' . DIRECTORY_SEPARATOR . 'api.log');

    // Define log levels
    define('APPLICATION_LOG_LEVEL_DEBUG', 1);
    define('APPLICATION_LOG_LEVEL_WARN', 2);
    define('APPLICATION_LOG_LEVEL_INFO', 4);
    define('APPLICATION_LOG_LEVEL_CRITICAL', 8);

    // On critical errors send mail
    define('APPLICATION_CRITICAL_EMAIL_FROM', 'admin@letsshootshow.de');
    define('APPLICATION_CRITICAL_EMAIL_TO', 'ms@letsshootshow.de');

    // Set log level
    define('APPLICATION_LOG_LEVEL', APPLICATION_LOG_LEVEL_CRITICAL | APPLICATION_LOG_LEVEL_DEBUG | APPLICATION_LOG_LEVEL_WARN | APPLICATION_LOG_LEVEL_INFO);

    // Send error traces in response object?
    define('APPLICATION_SHOW_ERRORTRACE', false);
