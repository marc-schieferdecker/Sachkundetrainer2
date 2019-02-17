<?php
    namespace LetsShoot\Sachkundetrainer\Frontend\Control;

    use LetsShoot\Sachkundetrainer\Frontend\Control;

    /**
     * Class HomeControl
     * @package LetsShoot\Sachkundetrainer\Frontend\Control
     */
    class HomeControl extends Control {
        public function __construct(string $controllerName, string $controllerAction) {
            parent::__construct($controllerName, $controllerAction);
        }

        public function Home() {
            // Get latest comment
            $comments = $this->getApi()->Call('Comment', 'FindAll', array(
                'sort_by' => 'comment_timestamp',
                'sort_order' => 'desc',
                'limit' => 1
            ));
            if(!isset($comments->errorCode)) {
                foreach($comments AS $comment) {
                    $comment->question = $this->getApi()->Call('Question', 'FindById', array('id'=>$comment->comment_question_id));
                }
                $this->view->assign('comments',$comments);
            }

            // Output
            $this->render(true);
        }
    }