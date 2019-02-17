<?php
    /**
     * Catch uncatchable errors
     */
    set_exception_handler( "application_fatal_handler" );

    function application_fatal_handler($exception) {
        // Log
        $logger = new \LetsShoot\Sachkundetrainer\Backend\Logger();
        $logger->Log(APPLICATION_LOG_LEVEL_WARN, $exception->getMessage() . ' ' . $exception->getFile() . ' ' . $exception->getLine());
        $logger->MarkEndOfRequest();

        // Output error
        $Error = new \LetsShoot\Sachkundetrainer\Backend\ErrorResponse();
        $Error -> setErrorException();
        echo json_encode($Error,APPLICATION_JSON_OPTIONS);
    }

    // Disable error reporting
    error_reporting(0);
