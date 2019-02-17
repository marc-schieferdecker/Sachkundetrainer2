<?php
namespace LetsShoot\Sachkundetrainer\Frontend\Control;

use LetsShoot\Sachkundetrainer\Frontend\Control;

/**
 * Class StatisticsControl
 * @package LetsShoot\Sachkundetrainer\Frontend\Control
 */
class StatisticsControl extends Control {
    public function __construct(string $controllerName, string $controllerAction) {
        parent::__construct($controllerName, $controllerAction);
    }

    public function Statistics() {
        // Reset statistics?
        if($this->__getRequestVar('reset')) {
            $this->getApi()->Call('User', 'Update', array(
                'id'=>$this->getUser()->user_id,
                'user_count_wrong' => 0,
                'user_count_right' => 0
            ));
            $this->Redirect('Statistics', 'Statistics');
        }

        // Calculate percentages
        $count_right = intval($this->getUser()->user_count_right);
        $count_wrong = intval($this->getUser()->user_count_wrong);
        $count_total = $count_right + $count_wrong;
        if($count_total) {
            $this->view->assign('count_total', $count_total);
            $this->view->assign('precent_right', round(($count_right / $count_total) * 100, 2));
            $this->view->assign('precent_wrong', round(($count_wrong / $count_total) * 100, 2));
            $this->view->assign('count_right', $count_right);
            $this->view->assign('count_wrong', $count_wrong);
        }
        else {
            $this->view->assign('count_total', 0);
            $this->view->assign('precent_right', 0);
            $this->view->assign('precent_wrong', 0);
            $this->view->assign('count_right', 0);
            $this->view->assign('count_wrong', 0);
        }

        // Get hard questions
        $questions = $this->getApi()->Call('Question', 'FindAll', array('limit'=>50, 'sort_by' => 'question_count_wrong', 'sort_order' => 'DESC'));
        if($questions) {
            $this->view->assign('questions', $questions);
        }

        // Output
        $this->render(true);
    }

    public function Favourites() {
        // Get favourites
        $favourites = $this->getApi()->Call('Favourite', 'FindAll');
        if($favourites) {
            foreach($favourites AS $favourite) {
                $favourite->question = $this->getApi()->Call('Question','FindById',array('id'=>$favourite->question_id));
            }
        }
        $this->view->assign('favourites',$favourites);

        // Output
        $this->render(true);
    }

    public function Comments() {
        // Get comments and load all questions that have comments
        $questions = array();
        $comments = $this->getApi()->Call('Comment', 'FindAll', array(
            'sort_by' => 'comment_timestamp',
            'sort_order' => 'desc'
        ));
        if(!isset($comments->errorCode)) {
            foreach($comments AS $comment) {
                if(!isset($questions[$comment->comment_question_id])) {
                    $questions[$comment->comment_question_id] = $this->getApi()->Call('Question', 'FindById', array('id' => $comment->comment_question_id));
                }
            }
            $this->view->assign('questions',$questions);
        }

        // Output
        $this->render(true);
    }
}