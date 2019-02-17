<?php
    namespace LetsShoot\Sachkundetrainer\Backend\Repository;

    use LetsShoot\Sachkundetrainer\Backend\Repository;

    /**
     * Class AnswereRepository
     * @package LetsShoot\Sachkundetrainer\Backend\Repository
     */
    class AnswereRepository extends Repository {
        /**
         * @var array $config array with table definitions for repo base class
         */
        private $repoConfig = array(
            'datatable' => 'answere',
            'idfield' => 'answere_id',
            'orderfield' => 'answere_number',
            'orderby' => 'ASC',
            'model' => 'AnswereModel'
        );
        public function __construct() {
            parent::__construct($this->repoConfig);
        }

        /**
         * @param int $question_id
         * @return array|bool
         */
        public function findByQuestionId(int $question_id) {
            return $this->findByFieldvalue('answere_question_id', $question_id);
        }
    }
