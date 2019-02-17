<?php
    /**
     * Session config
     */
    define('APPLICATION_SESSION_VAR', 'wstrainer_frontend');

    // NOTICE: Session lifetime probably won't work correctly on debian (must be set in php.ini)
    // https://stackoverflow.com/questions/46198761/session-is-lost-after-some-time-around-25-minutes
    // lifetime settings
    $lt = 86400 * 30;
    ini_set('session.gc_maxlifetime', $lt);
    session_set_cookie_params($lt,"/");

    // start session
    session_start();
