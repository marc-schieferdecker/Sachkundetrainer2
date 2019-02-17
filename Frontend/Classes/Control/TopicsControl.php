<?php
    namespace LetsShoot\Sachkundetrainer\Frontend\Control;

    use LetsShoot\Sachkundetrainer\Frontend\Control;
    use LetsShoot\Sachkundetrainer\Frontend\View\ErrorView;

    /**
     * Class TopicsControl
     * @package LetsShoot\Sachkundetrainer\Frontend\Control
     */
    class TopicsControl extends Control {
        public function __construct(string $controllerName, string $controllerAction) {
            parent::__construct($controllerName, $controllerAction);
        }

        public function Topics() {
            if($this->getSession()->getUserlevel() < APPLICATION_USERLEVEL_ADMIN) {
                $error = new ErrorView();
                $error->setRightsMissing();
                $this->view->assign('RightsMissing', $error->render());
            }
            else {
                if($this->__getRequestVar('save')) {
                    $add = $this->getApi()->Call('Topic','Add', $this->getRequest());
                    if(isset($add->errorCode)) {
                        $error = new ErrorView();
                        $error->setTitle($add->errorCode);
                        $error->setMessage($add->errorMsg);
                        if($this->getSession()->getUserlevel() == APPLICATION_USERLEVEL_ADMIN && APPLICATION_SHOW_ERRORTRACE) {
                            $error->setTrace($add->errorTrace);
                        }
                        $this->view->assign('error',$error->render());
                    }
                    else {
                        $this->Redirect('Topics', 'Topics');
                    }
                }

                $topics = $this->getApi()->Call('Topic', 'FindAll');
                $this->view->assign('topics',$topics);
            }
            // Output
            $this->render(true);
        }

        public function Edit() {
            if($this->getSession()->getUserlevel() < APPLICATION_USERLEVEL_ADMIN) {
                $error = new ErrorView();
                $error->setRightsMissing();
                $this->view->assign('RightsMissing', $error->render());
            }
            else {
                if($this->__getRequestVar('id')) {
                    if($this->__getRequestVar('save')) {
                        $update = $this->getApi()->Call('Topic','Update', $this->getRequest());
                        if(isset($update->errorCode)) {
                            $error = new ErrorView();
                            $error->setTitle($update->errorCode);
                            $error->setMessage($update->errorMsg);
                            if($this->getSession()->getUserlevel() == APPLICATION_USERLEVEL_ADMIN && APPLICATION_SHOW_ERRORTRACE) {
                                $error->setTrace($update->errorTrace);
                            }
                            $this->view->assign('error',$error->render());
                        }
                        else {
                            $this->Redirect('Topics', 'Topics');
                        }
                    }

                    $topic = $this->getApi()->Call('Topic','FindById', $this->getRequest());
                    $this->view->assign('topic', $topic);
                }
                else {
                    $error = new ErrorView();
                    $error->setIDParameterMissing();
                    $this->view->assign('error',$error->render());
                }
            }
            // Output
            $this->render(true);
        }

        public function Delete() {
            if($this->getSession()->getUserlevel() < APPLICATION_USERLEVEL_ADMIN) {
                $error = new ErrorView();
                $error->setRightsMissing();
                $this->view->assign('RightsMissing', $error->render());
            }
            else {
                if($this->__getRequestVar('id')) {
                    $delete = $this->getApi()->Call('Topic','Delete', $this->getRequest());
                    if(isset($delete->errorCode)) {
                        $error = new ErrorView();
                        $error->setTitle($delete->errorCode);
                        $error->setMessage($delete->errorMsg);
                        if($this->getSession()->getUserlevel() == APPLICATION_USERLEVEL_ADMIN && APPLICATION_SHOW_ERRORTRACE) {
                            $error->setTrace($delete->errorTrace);
                        }
                        $this->view->assign('error',$error->render());
                    }
                    else {
                        $this->Redirect('Topics', 'Topics');
                    }
                }
                else {
                    $error = new ErrorView();
                    $error->setIDParameterMissing();
                    $this->view->assign('error',$error->render());
                }
            }
            // Output
            $this->render(true);
        }
    }