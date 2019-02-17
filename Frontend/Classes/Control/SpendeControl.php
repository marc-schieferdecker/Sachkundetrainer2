<?php
    namespace LetsShoot\Sachkundetrainer\Frontend\Control;

    use LetsShoot\Sachkundetrainer\Frontend\Control;

    /**
     * Class SpendeControl
     * @package LetsShoot\Sachkundetrainer\Frontend\Control
     */
    class SpendeControl extends Control {
        public function __construct(string $controllerName, string $controllerAction) {
            parent::__construct($controllerName, $controllerAction);
        }

        public function Spende() {
            if($this->__getRequestVar('amount')) {
                $this->view->assign('amount', number_format($this->__getRequestVar('amount'), 2, ',', ''));
            }
            // Output
            $this->render(true);
        }
    }