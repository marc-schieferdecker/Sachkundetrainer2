<?php
    namespace LetsShoot\Sachkundetrainer\Frontend\Control;

    use LetsShoot\Sachkundetrainer\Frontend\Control;
    use LetsShoot\Sachkundetrainer\Frontend\View\ErrorView;

    /**
     * Class QuestionsControl
     * @package LetsShoot\Sachkundetrainer\Frontend\Control
     */
    class QuestionsControl extends Control {
        public function __construct(string $controllerName, string $controllerAction) {
            parent::__construct($controllerName, $controllerAction);
        }

        public function Questions() {
            if($this->getSession()->getUserlevel() < APPLICATION_USERLEVEL_ADMIN) {
                $error = new ErrorView();
                $error->setRightsMissing();
                $this->view->assign('RightsMissing', $error->render());
            }
            else {
                if($this->__getRequestVar('save')) {
                    $add = $this->getApi()->Call('Question','Add', $this->getRequest());
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
                        // Handle answeres
                        if($this->__getRequestVar('answere') && is_array($this->__getRequestVar('answere')) && count($this->__getRequestVar('answere'))) {
                            $answeres = $this->__getRequestVar('answere');
                            foreach($answeres['answere_number'] AS $index => $answere_number) {
                                $answere_choice = $answeres['answere_choice'][$index];
                                $answere_text = $answeres['answere_text'][$index];
                                $answere_correct = $answeres['answere_correct'][$index];
                                $this->getApi()->Call('Answere', 'Add', array(
                                    'answere_question_id' => $add->question_id,
                                    'answere_number' => $answere_number,
                                    'answere_choice' => $answere_choice,
                                    'answere_text' => $answere_text,
                                    'answere_correct' => $answere_correct
                                ));
                            }
                        }
                        $this->Redirect('Questions', 'Questions');
                    }
                }

                $topics = $this->getApi()->Call('Topic','FindAll');
                $this->view->assign('topics', $topics);

                $subtopics = $this->getApi()->Call('Subtopic','FindAll');
                $this->view->assign('subtopics', $subtopics);

                $questions = $this->getApi()->Call('Question', 'FindAll');
                $this->view->assign('questions',$questions);
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
                        $update = $this->getApi()->Call('Question','Update', $this->getRequest());
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
                            // Handle answeres
                            if($this->__getRequestVar('answere') && is_array($this->__getRequestVar('answere')) && count($this->__getRequestVar('answere'))) {
                                $answeres = $this->__getRequestVar('answere');
                                foreach($answeres['answere_number'] AS $index => $answere_number) {
                                    $answere_id = isset($answeres['answere_id'][$index]) ? $answeres['answere_id'][$index] : 0;
                                    $answere_choice = $answeres['answere_choice'][$index];
                                    $answere_text = $answeres['answere_text'][$index];
                                    $answere_correct = $answeres['answere_correct'][$index];
                                    $delete = $answeres['delete'][$index];
                                    if($delete && $answere_id) {
                                        $this->getApi()->Call('Answere', 'Delete', array('id'=>$answere_id));
                                    }
                                    elseif ($answere_id) {
                                        $this->getApi()->Call('Answere', 'Update', array(
                                            'id' => $answere_id,
                                            'answere_question_id' => $this->__getRequestVar('id'),
                                            'answere_number' => $answere_number,
                                            'answere_choice' => $answere_choice,
                                            'answere_text' => $answere_text,
                                            'answere_correct' => $answere_correct
                                        ));
                                    }
                                    else {
                                        $this->getApi()->Call('Answere', 'Add', array(
                                            'answere_question_id' => $this->__getRequestVar('id'),
                                            'answere_number' => $answere_number,
                                            'answere_choice' => $answere_choice,
                                            'answere_text' => $answere_text,
                                            'answere_correct' => $answere_correct
                                        ));
                                    }
                                }
                            }
                            $this->Redirect('Questions', 'Questions');
                        }
                    }

                    $topics = $this->getApi()->Call('Topic','FindAll');
                    $this->view->assign('topics', $topics);

                    $subtopics = $this->getApi()->Call('Subtopic','FindAll');
                    $this->view->assign('subtopics', $subtopics);

                    $question = $this->getApi()->Call('Question','FindById', $this->getRequest());
                    $this->view->assign('question', $question);
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
                    // Load question and delete answeres
                    $question = $this->getApi()->Call('Question', 'FindById', array('id'=>$this->__getRequestVar('id')));
                    if(!isset($question->errorCode)) {
                        if(count($question->answeres)) {
                            foreach($question->answeres AS $answere) {
                                $this->getApi()->Call('Answere', 'Delete', array('id'=>$answere->answere_id));
                            }
                        }
                        $delete = $this->getApi()->Call('Question', 'Delete', $this->getRequest());
                        if (isset($delete->errorCode)) {
                            $error = new ErrorView();
                            $error->setTitle($delete->errorCode);
                            $error->setMessage($delete->errorMsg);
                            if ($this->getSession()->getUserlevel() == APPLICATION_USERLEVEL_ADMIN && APPLICATION_SHOW_ERRORTRACE) {
                                $error->setTrace($delete->errorTrace);
                            }
                            $this->view->assign('error', $error->render());
                        } else {
                            $this->Redirect('Questions', 'Questions');
                        }
                    }
                    else {
                        $error = new ErrorView();
                        $error->setCanNotLoadEntry();
                        $this->view->assign('error',$error->render());
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