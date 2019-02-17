<?php
    namespace LetsShoot\Sachkundetrainer\Backend\Model;

    /**
     * Class TopicModel
     * @package LetsShoot\Sachkundetrainer\Backend\Model
     */
    class TopicModel implements \JsonSerializable {
        /**
         * @var int $topic_id
         */
        private $topic_id;

        /**
         * @var string $topic_name
         */
        private $topic_name;

        /**
         * @var string $topic_number
         */
        private $topic_number;

        /**
         * @var array $subtopics
         */
        private $subtopics;

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
        public function getTopicId(): int
        {
            return $this->topic_id;
        }

        /**
         * @param int $topic_id
         */
        public function setTopicId(int $topic_id): void
        {
            $this->topic_id = $topic_id;
        }

        /**
         * @return string
         */
        public function getTopicName(): string
        {
            return $this->topic_name;
        }

        /**
         * @param string $topic_name
         */
        public function setTopicName(string $topic_name): void
        {
            $this->topic_name = $topic_name;
        }

        /**
         * @return string
         */
        public function getTopicNumber(): string
        {
            return $this->topic_number;
        }

        /**
         * @param string $topic_number
         */
        public function setTopicNumber(string $topic_number): void
        {
            $this->topic_number = $topic_number;
        }

        /**
         * @return array
         */
        public function getSubtopics(): ?array
        {
            return $this->subtopics;
        }

        /**
         * @param array $subtopics
         */
        public function setSubtopics(array $subtopics): void
        {
            $this->subtopics = $subtopics;
        }
    }
