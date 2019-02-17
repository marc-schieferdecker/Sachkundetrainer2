<?php
    namespace LetsShoot\Sachkundetrainer\Backend\Control;

    use LetsShoot\Sachkundetrainer\Backend\Control;
    use LetsShoot\Sachkundetrainer\Backend\ErrorResponse;
    use LetsShoot\Sachkundetrainer\Backend\Model\CommentModel;
    use LetsShoot\Sachkundetrainer\Backend\Repository\CommentRepository;
    use LetsShoot\Sachkundetrainer\Backend\Repository\QuestionRepository;

    /**
     * Class CommentControl
     * @package LetsShoot\Sachkundetrainer\Backend\Control
     */
    class CommentControl extends Control {
        /**
         * CommentControl constructor.
         */
        public function __construct() {
            parent::__construct();

            // Action
            if(!method_exists($this->getResponse(),'getErrorCode')) {
                $action = $this->__getRequestVar('action');
                // Check if action parameter is set, if not set error
                if ($action !== null) {
                    if (method_exists($this, $action)) {
                        $this->getLogger()->Log(APPLICATION_LOG_LEVEL_DEBUG, "CALL " . get_class($this) . '->' . $action . ': ' . print_r($this->getRequest(), true));
                        $this->$action();
                    } else {
                        $Error = new ErrorResponse();
                        $Error->setErrorActionNotImplemented();
                        $this->setResponse($Error);
                        $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
                    }
                } else {
                    $Error = new ErrorResponse();
                    $Error->setErrorNoActionSet();
                    $this->setResponse($Error);
                    $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
                }
            }

            // Mark end of request in log
            $this->getLogger()->MarkEndOfRequest();
        }

        public function Delete() {
            if($this->getUser()->getUserUserlevel() < APPLICATION_USERLEVEL_USER) {
                $Error = new ErrorResponse();
                $Error -> setErrorUserrightsMissing();
                $this->setResponse($Error);
                $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
            }
            else {
                $CommentRepository = new CommentRepository();
                if ($this->__getRequestVar('id') !== null && !empty($this->__getRequestVar('id'))) {
                    $CommentModel = $CommentRepository->findById( $this->__getRequestVar('id') );
                    if($CommentModel) {
                        // Check right to delete comment
                        if($this->getUser()->getUserUserlevel() == APPLICATION_USERLEVEL_ADMIN || $this->getUser()->getUserId() == $CommentModel->getCommentUserId()) {
                            if ($CommentRepository->deleteEntryById($CommentModel)) {
                                $this->setResponse($CommentModel);
                                $this->getLogger()->Log(APPLICATION_LOG_LEVEL_DEBUG, $CommentModel);
                            } else {
                                $Error = new ErrorResponse();
                                $Error->setErrorDeleteObject();
                                $this->setResponse($Error);
                                $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
                            }
                        }
                        else {
                            $Error = new ErrorResponse();
                            $Error -> setErrorUserrightsMissing();
                            $this->setResponse($Error);
                            $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
                        }
                    }
                    else {
                        $Error = new ErrorResponse();
                        $Error->setErrorReadingObject();
                        $this->setResponse($Error);
                        $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
                    }
                } else {
                    $Error = new ErrorResponse();
                    $Error->setErrorIdParameterEmpty();
                    $this->setResponse($Error);
                    $this->getLogger()->Log(APPLICATION_LOG_LEVEL_INFO, $Error->getErrorMsg());
                }
            }
        }

        public function Add()
        {
            if($this->getUser()->getUserUserlevel() < APPLICATION_USERLEVEL_USER) {
                $Error = new ErrorResponse();
                $Error -> setErrorUserrightsMissing();
                $this->setResponse($Error);
                $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
            }
            else {
                $data = $this->getRequest();
                // Remove forbidden fields
                unset($data['comment_id']);
                // Set user
                $data['comment_user_id'] = $this->getUser()->getUserId();
                // Set timestamp
                $data['comment_timestamp'] = time();

                // Check required fields
                if($this->__getRequestVar('comment_question_id')) {
                    // Check question
                    $QuestionRepository = new QuestionRepository();
                    $question = $QuestionRepository->findById($this->__getRequestVar('comment_question_id'));
                    if($question) {
                        // Repo
                        $CommentRepository = new CommentRepository();

                        // Gen object
                        $CommentModel = new CommentModel();
                        foreach ($CommentRepository->getFieldlist() AS $fdef) {
                            if (isset($data[$fdef['field']])) {
                                $setMethod = $CommentRepository->__getMethodNameFromFieldname($fdef['field'], 'set');
                                $CommentModel->$setMethod($data[$fdef['field']]);
                            }
                        }

                        // Insert
                        $insertId = $CommentRepository->insertEntry($CommentModel);
                        if ($insertId) {
                            $DataObject = $CommentRepository->findById($insertId);
                            $this->setResponse($DataObject);
                            $this->getLogger()->Log(APPLICATION_LOG_LEVEL_DEBUG, $DataObject);
                        } else {
                            $Error = new ErrorResponse();
                            $Error->setErrorAddingObject();
                            $this->setResponse($Error);
                            $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
                        }
                    }
                    else {
                        $Error = new ErrorResponse();
                        $Error -> setErrorReadingObject();
                        $this->setResponse($Error);
                        $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
                    }
                }
                else {
                    $Error = new ErrorResponse();
                    $Error -> setErrorParametersMissing();
                    $this->setResponse($Error);
                    $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
                }
            }
        }

        public function Update() {
            if($this->getUser()->getUserUserlevel() < APPLICATION_USERLEVEL_USER) {
                $Error = new ErrorResponse();
                $Error -> setErrorUserrightsMissing();
                $this->setResponse($Error);
                $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
            }
            else {
                if ($this->__getRequestVar('id') !== null && !empty($this->__getRequestVar('id'))) {
                    $data = $this->getRequest();
                    // Remove forbidden fields
                    unset($data['comment_id']);
                    unset($data['comment_question_id']);
                    unset($data['comment_user_id']);

                    // Check question
                    $QuestionRepository = new QuestionRepository();
                    $question = $QuestionRepository->findById($this->__getRequestVar('comment_question_id'));
                    if($question) {
                        $CommentRepository = new CommentRepository();
                        $CommentModel = $CommentRepository->findById($this->__getRequestVar('id'));
                        if ($CommentModel) {
                            // Check right to update comment
                            if($this->getUser()->getUserUserlevel() == APPLICATION_USERLEVEL_ADMIN || $this->getUser()->getUserId() == $CommentModel->getCommentUserId()) {
                                // Set data
                                foreach ($CommentRepository->getFieldlist() AS $fdef) {
                                    if (isset($data[$fdef['field']])) {
                                        $setMethod = $CommentRepository->__getMethodNameFromFieldname($fdef['field'], 'set');
                                        $CommentModel->$setMethod($data[$fdef['field']]);
                                    }
                                }
                                // Update
                                if ($CommentRepository->updateEntry($CommentModel)) {
                                    $DataObject = $CommentRepository->findById($this->__getRequestVar('id'));
                                    $this->setResponse($DataObject);
                                    $this->getLogger()->Log(APPLICATION_LOG_LEVEL_DEBUG, $DataObject);
                                } else {
                                    $Error = new ErrorResponse();
                                    $Error->setErrorUpdateObject();
                                    $this->setResponse($Error);
                                    $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
                                }
                            } else {
                                $Error = new ErrorResponse();
                                $Error -> setErrorUserrightsMissing();
                                $this->setResponse($Error);
                                $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
                            }
                        } else {
                            $Error = new ErrorResponse();
                            $Error->setErrorReadingObject();
                            $this->setResponse($Error);
                            $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
                        }
                    } else {
                        $Error = new ErrorResponse();
                        $Error->setErrorReadingObject();
                        $this->setResponse($Error);
                        $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
                    }
                } else {
                    $Error = new ErrorResponse();
                    $Error->setErrorIdParameterEmpty();
                    $this->setResponse($Error);
                    $this->getLogger()->Log(APPLICATION_LOG_LEVEL_INFO, $Error->getErrorMsg());
                }
            }
        }

        public function FindAll() {
            if($this->getUser()->getUserUserlevel() < APPLICATION_USERLEVEL_USER) {
                $Error = new ErrorResponse();
                $Error -> setErrorUserrightsMissing();
                $this->setResponse($Error);
                $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
            }
            else {
                $CommentRepository = new CommentRepository();

                $sort_by = $this->__getRequestVar('sort_by');
                $sort_order = $this->__getRequestVar('sort_order');
                if($sort_by || $sort_order) {
                    $repoConf = $CommentRepository->getConfig();
                    if($sort_by) {
                        $repoConf['orderfield'] = $sort_by;
                    }
                    if($sort_order) {
                        $repoConf['orderby'] = $sort_order;
                    }
                    $CommentRepository->setConfig($repoConf);
                }
                $limit = intval($this->__getRequestVar('limit'));
                if($limit) {
                    $CommentRepository->applyLimit($limit);
                }

                $DataObject = $CommentRepository->findAll();
                // Remove data from response (security issues)
                if($DataObject && count($DataObject)) {
                    foreach($DataObject AS $key => $c) {
                        $c->getUser()->setUserEmail('');
                        $c->getUser()->setUserCountWrong(0);
                        $c->getUser()->setUserCountRight(0);
                    }
                }
                $this->setResponse($DataObject);
                $this->getLogger()->Log(APPLICATION_LOG_LEVEL_DEBUG, $DataObject);
            }
        }

        public function FindById() {
            if($this->getUser()->getUserUserlevel() < APPLICATION_USERLEVEL_GUEST) {
                $Error = new ErrorResponse();
                $Error -> setErrorUserrightsMissing();
                $this->setResponse($Error);
                $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
            }
            else {
                $CommentRepository = new CommentRepository();
                if ($this->__getRequestVar('id') !== null && !empty($this->__getRequestVar('id'))) {
                    $DataObject = $CommentRepository->findById($this->__getRequestVar('id'));
                    if($DataObject) {
                        $this->setResponse($DataObject);
                        // Remove data from response (security issues)
                        if($DataObject) {
                            $DataObject->getUser()->setUserEmail('');
                            $DataObject->getUser()->setUserCountWrong(0);
                            $DataObject->getUser()->setUserCountRight(0);
                        }
                        $this->getLogger()->Log(APPLICATION_LOG_LEVEL_DEBUG, $DataObject);
                    }
                    else {
                        $Error = new ErrorResponse();
                        $Error->setErrorReadingObject();
                        $this->setResponse($Error);
                        $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
                    }
                } else {
                    $Error = new ErrorResponse();
                    $Error->setErrorIdParameterEmpty();
                    $this->setResponse($Error);
                    $this->getLogger()->Log(APPLICATION_LOG_LEVEL_INFO, $Error->getErrorMsg());
                }
            }
        }

        public function FindByQuestionId() {
            if($this->getUser()->getUserUserlevel() < APPLICATION_USERLEVEL_GUEST) {
                $Error = new ErrorResponse();
                $Error -> setErrorUserrightsMissing();
                $this->setResponse($Error);
                $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
            }
            else {
                $CommentRepository = new CommentRepository();
                if ($this->__getRequestVar('id') !== null && !empty($this->__getRequestVar('id'))) {
                    $DataObject = $CommentRepository->findByFieldvalue('comment_question_id', $this->__getRequestVar('id'));
                    if($DataObject) {
                        $this->setResponse($DataObject);
                        // Remove data from response (security issues)
                        if($DataObject && count($DataObject)) {
                            foreach($DataObject AS $key => $c) {
                                $c->getUser()->setUserEmail('');
                                $c->getUser()->setUserCountWrong(0);
                                $c->getUser()->setUserCountRight(0);
                            }
                        }
                        $this->getLogger()->Log(APPLICATION_LOG_LEVEL_DEBUG, $DataObject);
                    }
                    else {
                        $Error = new ErrorResponse();
                        $Error->setErrorReadingObject();
                        $this->setResponse($Error);
                        $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
                    }
                } else {
                    $Error = new ErrorResponse();
                    $Error->setErrorIdParameterEmpty();
                    $this->setResponse($Error);
                    $this->getLogger()->Log(APPLICATION_LOG_LEVEL_INFO, $Error->getErrorMsg());
                }
            }
        }
    }
