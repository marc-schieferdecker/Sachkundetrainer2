<?php
    namespace LetsShoot\Sachkundetrainer\Backend;
    if(!defined('APPLICATION_NAMESPACE')) {
        define('APPLICATION_NAMESPACE', 'LetsShoot\\Sachkundetrainer\\Backend');
    }
    
    /**
     * Class Repository
     * @package LetsShoot\Sachkundetrainer\Backend
     */
    class Repository extends \PDO {

        /**
         * @var string $engine
         */
        private $engine;

        /**
         * @var string $host
         */
        private $host;

        /**
         * @var string $database
         */
        private $database;

        /**
         * @var string $user
         */
        private $user;

        /**
         * @var string $pass
         */
        private $pass;

        /**
         * @var array $config
         */
        private $config = array(
            'datatable' => '',
            'language_table' => '',
            'language_fields' => array(),
            'idfield' => '',
            'orderfield' => '',
            'orderby' => 'ASC',
            'model' => '',
            'mm_relations' => array(
                'object_fieldname' => array(
                    'mm_table' => '',
                    'local_field' => '',
                    'foreign_field' => '',
                    'foreign_repository' => '',
                    'mm_idfield' => '',
                ),
            ),
            'autoload_relations' => array(
                'property_name' => array(
                    'local_field' => '',
                    'foreign_field' => '',
                    'foreign_repository' => '',
                    'result_is_array' => false,
                    'add_where_sql' => '',
                ),
            ),
            'join_sql' => '',
            'add_where_sql' => '',
            'limit_sql' => '',
            'order_with_subselect' => false,
            'add_select_sql' => '',
            'orderfield_subselect' => ''
        );

        /**
         * @var array $fieldlist
         */
        private $fieldlist;

        /**
         * @var Logger $logger
         */
        private $logger;

        /**
         * @var int $language_id
         */
        private $language_id;

        /**
         * Repository constructor.
         * @param array $config
         * @param int $language_id
         * @param string $engine
         */
        public function __construct( $config, $language_id = 0, $engine = 'mysql' ) {
            // Set logger
            $this->logger = new Logger();

            // Database and repo config
            $this->config = $config;
            $this->engine = $engine;
            $this->host = APPLICATION_DB_HOST;
            $this->database = APPLICATION_DB_DATABASE;
            $this->user = APPLICATION_DB_USER;
            $this->pass = APPLICATION_DB_PASSWORD;
            $dns = $this->engine.':dbname='.$this->database.";host=".$this->host;
            try {
                parent::__construct( $dns, $this->user, $this->pass, array(
                    \PDO::ATTR_PERSISTENT => false
                ));
            }
            catch( \PDOException $e ) {
                $this->getLogger()->Log(APPLICATION_LOG_LEVEL_CRITICAL, $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine());
            }

            // Set language id
            $this->setLanguageId($language_id);

            // Generate fieldlist from model
            try {
                $this->__generateFieldList();
            }
            catch( \Exception $e ) {
                $this->getLogger()->Log(APPLICATION_LOG_LEVEL_CRITICAL, $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine());
            }
        }

        /**
         * read fields from model class
         * @throws \Exception
         */
        protected function __generateFieldList() {
            $modelFile = APPLICATION_DIRECTORY . DIRECTORY_SEPARATOR . 'Api' . DIRECTORY_SEPARATOR . 'Classes' . DIRECTORY_SEPARATOR . 'Model' . DIRECTORY_SEPARATOR . $this->config['model'].'.php';
            if(file_exists($modelFile)) {
                $modelSource = file_get_contents($modelFile);
                if(preg_match_all('#@var ([a-z0-9]+) \$([a-z0-9_ ]+)#si', $modelSource, $fieldmatches)) {
                    $this -> fieldlist = array();
                    foreach($fieldmatches[0] AS $key => $match) {
                        $type = trim($fieldmatches[1][$key]);
                        $field = trim($fieldmatches[2][$key]);
                        if($field != $this -> config['idfield']) {
                            $this -> fieldlist[] = array('field'=>$field,'type'=>$type);
                        }
                    }
                }
                else {
                    throw new \Exception('no field matches in source file: '.$modelFile);
                }
            }
            else {
                throw new \Exception('model source file not found: '.$modelFile);
            }
        }

        /**
         * @param string $fieldname
         * @return string
         */
        public function __getFieldType(string $fieldname): string
        {
            // Check for id field
            if($fieldname == $this -> config['idfield']) return 'int';
            // Check other field
            foreach($this->fieldlist AS $fdef) {
                if($fdef['field']==$fieldname) {
                    return $fdef['type'];
                }
            }
            // Fail
            return '';
        }

        /**
         * @param string $fieldname
         * @return bool
         */
        public function __isAutoloadRelation(string $fieldname): bool
        {
            if(isset($this->config['autoload_relations'])) {
                return array_key_exists($fieldname, $this->config['autoload_relations']);
            }
            else {
                return false;
            }
        }

        /**
         * @param string $fieldname
         * @param string $prefix
         * @return string
         */
        public function __getMethodNameFromFieldname(string $fieldname,string $prefix) {
            // Build method name
            $fnameArr = explode('_', $fieldname);
            foreach($fnameArr AS $fk => $fn) {
                $fnameArr[$fk] = ucfirst($fn);
            }
            return $prefix . implode('',$fnameArr);
        }

        /**
         * public static function to get a method for a fieldname
         * @param string $fieldname
         * @param string $prefix
         * @return string
         */
        public static function getMethodNameFromFieldname(string $fieldname,string $prefix) {
            // Build method name
            $fnameArr = explode('_', $fieldname);
            foreach($fnameArr AS $fk => $fn) {
                $fnameArr[$fk] = ucfirst($fn);
            }
            return $prefix . implode('',$fnameArr);
        }

        /**
         * map an query result to an array containing real objects
         * @param array $results
         * @return array
         */
        public function __mapResultsToObjectsArray(array $results) {
            $modelInstance = APPLICATION_NAMESPACE.'\\Model\\'.$this->config['model'];
            $objectarray = array();
            foreach( $results AS $rkey => $result ) {
                // Create instance
                $objectarray[$rkey] = new $modelInstance();
                // Call setters
                foreach( $result AS $fname => $fvalue ) {
                    // Build method name
                    $set = $this->__getMethodNameFromFieldname($fname,'set');

                    // Set type
                    settype($fvalue, $this->__getFieldType($fname));

                    // Call method
                    if(method_exists($objectarray[$rkey], $set)) {
                        $objectarray[$rkey]->$set($fvalue);
                    }
                }
            }
            return $objectarray;
        }

        /**
         * @param $data
         * @param string $fieldName
         * @param $fieldValue
         * @param array $whereParams
         * @param string $logical
         * @return bool
         */
        public function deleteEntryByFieldvalue($data, string $fieldName, $fieldValue, array $whereParams = array(), string $logical = 'AND') {
            $whereParams[$fieldName] = $fieldValue;
            $pFields = array_keys($whereParams);
            $Execute = array();
            $WhereConditions = array();
            foreach($pFields AS $field) {
                $Execute[':'.$field] = $whereParams[$field];
                if( $this->__getFieldType($field) == 'string' ) {
                    $WhereConditions[] = "$field LIKE :$field";
                }
                else {
                    $WhereConditions[] = "$field = :$field";
                }
            }
            $params = implode( " $logical ", $WhereConditions );
            if($this->getLanguageId() && isset($this->config['language_table'])) {
                $Execute[':language_id'] = $this->getLanguageId();
                $sql = "DELETE FROM " . $this->config['language_table'] . " WHERE language_id=:language_id AND (" . $params . ");";
            }
            else {
                $sql = "DELETE FROM " . $this->config['datatable'] . " WHERE " . $params . ";";
            }
            $this->getLogger()->Log(APPLICATION_LOG_LEVEL_DEBUG, $sql);
            try {
                $q = $this->prepare($sql);
                try {
                    $this->beginTransaction();
                    $q->execute($Execute);
                    $this->commit();

                    // If working on default language and there is language content, delete language content
                    if($this->getLanguageId() == 0 && isset($this->config['language_table'])) {
                        $getIdFieldMethod = $this->__getMethodNameFromFieldname($this->config['idfield'], 'get');

                        $deleteId = $data->$getIdFieldMethod();
                        $lang_sql = "DELETE FROM " . $this->config['language_table'] . " WHERE ".$this->config['idfield']." = '$deleteId';";
                        $this->query($lang_sql);
                    }

                    // Check for mm relations and delete if working on default language
                    if(isset($this->config['mm_relations']) && count($this->config['mm_relations']) && $this->getLanguageId() == 0) {
                        foreach ($this->config['mm_relations'] AS $mm_fieldname => $mm_config) {
                            $getLocalIdFieldMethod = $this->__getMethodNameFromFieldname($mm_config['local_field'],'get');

                            // Remove mm relations
                            $deleteId = $data->$getLocalIdFieldMethod();
                            $mm_delete_sql = "DELETE FROM ".$mm_config['mm_table']." WHERE ".$mm_config['local_field']." = '$deleteId';";
                            $this->query($mm_delete_sql);
                        }
                    }

                    $this->getLogger()->Log(APPLICATION_LOG_LEVEL_INFO, 'deleted object(s) by field value ' . $fieldName . '->' . $fieldValue . ' in table ' . $this->config['datatable']);

                    return true;
                }
                catch( \PDOException $e ) {
                    $this -> rollback();
                    print "PDO error: " . $e->getMessage();
                    return false;
                }
            }
            catch( \PDOException $e ) {
                print "PDO error: " . $e->getMessage();
                return false;
            }
        }

        /**
         * @param object $data
         * @return bool
         */
        public function deleteEntryById($data)
        {
            $Execute = array();
            $getIdFieldMethod = $this->__getMethodNameFromFieldname($this->config['idfield'], 'get');
            $Execute[':' . $this->config['idfield']] = $data->$getIdFieldMethod();
            if($this->getLanguageId() && isset($this->config['language_table'])) {
                $Execute[':language_id'] = $this->getLanguageId();
                $sql = "DELETE FROM " . $this->config['language_table'] . " WHERE " . $this->config['idfield'] . "=:" . $this->config['idfield'] . " AND language_id=:language_id;";
            }
            else {
                $sql = "DELETE FROM " . $this->config['datatable'] . " WHERE " . $this->config['idfield'] . "=:" . $this->config['idfield'] . ";";
            }
            $this->getLogger()->Log(APPLICATION_LOG_LEVEL_DEBUG, $sql);
            try {
                $q = $this->prepare($sql);
                try {
                    $this->beginTransaction();
                    $q->execute($Execute);
                    $this->commit();

                    // If working on default language and there is language content, delete language content
                    if($this->getLanguageId() == 0 && isset($this->config['language_table'])) {
                        $getIdFieldMethod = $this->__getMethodNameFromFieldname($this->config['idfield'], 'get');

                        $deleteId = $data->$getIdFieldMethod();
                        $lang_sql = "DELETE FROM " . $this->config['language_table'] . " WHERE ".$this->config['idfield']." = '$deleteId';";
                        $this->query($lang_sql);
                    }

                    // Check for mm relations and delete if working on default language
                    if(isset($this->config['mm_relations']) && count($this->config['mm_relations']) && $this->getLanguageId() == 0) {
                        foreach ($this->config['mm_relations'] AS $mm_fieldname => $mm_config) {
                            $getLocalIdFieldMethod = $this->__getMethodNameFromFieldname($mm_config['local_field'],'get');

                            // Remove mm relations
                            $deleteId = $data->$getLocalIdFieldMethod();
                            $mm_delete_sql = "DELETE FROM ".$mm_config['mm_table']." WHERE ".$mm_config['local_field']." = '$deleteId';";
                            $this->query($mm_delete_sql);
                        }
                    }

                    $this->getLogger()->Log(APPLICATION_LOG_LEVEL_INFO, 'deleted object id ' . $data->$getIdFieldMethod() . ' in table ' . $this->config['datatable']);

                    return true;
                }
                catch( \PDOException $e ) {
                    $this -> rollback();
                    print "PDO error: " . $e->getMessage();
                    return false;
                }
            }
            catch( \PDOException $e ) {
                print "PDO error: " . $e->getMessage();
                return false;
            }
        }

        /**
         * @param object $data
         * @return bool
         */
        public function updateEntry($data) {
            $pFieldsValues = array();
            $Execute = array();
            $getIdFieldMethod = $this -> __getMethodNameFromFieldname($this->config['idfield'],'get');
            $Execute[':'.$this->config['idfield']] = $data->$getIdFieldMethod();
            foreach($this -> fieldlist AS $fdef) {
                if($fdef['type'] != 'array' && $fdef['field'] != $this->config['idfield'] && !$this->__isAutoloadRelation($fdef['field'])) {
                    // Prepare sql
                    $pFieldsValues[] = $fdef['field'].'=:'.$fdef['field'];

                    // Build method name
                    $get = $this->__getMethodNameFromFieldname($fdef['field'],'get');

                    // Get Values
                    if($data->$get() !== null) {
                        $Execute[':'.$fdef['field']] = $data->$get();
                    }
                    else {
                        if($fdef['type'] == 'int' || $fdef['type'] == 'integer' || $fdef['type'] == 'double' || $fdef['type'] == 'float') {
                            $Execute[':'.$fdef['field']] = 0;
                        }
                        else {
                            $Execute[':'.$fdef['field']] = '';
                        }
                    }
                }
            }
            if($this->getLanguageId() && isset($this->config['language_table'])) {
                $Execute[':language_id'] = $this->getLanguageId();
                $lc = $this->getLanguageContent($data->$getIdFieldMethod());
                if($lc) {
                    $sql = "UPDATE " . $this->config['language_table'] . " SET " . implode(', ', $pFieldsValues ) . " WHERE " . $this->config['idfield'] . "=:" .  $this->config['idfield'] . " AND language_id=:language_id;";
                }
                else {
                    $pFieldsValues[] = $this->config['idfield'].'=:'.$this->config['idfield'];
                    $pFieldsValues[] = 'language_id=:language_id';
                    $sql = "INSERT INTO " . $this->config['language_table'] . " SET " . implode(', ', $pFieldsValues );
                }
            }
            else {
                $sql = "UPDATE " . $this->config['datatable'] . " SET " . implode(', ', $pFieldsValues ) . " WHERE " . $this->config['idfield'] . "=:" .  $this->config['idfield'] . ";";
            }
            $this->getLogger()->Log(APPLICATION_LOG_LEVEL_DEBUG, $sql);
            try {
                $q = $this -> prepare( $sql );
                try {
                    $this -> beginTransaction();
                    $q -> execute( $Execute );
                    $this -> commit();

                    // Check for mm relations
                    if(isset($this->config['mm_relations']) && count($this->config['mm_relations'])) {
                        foreach ($this->config['mm_relations'] AS $mm_fieldname => $mm_config) {
                            $getLocalIdFieldMethod = $this->__getMethodNameFromFieldname($mm_config['local_field'],'get');
                            $getForeignIdFieldMethod = $this->__getMethodNameFromFieldname($mm_config['foreign_field'],'get');

                            // Remove old mm relations
                            $localId = $data->$getLocalIdFieldMethod();
                            $mm_delete_sql = "DELETE FROM ".$mm_config['mm_table']." WHERE ".$mm_config['local_field']." = '$localId';";
                            $this->query($mm_delete_sql);

                            $get = $this->__getMethodNameFromFieldname($mm_fieldname,'get');
                            if($data->$get() !== null && is_array($data->$get()) && count($data->$get())) {
                                $mm_data = $data->$get();
                                foreach($mm_data AS $mm_object) {
                                    $foreignId = $mm_object->$getForeignIdFieldMethod();
                                    $mm_sql = "INSERT INTO ".$mm_config['mm_table']." SET ".$mm_config['local_field']." = '$localId', ".$mm_config['foreign_field']." = '$foreignId';";
                                    $this->query($mm_sql);
                                }
                            }
                        }
                    }

                    $this->getLogger()->Log(APPLICATION_LOG_LEVEL_INFO, 'updated object id ' . $data->$getIdFieldMethod() . ' in table ' . $this->config['datatable']);

                    return true;
                }
                catch( \PDOException $e ) {
                    $this -> rollback();
                    print "PDO error: " . $e->getMessage();
                    return false;
                }
            }
            catch( \PDOException $e ) {
                print "PDO error: " . $e->getMessage();
                return false;
            }
        }

        /**
         * @param object $data
         * @return bool|int
         */
        public function insertEntry($data) {
            $pFields = array();
            $pValues = array();
            $Execute = array();
            foreach($this -> fieldlist AS $fdef) {
                if($fdef['type'] != 'array' && !$this->__isAutoloadRelation($fdef['field'])) {
                    // Prepare sql
                    $pFields[] = $fdef['field'];
                    $pValues[] = ':'.$fdef['field'];

                    // Build method name
                    $get = $this->__getMethodNameFromFieldname($fdef['field'],'get');

                    // Get Values
                    if($data->$get() !== null) {
                        $Execute[':'.$fdef['field']] = $data->$get();
                    }
                    else {
                        if($fdef['type'] == 'int' || $fdef['type'] == 'integer' || $fdef['type'] == 'double' || $fdef['type'] == 'float') {
                            $Execute[':'.$fdef['field']] = 0;
                        }
                        else {
                            $Execute[':'.$fdef['field']] = '';
                        }
                    }
                }
            }
            $sql = "INSERT INTO " . $this->config['datatable'] . " (`" . implode('`,`', $pFields) . "`) VALUES (" . implode(',', $pValues) . ");";
            $this->getLogger()->Log(APPLICATION_LOG_LEVEL_DEBUG, $sql);
            try {
                $q = $this -> prepare( $sql );
                try {
                    $this -> beginTransaction();
                    $q -> execute( $Execute );
                    $insertId = $this -> lastInsertId();
                    $this -> commit();

                    // Check for mm relations
                    if(isset($this->config['mm_relations']) && count($this->config['mm_relations'])) {
                        foreach ($this->config['mm_relations'] AS $mm_fieldname => $mm_config) {
                            $getIdFieldMethod = $this->__getMethodNameFromFieldname($mm_config['foreign_field'],'get');

                            $get = $this->__getMethodNameFromFieldname($mm_fieldname,'get');
                            if($data->$get() !== null && is_array($data->$get()) && count($data->$get())) {
                                $mm_data = $data->$get();
                                foreach($mm_data AS $mm_object) {
                                    $foreignId = $mm_object->$getIdFieldMethod();
                                    $mm_sql = "INSERT INTO ".$mm_config['mm_table']." SET ".$mm_config['local_field']." = '$insertId', ".$mm_config['foreign_field']." = '$foreignId';";
                                    $this->query($mm_sql);
                                }
                            }
                        }
                    }

                    $this->getLogger()->Log(APPLICATION_LOG_LEVEL_INFO, 'added object with id ' . $insertId . ' to table ' . $this->config['datatable']);

                    return $insertId;
                }
                catch( \PDOException $e ) {
                    $this -> rollback();
                    print "PDO error: " . $e->getMessage();
                    return false;
                }
            }
            catch( \PDOException $e ) {
                print "PDO error: " . $e->getMessage();
                return false;
            }
        }

        /**
         * @param array $whereParams array('fieldname'=>'value', ...)
         * @param string $logical
         * @return array|bool
         */
        public function findByConditions(array $whereParams, string $logical = 'AND') {
            $result = $this -> findAll($whereParams, $logical);
            if($result) {
                return $result;
            }
            else {
                return false;
            }
        }

        /**
         * @param string $fieldName
         * @param mixed $fieldValue fields of type string can contain % as placeholder
         * @param array $whereParams
         * @param string $logical (default = AND)
         * @return array|bool
         */
        public function findByFieldvalue(string $fieldName, $fieldValue, array $whereParams = array(), string $logical = 'AND') {
            $whereParams[$fieldName] = $fieldValue;
            $result = $this -> findAll($whereParams, $logical);
            if($result) {
                return $result;
            }
            else {
                return false;
            }
        }

        /**
         * @param int $idFieldValue
         * @param array $whereParams
         * @return bool|mixed
         */
        public function findById(int $idFieldValue, array $whereParams = array()) {
            $whereParams[$this->config['idfield']] = $idFieldValue;
            $result = $this -> findAll($whereParams);
            if($result) {
                return $result[0];
            }
            else {
                return false;
            }
        }

        /**
         * Get object from language table
         * @param int $idFieldValue
         * @return int|bool
         */
        public function getLanguageContent(int $idFieldValue) {
            $Execute = array();
            $Execute[':' . $this->config['idfield']] = $idFieldValue;
            $Execute[':language_id'] = $this->getLanguageId();
            $sql = "SELECT * FROM ".$this->config['language_table']." WHERE ".$this->config['idfield']."=:".$this->config['idfield']." AND language_id=:language_id LIMIT 1;";
            try {
                $q = $this -> prepare( $sql );
                try {
                    $q -> execute( $Execute );
                    $data = $q -> fetchAll(\PDO::FETCH_ASSOC);
                    if(count($data)) {
                        $dataobject = $this->__mapResultsToObjectsArray($data);
                        return $dataobject[0];
                    }
                    else {
                        return false;
                    }
                }
                catch( \PDOException $e ) {
                    $this->getLogger()->Log(APPLICATION_LOG_LEVEL_CRITICAL, "PDO error: " . $e->getMessage());
                    print "PDO error: " . $e->getMessage();
                    return false;
                }
            }
            catch( \PDOException $e ) {
                $this->getLogger()->Log(APPLICATION_LOG_LEVEL_CRITICAL, "PDO error: " . $e->getMessage());
                print "PDO error: " . $e->getMessage();
                return false;
            }
        }

        /**
         * Query data, but get count(idfield)
         * @param array $whereParams
         * @param string $logical
         * @param array $restrictParams additional parameters to restrict results with logical and (e.g.: by mandant_id)
         * @return int|bool
         */
        public function countAll(array $whereParams = array(), string $logical = 'AND', array $restrictParams = array()) {
            $Execute = array();
            $orderfield = preg_replace('/[ ]+/', '', $this->config['orderfield']);
            $orderby = preg_replace('/[ ]+/', '', $this->config['orderby']);

            // Build restrict parameters
            $RestrictWhereConditions = array();
            if(count($restrictParams)) {
                $rFields = array_keys($restrictParams);
                foreach($rFields AS $field) {
                    if(strstr($field, '.') === false) {
                        if($restrictParams[$field] === null) {
                            $RestrictWhereConditions[] = $this->config['datatable'].".$field IS NULL";
                        }
                        else {
                            $Execute[':'.$field] = $restrictParams[$field];
                            $RestrictWhereConditions[] = $this->config['datatable'].".$field = :$field";
                        }
                    }
                    else {
                        if($restrictParams[$field] === null) {
                            $RestrictWhereConditions[] = "$field IS NULL";
                        }
                        else {
                            $Execute[':'.$field] = $restrictParams[$field];
                            $RestrictWhereConditions[] = "$field = :$field";
                        }
                    }
                }
                $restrict = implode( " AND ", $RestrictWhereConditions );
            }
            else {
                $restrict = ' 1 ';
            }

            // Build query
            $join_sql = isset($this->config['join_sql']) ? $this->config['join_sql'] : '';
            $limit_sql = isset($this->config['limit_sql']) ? $this->config['limit_sql'] : '';
            $add_where_sql = isset($this->config['add_where_sql']) ? $this->config['add_where_sql'] : '';
            if(count($whereParams)) {
                $pFields = array_keys($whereParams);
                $WhereConditions = array();
                foreach($pFields AS $field) {
                    $Execute[':'.$field] = $whereParams[$field];
                    if(strstr($field, '.') === false) {
                        if( $this->__getFieldType($field) == 'string' ) {
                            $WhereConditions[] = $this->config['datatable'] . ".$field LIKE :$field";
                        }
                        else {
                            $WhereConditions[] = $this->config['datatable'] . ".$field = :$field";
                        }
                    }
                    else {
                        if( $this->__getFieldType($field) == 'string' ) {
                            $WhereConditions[] = "$field LIKE :$field";
                        }
                        else {
                            $WhereConditions[] = "$field = :$field";
                        }
                    }
                }
                $params = implode( " $logical ", $WhereConditions );
                if(empty($this->config['idfield'])) {
                    $sql = "SELECT " . $this->config['datatable'] . "." . $this->config['idfield'] . " FROM " . $this->config['datatable'] . " " . $join_sql . " WHERE (" . $params . ") AND $restrict $add_where_sql ORDER BY $orderfield $orderby " . $limit_sql . ";";
                }
                else {
                    $sql = "SELECT " . $this->config['datatable'] . "." . $this->config['idfield'] . " FROM " . $this->config['datatable'] . " " . $join_sql . " WHERE (" . $params . ") AND $restrict $add_where_sql GROUP BY " . $this->config['datatable'] . "." . $this->config['idfield'] . " ORDER BY $orderfield $orderby " . $limit_sql . ";";
                }
            }
            else {
                if(empty($this->config['idfield'])) {
                    $sql = "SELECT " . $this->config['datatable'] . "." . $this->config['idfield'] . " FROM " . $this->config['datatable'] . " " . $join_sql . " WHERE $restrict $add_where_sql ORDER BY $orderfield $orderby " . $limit_sql . ";";
                }
                else {
                    $sql = "SELECT " . $this->config['datatable'] . "." . $this->config['idfield'] . " FROM " . $this->config['datatable'] . " " . $join_sql . " WHERE $restrict $add_where_sql GROUP BY " . $this->config['datatable'] . "." . $this->config['idfield'] . " ORDER BY $orderfield $orderby " . $limit_sql . ";";
                }
            }
            $this->getLogger()->Log(APPLICATION_LOG_LEVEL_DEBUG, $sql);
            try {
                $q = $this -> prepare( $sql );
                try {
                    $q -> execute( $Execute );
                    return $q->rowCount();
                }
                catch( \PDOException $e ) {
                    $this->getLogger()->Log(APPLICATION_LOG_LEVEL_CRITICAL, "PDO error: " . $e->getMessage());
                    print "PDO error: " . $e->getMessage();
                    return false;
                }
            }
            catch( \PDOException $e ) {
                $this->getLogger()->Log(APPLICATION_LOG_LEVEL_CRITICAL, "PDO error: " . $e->getMessage());
                print "PDO error: " . $e->getMessage();
                return false;
            }
        }

        /**
         * Query data by SQL
         * @param string $sql
         * @return array|bool
         */
        public function findBySQL(string $sql) {
            $this->getLogger()->Log(APPLICATION_LOG_LEVEL_DEBUG, $sql);
            try {
                $q = $this -> prepare( $sql );
                try {
                    $q -> execute();
                    $data = $q -> fetchAll(\PDO::FETCH_ASSOC);
                    $dataobject = $this->__mapResultsToObjectsArray($data);

                    // Check for mm relations
                    if(isset($this->config['mm_relations']) && count($this->config['mm_relations'])) {
                        foreach($this->config['mm_relations'] AS $mm_fieldname => $mm_config) {
                            $repoInstance = APPLICATION_NAMESPACE.'\\Repository\\'.$mm_config['foreign_repository'];
                            /**
                             * @var Repository $ForeignRepository
                             */
                            $ForeignRepository = new $repoInstance();
                            $addMMFieldMethod = $this->__getMethodNameFromFieldname($mm_fieldname,'add');

                            foreach( $dataobject AS $objectkey => $object ) {
                                // Load foreign ids
                                $getIdFieldMethod = $this->__getMethodNameFromFieldname($this->config['idfield'],'get');
                                $local_id = $object->$getIdFieldMethod();
                                $order = isset($mm_config['mm_idfield']) ? "ORDER BY " . $mm_config['mm_idfield'] . " ASC" : "";
                                $mm_sql = "SELECT " . $mm_config['foreign_field'] . " FROM " . $mm_config['mm_table'] . " WHERE " . $mm_config['local_field'] . " = '$local_id' $order;";
                                foreach ($this->query($mm_sql,\PDO::FETCH_ASSOC) AS $results) {
                                    $foreign_id = $results[$mm_config['foreign_field']];
                                    $foreignObject = $ForeignRepository -> findById($foreign_id);
                                    if($foreignObject) {
                                        $object->$addMMFieldMethod($foreignObject);
                                    }
                                }
                            }
                        }
                    }

                    // Check for autoload relations
                    if(isset($this->config['autoload_relations']) && count($this->config['autoload_relations'])) {
                        foreach ($this->config['autoload_relations'] AS $autoload_fieldname => $autoload_config) {
                            $repoInstance = APPLICATION_NAMESPACE . '\\Repository\\' . $autoload_config['foreign_repository'];
                            /**
                             * @var Repository $ForeignRepository
                             */
                            $ForeignRepository = new $repoInstance();
                            $getFieldValueMethod = $this->__getMethodNameFromFieldname($autoload_config['local_field'],'get');
                            $setLocalFieldMethod = $this->__getMethodNameFromFieldname($autoload_fieldname, 'set');

                            // Add add_where_sql to repo config of autoloaded repository
                            $autoload_add_where_sql = isset($autoload_config['add_where_sql']) && !empty($autoload_config['add_where_sql']) ? $autoload_config['add_where_sql'] : false;
                            if($autoload_add_where_sql) {
                                $config = $ForeignRepository->getConfig();
                                $config['add_where_sql'] = $autoload_add_where_sql;
                                $ForeignRepository->setConfig($config);
                            }

                            foreach( $dataobject AS $objectkey => $object ) {
                                $relationObject = $ForeignRepository->findByFieldvalue($autoload_config['foreign_field'],$object->$getFieldValueMethod());
                                if($relationObject) {
                                    if($autoload_config['result_is_array'] === true) {
                                        $object->$setLocalFieldMethod($relationObject);
                                    }
                                    else {
                                        $object->$setLocalFieldMethod($relationObject[0]);
                                    }
                                }
                            }
                        }
                    }

                    // Overwrite with language content if language content is requested
                    if($this->getLanguageId() && isset($this->config['language_table']) && isset($this->config['language_fields'])) {
                        foreach($dataobject AS $objectkey => $object) {
                            $getIdFieldMethod = $this->__getMethodNameFromFieldname($this->config['idfield'], 'get');
                            $lc = $this->getLanguageContent($object->$getIdFieldMethod());
                            if($lc) {
                                foreach($this->config['language_fields'] AS $lcfield) {
                                    $setLcFieldMethod = $this->__getMethodNameFromFieldname($lcfield, 'set');
                                    $getLcFieldMethod = $this->__getMethodNameFromFieldname($lcfield, 'get');
                                    $object->$setLcFieldMethod($lc->$getLcFieldMethod());
                                }
                            }
                        }
                    }

                    return $dataobject;
                }
                catch( \PDOException $e ) {
                    $this->getLogger()->Log(APPLICATION_LOG_LEVEL_CRITICAL, "PDO error: " . $e->getMessage());
                    print "PDO error: " . $e->getMessage();
                    return false;
                }
            }
            catch( \PDOException $e ) {
                $this->getLogger()->Log(APPLICATION_LOG_LEVEL_CRITICAL, "PDO error: " . $e->getMessage());
                print "PDO error: " . $e->getMessage();
                return false;
            }
        }

        /**
         * Query data
         * @param array $whereParams
         * @param string $logical
         * @param array $restrictParams additional parameters to restrict results with logical and (e.g.: by mandant_id)
         * @return array|bool
         */
        public function findAll(array $whereParams = array(), string $logical = 'AND', array $restrictParams = array()) {
            $Execute = array();
            $orderfield = preg_replace('/[ ]+/', '', $this->config['orderfield']);
            $orderby = preg_replace('/[ ]+/', '', $this->config['orderby']);

            // Build restrict parameters
            $RestrictWhereConditions = array();
            if(count($restrictParams)) {
                $rFields = array_keys($restrictParams);
                foreach($rFields AS $field) {
                    if(strstr($field, '.') === false) {
                        if($restrictParams[$field] === null) {
                            $RestrictWhereConditions[] = $this->config['datatable'].".$field IS NULL";
                        }
                        else {
                            $Execute[':'.$field] = $restrictParams[$field];
                            $RestrictWhereConditions[] = $this->config['datatable'].".$field = :$field";
                        }
                    }
                    else {
                        if($restrictParams[$field] === null) {
                            $RestrictWhereConditions[] = "$field IS NULL";
                        }
                        else {
                            $Execute[':'.$field] = $restrictParams[$field];
                            $RestrictWhereConditions[] = "$field = :$field";
                        }
                    }
                }
                $restrict = implode( " AND ", $RestrictWhereConditions );
            }
            else {
                $restrict = ' 1 ';
            }

            // Build query
            $join_sql = isset($this->config['join_sql']) ? $this->config['join_sql'] : '';
            $limit_sql = isset($this->config['limit_sql']) ? $this->config['limit_sql'] : '';
            $add_where_sql = isset($this->config['add_where_sql']) ? $this->config['add_where_sql'] : '';
            $add_select_sql = isset($this->config['add_select_sql']) ? ','.$this->config['add_select_sql'] : '';
            if(count($whereParams)) {
                $pFields = array_keys($whereParams);
                $WhereConditions = array();
                foreach($pFields AS $field) {
                    $Execute[':'.$field] = $whereParams[$field];
                    if(strstr($field, '.') === false) {
                        if( $this->__getFieldType($field) == 'string' ) {
                            $WhereConditions[] = $this->config['datatable'] . ".$field LIKE :$field";
                        }
                        else {
                            $WhereConditions[] = $this->config['datatable'] . ".$field = :$field";
                        }
                    }
                    else {
                        if( $this->__getFieldType($field) == 'string' ) {
                            $WhereConditions[] = "$field LIKE :$field";
                        }
                        else {
                            $WhereConditions[] = "$field = :$field";
                        }
                    }
                }
                $params = implode( " $logical ", $WhereConditions );
                if(empty($this->config['idfield'])) {
                    $sql = "SELECT " . $this->config['datatable'] . ".* $add_select_sql FROM " . $this->config['datatable'] . " " . $join_sql . " WHERE (" . $params . ") AND $restrict $add_where_sql ORDER BY $orderfield $orderby " . $limit_sql . ";";
                }
                else {
                    if($this->config['order_with_subselect']) {
                        $sql = "SELECT * FROM (
                              SELECT " . $this->config['datatable'] . ".* $add_select_sql FROM " . $this->config['datatable'] . " " . $join_sql . " WHERE (" . $params . ") AND $restrict $add_where_sql ORDER BY $orderfield
                            ) AS " . $this->config['datatable'] . "
                            GROUP BY " . $this->config['datatable'] . "." . $this->config['idfield'] . "
                            ORDER BY " . $this->config['orderfield_subselect'] . " $orderby " . $limit_sql . "
                        ;";
                    }
                    else {
                        $sql = "SELECT " . $this->config['datatable'] . ".* $add_select_sql FROM " . $this->config['datatable'] . " " . $join_sql . " WHERE (" . $params . ") AND $restrict $add_where_sql GROUP BY " . $this->config['datatable'] . "." . $this->config['idfield'] . " ORDER BY $orderfield $orderby " . $limit_sql . ";";
                    }
                }
            }
            else {
                if(empty($this->config['idfield'])) {
                    $sql = "SELECT " . $this->config['datatable'] . ".* $add_select_sql FROM " . $this->config['datatable'] . " " . $join_sql . " WHERE $restrict $add_where_sql ORDER BY $orderfield $orderby " . $limit_sql . ";";
                }
                else {
                    if($this->config['order_with_subselect']) {
                        $sql = "SELECT * FROM (
                              SELECT " . $this->config['datatable'] . ".* $add_select_sql FROM " . $this->config['datatable'] . " " . $join_sql . " WHERE $restrict $add_where_sql ORDER BY $orderfield
                            ) AS " . $this->config['datatable'] . "
                            GROUP BY " . $this->config['datatable'] . "." . $this->config['idfield'] . "
                            ORDER BY " . $this->config['orderfield_subselect'] . " $orderby " . $limit_sql . "
                        ;";
                    }
                    else {
                        $sql = "SELECT " . $this->config['datatable'] . ".* $add_select_sql FROM " . $this->config['datatable'] . " " . $join_sql . " WHERE $restrict $add_where_sql GROUP BY " . $this->config['datatable'] . "." . $this->config['idfield'] . " ORDER BY $orderfield $orderby " . $limit_sql . ";";
                    }
                }
            }
            $this->getLogger()->Log(APPLICATION_LOG_LEVEL_DEBUG, $sql);
            try {
                $q = $this -> prepare( $sql );
                try {
                    $q -> execute( $Execute );
                    $data = $q -> fetchAll(\PDO::FETCH_ASSOC);
                    $dataobject = $this->__mapResultsToObjectsArray($data);

                    // Check for mm relations
                    if(isset($this->config['mm_relations']) && count($this->config['mm_relations'])) {
                        foreach($this->config['mm_relations'] AS $mm_fieldname => $mm_config) {
                            $repoInstance = APPLICATION_NAMESPACE.'\\Repository\\'.$mm_config['foreign_repository'];
                            /**
                             * @var Repository $ForeignRepository
                             */
                            $ForeignRepository = new $repoInstance();
                            $addMMFieldMethod = $this->__getMethodNameFromFieldname($mm_fieldname,'add');

                            foreach( $dataobject AS $objectkey => $object ) {
                                // Load foreign ids
                                $getIdFieldMethod = $this->__getMethodNameFromFieldname($this->config['idfield'],'get');
                                $local_id = $object->$getIdFieldMethod();
                                $order = isset($mm_config['mm_idfield']) ? "ORDER BY " . $mm_config['mm_idfield'] . " ASC" : "";
                                $mm_sql = "SELECT " . $mm_config['foreign_field'] . " FROM " . $mm_config['mm_table'] . " WHERE " . $mm_config['local_field'] . " = '$local_id' $order;";
                                foreach ($this->query($mm_sql,\PDO::FETCH_ASSOC) AS $results) {
                                    $foreign_id = $results[$mm_config['foreign_field']];
                                    $foreignObject = $ForeignRepository -> findById($foreign_id);
                                    if($foreignObject) {
                                        $object->$addMMFieldMethod($foreignObject);
                                    }
                                }
                            }
                        }
                    }

                    // Check for autoload relations
                    if(isset($this->config['autoload_relations']) && count($this->config['autoload_relations'])) {
                        foreach ($this->config['autoload_relations'] AS $autoload_fieldname => $autoload_config) {
                            $repoInstance = APPLICATION_NAMESPACE . '\\Repository\\' . $autoload_config['foreign_repository'];
                            /**
                             * @var Repository $ForeignRepository
                             */
                            $ForeignRepository = new $repoInstance();
                            $getFieldValueMethod = $this->__getMethodNameFromFieldname($autoload_config['local_field'],'get');
                            $setLocalFieldMethod = $this->__getMethodNameFromFieldname($autoload_fieldname, 'set');

                            // Add add_where_sql to repo config of autoloaded repository
                            $autoload_add_where_sql = isset($autoload_config['add_where_sql']) && !empty($autoload_config['add_where_sql']) ? $autoload_config['add_where_sql'] : false;
                            if($autoload_add_where_sql) {
                                $config = $ForeignRepository->getConfig();
                                $config['add_where_sql'] = $autoload_add_where_sql;
                                $ForeignRepository->setConfig($config);
                            }

                            foreach( $dataobject AS $objectkey => $object ) {
                                $relationObject = $ForeignRepository->findByFieldvalue($autoload_config['foreign_field'],$object->$getFieldValueMethod());
                                if($relationObject) {
                                    if($autoload_config['result_is_array'] === true) {
                                        $object->$setLocalFieldMethod($relationObject);
                                    }
                                    else {
                                        $object->$setLocalFieldMethod($relationObject[0]);
                                    }
                                }
                            }
                        }
                    }

                    // Overwrite with language content if language content is requested
                    if($this->getLanguageId() && isset($this->config['language_table']) && isset($this->config['language_fields'])) {
                        foreach($dataobject AS $objectkey => $object) {
                            $getIdFieldMethod = $this->__getMethodNameFromFieldname($this->config['idfield'], 'get');
                            $lc = $this->getLanguageContent($object->$getIdFieldMethod());
                            if($lc) {
                                foreach($this->config['language_fields'] AS $lcfield) {
                                    $setLcFieldMethod = $this->__getMethodNameFromFieldname($lcfield, 'set');
                                    $getLcFieldMethod = $this->__getMethodNameFromFieldname($lcfield, 'get');
                                    $object->$setLcFieldMethod($lc->$getLcFieldMethod());
                                }
                            }
                        }
                    }

                    return $dataobject;
                }
                catch( \PDOException $e ) {
                    $this->getLogger()->Log(APPLICATION_LOG_LEVEL_CRITICAL, "PDO error: " . $e->getMessage());
                    print "PDO error: " . $e->getMessage();
                    return false;
                }
            }
            catch( \PDOException $e ) {
                $this->getLogger()->Log(APPLICATION_LOG_LEVEL_CRITICAL, "PDO error: " . $e->getMessage());
                print "PDO error: " . $e->getMessage();
                return false;
            }
        }

        /**
         * @return array
         */
        public function getFieldlist() {
            return $this->fieldlist;
        }

        /**
         * @return Logger
         */
        public function getLogger(): Logger
        {
            return $this->logger;
        }

        /**
         * @return array
         */
        public function getConfig(): array
        {
            return $this->config;
        }

        /**
         * @param array $config
         */
        public function setConfig(array $config)
        {
            $this->config = $config;
        }

        /**
         * @return int
         */
        public function getLanguageId(): int
        {
            return $this->language_id;
        }

        /**
         * @param int $language_id
         */
        public function setLanguageId(int $language_id)
        {
            $this->language_id = $language_id;
        }
    }
