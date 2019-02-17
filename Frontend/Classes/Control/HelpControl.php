<?php
    namespace LetsShoot\Sachkundetrainer\Frontend\Control;

    use LetsShoot\Sachkundetrainer\Frontend\Control;

    /**
     * Class HelpControl
     * @package LetsShoot\Sachkundetrainer\Frontend\Control
     */
    class HelpControl extends Control {
        public function __construct(string $controllerName, string $controllerAction) {
            parent::__construct($controllerName, $controllerAction);
        }

        public function Help() {
            // Set which readme file to open
            $this->view->assign('readmefile', $this->__getRequestVar('readmefile'));

            // Output
            $this->render(true);
        }
    }