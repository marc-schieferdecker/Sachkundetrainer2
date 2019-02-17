<?php
    // Run application
    require('manifest.php');

    // Session
    $Session = new \LetsShoot\Sachkundetrainer\Frontend\Session();

    // Try single sign on if request parameter api_key is set
    if (isset($_REQUEST['api_key']) && !empty($_REQUEST['api_key'])) {
        $Api = new \LetsShoot\Sachkundetrainer\Frontend\Api();
        $sso_result = $Api->Call('User', 'FindByApiKey', array('api_key' => $_REQUEST['api_key']));
        if (!isset($sso_result->errorCode) && !empty($sso_result->user_id)) {
            $Session->setApiKey($_REQUEST['api_key']);
            $Session->setUserlevel($sso_result->user_userlevel);
            $redirect = str_replace('&api_key='.$_REQUEST['api_key'], '', $_SERVER['REQUEST_URI']);
            $redirect = str_replace('api_key='.$_REQUEST['api_key'], '', $redirect);
            header('Location:'.$redirect);
        }
    }

    // Set Controller
    if(!isset($_REQUEST['ctrl']) || empty($_REQUEST['ctrl'])) {
        if(!empty($Session->getApiKey())) {
            $_REQUEST['ctrl'] = 'Home';
        }
        else {
            $_REQUEST['ctrl'] = 'Login';
            $_GET['ctrl'] = 'Login';
            $_POST['ctrl'] = 'Login';
        }
    }
    else {
        // If session has ended
        if(empty($Session->getApiKey()) && $_REQUEST['ctrl'] != 'Login') {
            $_REQUEST['ctrl'] = 'Login';
            $_REQUEST['action'] = '';
        }
    }

    // Set default action
    if(!isset($_REQUEST['action']) || empty($_REQUEST['action'])) {
        $_REQUEST['action'] = $_REQUEST['ctrl'];
    }

    // Handle ctrl->action
    $ActionMethod = $_REQUEST['action'];
    $ControllerClassString = "\\LetsShoot\\Sachkundetrainer\\Frontend\\Control\\" . $_REQUEST['ctrl'] . "Control";
    $ControllerClass = new $ControllerClassString($_REQUEST['ctrl'],$_REQUEST['action']);
    $ControllerClass->$ActionMethod();
