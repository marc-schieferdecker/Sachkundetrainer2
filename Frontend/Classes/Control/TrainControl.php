<?php
    namespace LetsShoot\Sachkundetrainer\Frontend\Control;

    use LetsShoot\Sachkundetrainer\Frontend\Control;
    use LetsShoot\Sachkundetrainer\Frontend\View\ErrorView;

    /**
     * Class TrainControl
     * @package LetsShoot\Sachkundetrainer\Frontend\Control
     */
    class TrainControl extends Control {
        public function __construct(string $controllerName, string $controllerAction) {
            parent::__construct($controllerName, $controllerAction);
        }

        public function Solve() {
            if($this->getSession()->getUserlevel() < APPLICATION_USERLEVEL_GUEST) {
                $error = new ErrorView();
                $error->setRightsMissing();
                $this->view->assign('RightsMissing', $error->render());
            }
            else {
                $this->view->assign('result', true);
                $result = true;

                $favourites = $this->getApi()->Call('Favourite', 'FindAll');
                $this->view->assign('favourites',$favourites);

                $topic_id = $this->__getRequestVar('topic_id');
                $subtopic_id = $this->__getRequestVar('subtopic_id');
                $this->view->assign('topic_id', $topic_id);
                $this->view->assign('subtopic_id', $subtopic_id);

                $return = $this->__getRequestVar('return') ? $this->__getRequestVar('return') : 'Random';
                $answeres = is_array($this->__getRequestVar('answeres')) ? $this->__getRequestVar('answeres') : array();
                $question = $this->getApi()->Call('Question', 'FindById', array('id'=>$this->__getRequestVar('id')));
                if (!isset($question->errorCode)) {
                    $this->view->assign('question', $question);
                    $this->view->assign('answeres', $answeres);
                    $this->view->assign('return', $return);
                    if($question->answeres) {
                        foreach($question->answeres AS $answere) {
                            $is_selected = isset($answeres[$answere->answere_id]) ? true : false;
                            $is_correct = (($answere->answere_correct && $is_selected) || (!$answere->answere_correct && !$is_selected)) ? true : false;
                            if(!$is_correct) {
                                $this->view->assign('result', false);
                                $result = false;
                            }
                        }
                    }
                    // Update question statistics
                    if($result) {
                        $this->getApi()->Call('Question', 'IncreaseCountRight', array('id'=>$this->__getRequestVar('id')));
                        $this->getApi()->Call('User', 'IncreaseCountRight');
                        // Set already answered?
                        if($this->getUser()->user_config->disable_duplicate_questions) {
                            $alreadyAnswered = $this->getUser()->user_answered;
                            if(!in_array($this->__getRequestVar('id'), $alreadyAnswered)) {
                                $alreadyAnswered[] = $this->__getRequestVar('id');
                            }
                            $alreadyAnswered = array_unique($alreadyAnswered);
                            $this->getApi()->Call('User', 'Update', array('id'=>$this->getUser()->user_id,'user_answered'=>$alreadyAnswered));
                        }
                    }
                    else {
                        $this->getApi()->Call('Question', 'IncreaseCountWrong', array('id'=>$this->__getRequestVar('id')));
                        $this->getApi()->Call('User', 'IncreaseCountWrong');
                    }
                } else {
                    $error = new ErrorView();
                    $error->setCanNotLoadEntry();
                    $this->view->assign('error', $error->render());
                }
            }
            // Output
            $this->render(true);
        }

        public function Random() {
            if($this->getSession()->getUserlevel() < APPLICATION_USERLEVEL_GUEST) {
                $error = new ErrorView();
                $error->setRightsMissing();
                $this->view->assign('RightsMissing', $error->render());
            }
            else {
                $favourites = $this->getApi()->Call('Favourite', 'FindAll');
                $this->view->assign('favourites',$favourites);

                $question = $this->getApi()->Call('Question', 'FindByRandom');
                if (!isset($question->errorCode)) {
                    $this->view->assign('question', $question);
                } else {
                    $error = new ErrorView();
                    $error->setCanNotLoadQuestionMaybeAllAnswered();
                    $this->view->assign('error', $error->render());
                }
            }
            // Output
            $this->render(true);
        }

        public function Topics() {
            if($this->getSession()->getUserlevel() < APPLICATION_USERLEVEL_GUEST) {
                $error = new ErrorView();
                $error->setRightsMissing();
                $this->view->assign('RightsMissing', $error->render());
            }
            else {
                $favourites = $this->getApi()->Call('Favourite', 'FindAll');
                $this->view->assign('favourites',$favourites);

                $topics = $this->getApi()->Call('Topic', 'FindAll');
                $this->view->assign('topics', $topics);

                if($this->__getRequestVar('topic_id') || $this->__getRequestVar('id')) {
                    // If id is set load question
                    if($this->__getRequestVar('id')) {
                        $question = $this->getApi()->Call('Question', 'FindById', array('id'=>$this->__getRequestVar('id')));
                        if (!isset($question->errorCode)) {
                            $this->view->assign('topic_id', $question->question_topic_id);
                            $this->view->assign('subtopic_id', $question->question_subtopic_id);
                            $this->view->assign('question', $question);
                        } else {
                            $error = new ErrorView();
                            $error->setCanNotLoadEntry();
                            $this->view->assign('error', $error->render());
                        }
                    }
                    // Otherwise proceed normal and use user input to get a random question
                    else {
                        $question = $this->getApi()->Call('Question', 'FindByRandom', array('topic_id'=>$this->__getRequestVar('topic_id'),'subtopic_id'=>$this->__getRequestVar('subtopic_id')));
                        if (!isset($question->errorCode)) {
                            $this->view->assign('topic_id', $this->__getRequestVar('topic_id'));
                            $this->view->assign('subtopic_id', $this->__getRequestVar('subtopic_id'));
                            $this->view->assign('question', $question);
                        } else {
                            $error = new ErrorView();
                            $error->setCanNotLoadQuestionMaybeAllAnsweredInTopic();
                            $this->view->assign('error', $error->render());
                        }
                    }
                }
            }
            // Output
            $this->render(true);
        }

        public function Hard() {
            if($this->getSession()->getUserlevel() < APPLICATION_USERLEVEL_GUEST) {
                $error = new ErrorView();
                $error->setRightsMissing();
                $this->view->assign('RightsMissing', $error->render());
            }
            else {
                $favourites = $this->getApi()->Call('Favourite', 'FindAll');
                $this->view->assign('favourites',$favourites);

                $question = $this->getApi()->Call('Question', 'FindByRandom', array('hard'=>1));
                if (!isset($question->errorCode)) {
                    $this->view->assign('question', $question);
                } else {
                    $error = new ErrorView();
                    $error->setCanNotLoadEntry();
                    $this->view->assign('error', $error->render());
                }
            }
            // Output
            $this->render(true);
        }

        public function Favourites() {
            if($this->getSession()->getUserlevel() < APPLICATION_USERLEVEL_USER) {
                $error = new ErrorView();
                $error->setRightsMissing();
                $this->view->assign('RightsMissing', $error->render());
            }
            else {
                $favourites = $this->getApi()->Call('Favourite', 'FindAll');
                $this->view->assign('favourites',$favourites);

                $question = $this->getApi()->Call('Question', 'FindByRandom', array('favourites'=>1));
                if (!isset($question->errorCode)) {
                    $this->view->assign('question', $question);
                } else {
                    $error = new ErrorView();
                    $error->setCanNotLoadEntry();
                    $this->view->assign('error', $error->render());
                }
            }
            // Output
            $this->render(true);
        }

        public function List() {
            if($this->getSession()->getUserlevel() < APPLICATION_USERLEVEL_GUEST) {
                $error = new ErrorView();
                $error->setRightsMissing();
                $this->view->assign('RightsMissing', $error->render());
            }
            else {
                $topics = $this->getApi()->Call('Topic', 'FindAll');
                $this->view->assign('topics', $topics);
                $questions = $this->getApi()->Call('Question', 'FindAll');
                $this->view->assign('questions',$questions);
            }
            // Output
            $this->render(true);
        }
    }