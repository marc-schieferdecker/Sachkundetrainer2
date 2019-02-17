<?php
    namespace LetsShoot\Sachkundetrainer\Frontend\Control;

    use LetsShoot\Sachkundetrainer\Frontend\Control;
    use LetsShoot\Sachkundetrainer\Frontend\View\ErrorView;
    use LetsShoot\Sachkundetrainer\Frontend\Filter;

    /**
     * Class UsersControl
     * @package LetsShoot\Sachkundetrainer\Frontend\Control
     */
    class UsersControl extends Control {
        public function __construct(string $controllerName, string $controllerAction) {
            parent::__construct($controllerName, $controllerAction);

            // Filtering
            $Filter = new Filter($this->getRequest());
            $Filter->createExlusiveActionFilter(get_class($this));
            if(isset($this->getRequest()['filterreset'])) {
                $Filter->clearFilter(get_class($this));
            }
            $Filter->registerFilter(get_class($this), 'string');
            if(isset($this->getRequest()['string'])) {
                $Filter->setFilter(get_class($this), 'string', $this->getRequest()['string']);
            }
            $this->setRequest($Filter->getFilteredRequest());

            // Assign filters
            if($this->__getRequestVar('string')) {
                $this->view->assign('string', $this->__getRequestVar('string'));
            }
        }

        public function Users() {
            if($this->getSession()->getUserlevel() < APPLICATION_USERLEVEL_ADMIN) {
                $error = new ErrorView();
                $error->setRightsMissing();
                $this->view->assign('RightsMissing', $error->render());
            }
            else {
                if($this->__getRequestVar('save')) {
                    $add = $this->getApi()->Call('User','Add', $this->getRequest());
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
                        $this->Redirect('Users', 'Users');
                    }
                }

                if($this->__getRequestVar('string')) {
                    $users = $this->getApi()->Call('User', 'FindByFields', array(
                        'user_email'=>$this->__getRequestVar('string'),
                        'user_name'=>$this->__getRequestVar('string')
                    ));
                }
                else {
                    $users = $this->getApi()->Call('User', 'FindAll');
                }

                $this->view->assign('users',$users);
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
                        $update = $this->getApi()->Call('User','Update', $this->getRequest());
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
                            $this->Redirect('Users', 'Users');
                        }
                    }

                    $user = $this->getApi()->Call('User','FindById', $this->getRequest());
                    $this->view->assign('user', $user);
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
                    $delete = $this->getApi()->Call('User','Delete', $this->getRequest());
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
                        $this->Redirect('Users', 'Users');
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

        public function CreateApikey() {
            if($this->getSession()->getUserlevel() < APPLICATION_USERLEVEL_ADMIN) {
                $error = new ErrorView();
                $error->setRightsMissing();
                $this->view->assign('RightsMissing', $error->render());
            }
            else {
                if($this->__getRequestVar('id')) {
                    $result = $this->getApi()->Call('User', 'CreateApiKey', array('id' => $this->__getRequestVar('id')));
                    if(isset($result->errorCode)) {
                        $error = new ErrorView();
                        $error->setTitle($result->errorCode);
                        $error->setMessage($result->errorMsg);
                        if($this->getSession()->getUserlevel() == APPLICATION_USERLEVEL_ADMIN && APPLICATION_SHOW_ERRORTRACE) {
                            $error->setTrace($result->errorTrace);
                        }
                        $this->view->assign('error',$error->render());
                    }
                    else {
                        $this->Redirect('Users', 'Edit', array('id'=>$this->__getRequestVar('id')));
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