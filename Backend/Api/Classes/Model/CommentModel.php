<?php
    namespace LetsShoot\Sachkundetrainer\Backend\Model;

    /**
     * Class CommentModel
     * @package LetsShoot\Sachkundetrainer\Backend\Model
     */
    class CommentModel implements \JsonSerializable {
        /**
         * @var int $comment_id
         */
        private $comment_id;

        /**
         * @var int $comment_question_id
         */
        private $comment_question_id;

        /**
         * @var int $comment_user_id
         */
        private $comment_user_id;

        /**
         * @var string $comment_text;
         */
        private $comment_text;

        /**
         * @var int $comment_timestamp
         */
        private $comment_timestamp;

        /**
         * @var PublicuserModel $user
         */
        private $user;

        /**
         * convert to json parseable object
         */
        public function jsonSerialize() {
            $jsonObject = array();
            $vars = get_class_vars(get_class($this));
            foreach($vars AS $var => $val) {
                if(!is_array($this->$var)) {
                    $jsonObject[$var] = $this->$var;
                }
                else {
                    foreach($this->$var AS $item) {
                        $jsonObject[$var][] = $item->jsonSerialize();
                    }
                }
            }
            return $jsonObject;
        }

        /**
         * @return int
         */
        public function getCommentId(): int
        {
            return $this->comment_id;
        }

        /**
         * @param int $comment_id
         */
        public function setCommentId(int $comment_id): void
        {
            $this->comment_id = $comment_id;
        }

        /**
         * @return int
         */
        public function getCommentQuestionId(): int
        {
            return $this->comment_question_id;
        }

        /**
         * @param int $comment_question_id
         */
        public function setCommentQuestionId(int $comment_question_id): void
        {
            $this->comment_question_id = $comment_question_id;
        }

        /**
         * @return int
         */
        public function getCommentUserId(): int
        {
            return $this->comment_user_id;
        }

        /**
         * @param int $comment_user_id
         */
        public function setCommentUserId(int $comment_user_id): void
        {
            $this->comment_user_id = $comment_user_id;
        }

        /**
         * @return string
         */
        public function getCommentText(): ?string
        {
            return $this->comment_text;
        }

        /**
         * @param string $comment_text
         */
        public function setCommentText(string $comment_text): void
        {
            $this->comment_text = $comment_text;
        }

        /**
         * @return int
         */
        public function getCommentTimestamp(): int
        {
            return $this->comment_timestamp;
        }

        /**
         * @param int $comment_timestamp
         */
        public function setCommentTimestamp(int $comment_timestamp): void
        {
            $this->comment_timestamp = $comment_timestamp;
        }

        /**
         * @return PublicuserModel
         */
        public function getUser(): ?PublicuserModel
        {
            return $this->user;
        }

        /**
         * @param PublicuserModel $user
         */
        public function setUser(PublicuserModel $user): void
        {
            $this->user = $user;
        }
    }
