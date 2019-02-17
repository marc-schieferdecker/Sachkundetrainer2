<?php
    namespace LetsShoot\Sachkundetrainer\Frontend\Control;

    use LetsShoot\Sachkundetrainer\Frontend\Control;
    use LetsShoot\Sachkundetrainer\Frontend\View\ErrorView;

    /**
     * Class FavouriteControl
     * @package LetsShoot\Sachkundetrainer\Frontend\Control
     */
    class FavouriteControl extends Control {
        public function __construct(string $controllerName, string $controllerAction) {
            parent::__construct($controllerName, $controllerAction);
        }

        public function Add() {
            if($this->getSession()->getUserlevel() < APPLICATION_USERLEVEL_USER) {
                $error = new ErrorView();
                $error->setRightsMissing();
                $this->view->assign('RightsMissing', $error->render());
            }
            else {
                $return = $this->getApi()->Call('Favourite', 'Add', array('question_id'=>$this->__getRequestVar('question_id')));
                if (!isset($return->errorCode)) {

                } else {
                    $error = new ErrorView();
                    $error->setTitle($return->errorCode);
                    $error->setMessage($return->errorMsg);
                    if($this->getSession()->getUserlevel() == APPLICATION_USERLEVEL_ADMIN && APPLICATION_SHOW_ERRORTRACE) {
                        $error->setTrace($return->errorTrace);
                    }
                    $this->view->assign('error',$error->render());
                }
            }
            // Output
            $this->render(true);
        }

        public function Remove() {
            if($this->getSession()->getUserlevel() < APPLICATION_USERLEVEL_USER) {
                $error = new ErrorView();
                $error->setRightsMissing();
                $this->view->assign('RightsMissing', $error->render());
            }
            else {
                $return = $this->getApi()->Call('Favourite', 'Remove', array('question_id'=>$this->__getRequestVar('question_id')));
                if (!isset($return->errorCode)) {

                } else {
                    $error = new ErrorView();
                    $error->setTitle($return->errorCode);
                    $error->setMessage($return->errorMsg);
                    if($this->getSession()->getUserlevel() == APPLICATION_USERLEVEL_ADMIN && APPLICATION_SHOW_ERRORTRACE) {
                        $error->setTrace($return->errorTrace);
                    }
                    $this->view->assign('error',$error->render());
                }
            }
            // Output
            $this->render(true);
        }
    }