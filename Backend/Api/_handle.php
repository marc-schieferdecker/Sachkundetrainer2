<?php
    /**
     * handle request
     */
    try {
        $ActionClassString = "\\LetsShoot\\Sachkundetrainer\\Backend\\Control\\" . $ControllerClass;
        $Controller = new $ActionClassString();
    }
    catch( \Exception $e ) {
        $Logger = new \LetsShoot\Sachkundetrainer\Backend\Logger();
        $Logger->Log(APPLICATION_LOG_LEVEL_CRITICAL, $e);
    }

    // Response
    /**
     * @var LetsShoot\Sachkundetrainer\Backend\Control $Controller
     */
    echo json_encode($Controller->getResponse(),APPLICATION_JSON_OPTIONS);
