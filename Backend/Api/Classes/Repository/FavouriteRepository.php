<?php
    namespace LetsShoot\Sachkundetrainer\Backend\Repository;

    use LetsShoot\Sachkundetrainer\Backend\Model\FavouriteModel;
    use LetsShoot\Sachkundetrainer\Backend\Repository;

    /**
     * Class FavouriteRepository
     * @package LetsShoot\Sachkundetrainer\Backend\Repository
     */
    class FavouriteRepository extends Repository {
        /**
         * @var array $config array with table definitions for repo base class
         */
        private $repoConfig = array(
            'datatable' => 'favourite',
            'idfield' => 'favourite_id',
            'orderfield' => 'favourite_id',
            'orderby' => 'ASC',
            'model' => 'FavouriteModel',
        );
        public function __construct() {
            parent::__construct($this->repoConfig);
        }

        /**
         * Find favourite by user and question_id
         * @param int $user_id
         * @param int $question_id
         * @return FavouriteModel|null
         */
        public function FindByQuestionId(int $user_id, int $question_id): ?FavouriteModel {
            $favourite = $this->findByConditions(array(
                'user_id' => $user_id,
                'question_id' => $question_id
            ), 'AND');
            return $favourite[0] ? $favourite[0] : null;
        }
    }
