<?php
    namespace LetsShoot\Sachkundetrainer\Backend\Model;

    /**
     * Class AnswereModel
     * @package LetsShoot\Sachkundetrainer\Backend\Model
     */
    class AnswereModel implements \JsonSerializable {
        /**
         * @var int $answere_id
         */
        private $answere_id;

        /**
         * @var int $answere_question_id
         */
        private $answere_question_id;

        /**
         * @var string $answere_number
         */
        private $answere_number;

        /**
         * @var string $answere_choice
         */
        private $answere_choice;

        /**
         * @var string $answere_text
         */
        private $answere_text;

        /**
         * @var int $answere_correct
         */
        private $answere_correct;

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
        public function getAnswereId(): int
        {
            return $this->answere_id;
        }

        /**
         * @param int $answere_id
         */
        public function setAnswereId(int $answere_id): void
        {
            $this->answere_id = $answere_id;
        }

        /**
         * @return int
         */
        public function getAnswereQuestionId(): int
        {
            return $this->answere_question_id;
        }

        /**
         * @param int $answere_question_id
         */
        public function setAnswereQuestionId(int $answere_question_id): void
        {
            $this->answere_question_id = $answere_question_id;
        }

        /**
         * @return string
         */
        public function getAnswereNumber(): ?string
        {
            return $this->answere_number;
        }

        /**
         * @param string $answere_number
         */
        public function setAnswereNumber(string $answere_number): void
        {
            $this->answere_number = $answere_number;
        }

        /**
         * @return string
         */
        public function getAnswereChoice(): ?string
        {
            return $this->answere_choice;
        }

        /**
         * @param string $answere_choice
         */
        public function setAnswereChoice(string $answere_choice): void
        {
            $this->answere_choice = $answere_choice;
        }

        /**
         * @return string
         */
        public function getAnswereText(): ?string
        {
            return $this->answere_text;
        }

        /**
         * @param string $answere_text
         */
        public function setAnswereText(string $answere_text): void
        {
            $this->answere_text = $answere_text;
        }

        /**
         * @return int
         */
        public function getAnswereCorrect(): int
        {
            return $this->answere_correct;
        }

        /**
         * @param int $answere_correct
         */
        public function setAnswereCorrect(int $answere_correct): void
        {
            $this->answere_correct = $answere_correct;
        }
    }
