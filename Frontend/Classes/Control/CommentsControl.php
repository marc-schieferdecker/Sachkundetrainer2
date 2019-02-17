<?php
    namespace LetsShoot\Sachkundetrainer\Frontend\Control;

    use LetsShoot\Sachkundetrainer\Frontend\Control;
    use LetsShoot\Sachkundetrainer\Frontend\View\ErrorView;

    /**
     * Class CommentsControl
     * @package LetsShoot\Sachkundetrainer\Frontend\Control
     */
    class CommentsControl extends Control {
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
                if($this->__getRequestVar('comment_question_id') && $this->__getRequestVar('comment_text')) {
                    $comment = $this->getApi()->Call('Comment', 'Add', array(
                        'comment_question_id' => $this->__getRequestVar('comment_question_id'),
                        'comment_text' => $this->__getRequestVar('comment_text')
                    ));
                    if(isset($comment->errorCode)) {
                        $error = new ErrorView();
                        $error->setTitle($comment->errorCode);
                        $error->setMessage($comment->errorMsg);
                        if($this->getSession()->getUserlevel() == APPLICATION_USERLEVEL_ADMIN && APPLICATION_SHOW_ERRORTRACE) {
                            $error->setTrace($comment->errorTrace);
                        }
                        $this->view->assign('error',$error->render());
                    }
                    else {
                        $this->view->assign('comment', $comment);
                    }
                }
                else {
                    $error = new ErrorView();
                    $error->setParametersMissing();
                    $this->view->assign('error', $error->render());
                }
            }
            // Output
            $this->render(true);
        }
    }