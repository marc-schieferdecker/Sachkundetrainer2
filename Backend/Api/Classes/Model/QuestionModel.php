<?php
    namespace LetsShoot\Sachkundetrainer\Backend\Model;

    /**
     * Class QuestionModel
     * @package LetsShoot\Sachkundetrainer\Backend\Model
     */
    class QuestionModel implements \JsonSerializable {
        /**
         * @var int $question_id
         */
        private $question_id;

        /**
         * @var int $question_topic_id
         */
        private $question_topic_id;

        /**
         * @var int $question_subtopic_id
         */
        private $question_subtopic_id;

        /**
         * @var string $question_number
         */
        private $question_number;

        /**
         * @var string $question_text
         */
        private $question_text;

        /**
         * @var int $question_count_wrong
         */
        private $question_count_wrong;

        /**
         * @var int $question_count_right
         */
        private $question_count_right;

        /**
         * @var array $answeres
         */
        private $answeres;

        /**
         * @var TopicModel $topic
         */
        private $topic;

        /**
         * @var SubtopicModel $subtopic
         */
        private $subtopic;

        /**
         * @var array $comments
         */
        private $comments;

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
        public function getQuestionId(): int
        {
            return $this->question_id;
        }

        /**
         * @param int $question_id
         */
        public function setQuestionId(int $question_id): void
        {
            $this->question_id = $question_id;
        }

        /**
         * @return int
         */
        public function getQuestionTopicId(): int
        {
            return $this->question_topic_id;
        }

        /**
         * @param int $question_topic_id
         */
        public function setQuestionTopicId(int $question_topic_id): void
        {
            $this->question_topic_id = $question_topic_id;
        }

        /**
         * @return int
         */
        public function getQuestionSubtopicId(): int
        {
            return $this->question_subtopic_id;
        }

        /**
         * @param int $question_subtopic_id
         */
        public function setQuestionSubtopicId(int $question_subtopic_id): void
        {
            $this->question_subtopic_id = $question_subtopic_id;
        }

        /**
         * @return string
         */
        public function getQuestionNumber(): string
        {
            return $this->question_number;
        }

        /**
         * @param string $question_number
         */
        public function setQuestionNumber(string $question_number): void
        {
            $this->question_number = $question_number;
        }

        /**
         * @return string
         */
        public function getQuestionText(): string
        {
            return $this->question_text;
        }

        /**
         * @param string $question_text
         */
        public function setQuestionText(string $question_text): void
        {
            $this->question_text = $question_text;
        }

        /**
         * @return int
         */
        public function getQuestionCountWrong(): int
        {
            return $this->question_count_wrong;
        }

        /**
         * @param int $question_count_wrong
         */
        public function setQuestionCountWrong(int $question_count_wrong): void
        {
            $this->question_count_wrong = $question_count_wrong;
        }

        /**
         * @return int
         */
        public function getQuestionCountRight(): int
        {
            return $this->question_count_right;
        }

        /**
         * @param int $question_count_right
         */
        public function setQuestionCountRight(int $question_count_right): void
        {
            $this->question_count_right = $question_count_right;
        }

        /**
         * @return array
         */
        public function getAnsweres(): ?array
        {
            return $this->answeres;
        }

        /**
         * @param array $answeres
         */
        public function setAnsweres(array $answeres): void
        {
            $this->answeres = $answeres;
        }

        /**
         * @return TopicModel
         */
        public function getTopic(): TopicModel
        {
            return $this->topic;
        }

        /**
         * @param TopicModel $topic
         */
        public function setTopic(TopicModel $topic): void
        {
            $this->topic = $topic;
        }

        /**
         * @return SubtopicModel
         */
        public function getSubtopic(): SubtopicModel
        {
            return $this->subtopic;
        }

        /**
         * @param SubtopicModel $subtopic
         */
        public function setSubtopic(SubtopicModel $subtopic): void
        {
            $this->subtopic = $subtopic;
        }

        /**
         * @return array
         */
        public function getComments(): ?array
        {
            return $this->comments;
        }

        /**
         * @param array $comments
         */
        public function setComments(array $comments): void
        {
            $this->comments = $comments;
        }
    }
