<?php
    namespace LetsShoot\Sachkundetrainer\Backend\Repository;

    use LetsShoot\Sachkundetrainer\Backend\Model\QuestionModel;
    use LetsShoot\Sachkundetrainer\Backend\Repository;

    /**
     * Class QuestionRepository
     * @package LetsShoot\Sachkundetrainer\Backend\Repository
     */
    class QuestionRepository extends Repository {
        /**
         * @var array $config array with table definitions for repo base class
         */
        private $repoConfig = array(
            'datatable' => 'question',
            'idfield' => 'question_id',
            'orderfield' => 'question_number',
            'orderby' => 'ASC',
            'model' => 'QuestionModel',
            'autoload_relations' => array(
                'answeres' => array(
                    'local_field' => 'question_id',
                    'foreign_field' => 'answere_question_id',
                    'foreign_repository' => 'AnswereRepository',
                    'result_is_array' => true
                ),
                'topic' => array(
                    'local_field' => 'question_topic_id',
                    'foreign_field' => 'topic_id',
                    'foreign_repository' => 'TopicRepository',
                    'result_is_array' => false
                ),
                'subtopic' => array(
                    'local_field' => 'question_subtopic_id',
                    'foreign_field' => 'subtopic_id',
                    'foreign_repository' => 'SubtopicRepository',
                    'result_is_array' => false
                ),
                'comments' => array(
                    'local_field' => 'question_id',
                    'foreign_field' => 'comment_question_id',
                    'foreign_repository' => 'CommentRepository',
                    'result_is_array' => true
                ),
            )
        );
        public function __construct() {
            parent::__construct($this->repoConfig);
        }

        /**
         * @param array $question_ids
         */
        public function restrictByQuestionIds(array $question_ids): void {
            $conf = $this->getConfig();
            $conf['add_where_sql'] .= " AND question.question_id NOT IN (".implode(',', $question_ids).") ";
            $this->setConfig($conf);
        }

        /**
         * @param int $user_id
         * @return QuestionModel|null
         */
        public function FindByRandomFavourite(int $user_id): ?QuestionModel {
            $conf = $this->getConfig();
            $conf['orderfield'] = 'RAND()';
            $conf['limit_sql'] = 'LIMIT 1';
            $conf['join_sql'] = "LEFT JOIN favourite ON favourite.question_id = question.question_id";
            $conf['add_where_sql'] .= " AND favourite.user_id = '$user_id' ";
            $this->setConfig($conf);

            $questions = $this->findAll();
            if($questions && is_array($questions) && count($questions)) {
                return $questions[0];
            }
            else {
                return null;
            }
        }

        /**
         * @param int $topic_id
         * @param int $subtopic_id
         * @return QuestionModel|null
         */
        public function FindByRandomHard(int $topic_id = 0, int $subtopic_id = 0): ?QuestionModel {
            $conf = $this->getConfig();
            $conf['orderfield'] = 'question_count_wrong';
            $conf['limit_sql'] = 'LIMIT 100';
            if($topic_id) {
                $conf['add_where_sql'] .= " AND question.question_topic_id = '$topic_id' ";
            }
            if($subtopic_id) {
                $conf['add_where_sql'] .= " AND question.question_subtopic_id = '$subtopic_id' ";
            }
            $this->setConfig($conf);

            $questions = $this->findAll();
            if($questions && is_array($questions) && count($questions)) {
                shuffle($questions);
                return $questions[0];
            }
            else {
                return null;
            }
        }

        /**
         * Find random question
         * @param int $topic_id
         * @param int $subtopic_id
         * @return QuestionModel|null
         */
        public function FindByRandom(int $topic_id = 0, int $subtopic_id = 0): ?QuestionModel {
            $conf = $this->getConfig();
            $conf['orderfield'] = 'RAND()';
            $conf['limit_sql'] = 'LIMIT 1';
            if($topic_id) {
                $conf['add_where_sql'] .= " AND question.question_topic_id = '$topic_id' ";
            }
            if($subtopic_id) {
                $conf['add_where_sql'] .= " AND question.question_subtopic_id = '$subtopic_id' ";
            }
            $this->setConfig($conf);

            $questions = $this->findAll();
            if($questions && is_array($questions) && count($questions)) {
                return $questions[0];
            }
            else {
                return null;
            }
        }

        /**
         * Increase value of field by 1
         * @param string $field
         * @param int $id
         * @return QuestionModel|null
         */
        public function IncreaseCountField(string $field, int $id): ?QuestionModel
        {
            $conf = $this->getConfig();
            $id = intval($id);
            try {
                $q = $this->prepare("UPDATE ".$conf['datatable']." SET $field = $field + 1 WHERE ".$conf['idfield']." = $id");
                try {
                    $this->beginTransaction();
                    $q->execute();
                    $this->commit();

                    $this->getLogger()->Log(APPLICATION_LOG_LEVEL_INFO, 'increased '.$conf['datatable'].'.'.$field.' by 1');

                    return $this->findById($id);
                }
                catch( \PDOException $e ) {
                    $this -> rollback();
                    return null;
                }
            }
            catch( \PDOException $e ) {
                return null;
            }
        }

        /**
         * @param int $limit
         */
        public function applyLimit(int $limit): void {
            $config = $this->getConfig();
            $config['limit_sql'] = "LIMIT 0,$limit";
            $this->setConfig($config);
        }
    }
