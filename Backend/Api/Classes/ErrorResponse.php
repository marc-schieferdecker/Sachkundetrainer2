<?php
    namespace LetsShoot\Sachkundetrainer\Backend;

    /**
     * Class ErrorResponse
     * @package LetsShoot\Sachkundetrainer\Backend
     */
    class ErrorResponse implements \JsonSerializable {
        /**
         * @var string $errorMsg
         */
        private $errorMsg;

        /**
         * @var string $errorCode
         */
        private $errorCode;

        /**
         * @var string $errorTrace
         */
        private $errorTrace;

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
                        if(method_exists($item,'jsonSerialize')) {
                            $jsonObject[$var][] = $item->jsonSerialize();
                        }
                        else {
                            $jsonObject[$var][] = json_encode($item);
                        }
                    }
                }
            }
            return $jsonObject;
        }

        /**
         * no api key
         */
        public function setErrorNoApiKey() {
            $this->setErrorMsg('API Schlüssel nicht übergeben.');
            $this->setErrorCode('NO_API_KEY');
            $this->setErrorTrace();
        }

        /**
         * no config
         */
        public function setErrorNoConfig() {
            $this->setErrorMsg('Konfiguration konnte nicht geladen werden.');
            $this->setErrorCode('NO_CONFIG');
            $this->setErrorTrace();
        }

        /**
         * false api key
         */
        public function setErrorApiKeyInvalid() {
            $this->setErrorMsg('API Schlüssel passt zu keinem Benutzer.');
            $this->setErrorCode('API_KEY_INVALID');
            $this->setErrorTrace();
        }

        /**
         * no action set
         */
        public function setErrorNoActionSet() {
            $this->setErrorMsg('Der Parameter "action" ist nicht gesetzt.');
            $this->setErrorCode('NO_ACTION_SET');
            $this->setErrorTrace();
        }

        /**
         * no action found
         */
        public function setErrorActionNotImplemented() {
            $this->setErrorMsg('Die Aktion ist nicht implementiert.');
            $this->setErrorCode('ACTION_NOT_IMPLEMENTED');
            $this->setErrorTrace();
        }

        /**
         * id parameter empty
         */
        public function setErrorIdParameterEmpty() {
            $this->setErrorMsg('Es wurde keine ID übergeben.');
            $this->setErrorCode('ID_PARAMETER_EMPTY');
            $this->setErrorTrace();
        }

        /**
         * reading object failure
         */
        public function setErrorReadingObject() {
            $this->setErrorMsg('Fehler beim Lesen des Objektes. Der Datenbankeintrag existiert vermutlich nicht mehr.');
            $this->setErrorCode('READING_OBJECT');
            $this->setErrorTrace();
        }

        /**
         * missing object or missing rights failure
         */
        public function setErrorMissingObjectOrMissingRights() {
            $this->setErrorMsg('Fehler. Entweder existiert dieser Datenbankeintrag nicht, oder Sie haben keine ausreichenden Rechte für diese Aktion.');
            $this->setErrorCode('MISSING_OBJECT_OR_MISSING_RIGHTS');
            $this->setErrorTrace();
        }

        /**
         * adding object failure
         */
        public function setErrorAddingObject() {
            $this->setErrorMsg('Fehler beim Anlegen des Objektes, sind alle Pflichtfelder befüllt?');
            $this->setErrorCode('ADDING_OBJECT');
            $this->setErrorTrace();
        }

        /**
         * empty upload failure
         */
        public function setErrorUploadFileEmpty() {
            $this->setErrorMsg('Es wurde keine Datei übertragen. Der Eintrag wurde daher nicht angelegt.');
            $this->setErrorCode('UPLOADFILE_EMPTY');
            $this->setErrorTrace();
        }

        /**
         * empty upload failure
         */
        public function setErrorStoringOnDiskFailed() {
            $this->setErrorMsg('Die hochgeladene Datei konnte nicht auf das Speichermedium geschrieben werden.');
            $this->setErrorCode('STORING_ON_DISK_FAILED');
            $this->setErrorTrace();
        }

        /**
         * updating object failure
         */
        public function setErrorUpdateObject() {
            $this->setErrorMsg('Fehler beim Speichern des Objektes, sind alle Pflichtfelder befüllt?');
            $this->setErrorCode('UPDATE_OBJECT');
            $this->setErrorTrace();
        }

        /**
         * updating object failure
         */
        public function setErrorDeleteObject() {
            $this->setErrorMsg('Fehler beim Löschen des Objektes. Entweder keine Berechtigung, oder das Objekt existiert nicht mehr.');
            $this->setErrorCode('DELETE_OBJECT');
            $this->setErrorTrace();
        }

        /**
         * missing user rights
         */
        public function setErrorUserrightsMissing() {
            $this->setErrorMsg('Keine Ausreichenden Rechts für die Aktion.');
            $this->setErrorCode('USERRIGHTS_MISSING');
            $this->setErrorTrace();
        }

        /**
         * missing user rights
         */
        public function setErrorSecretsDoNotMatch() {
            $this->setErrorMsg('Die Sicherheitstoken stimmen nicht überein.');
            $this->setErrorCode('SECRETS_DO_NOT_MATCH');
            $this->setErrorTrace();
        }

        /**
         * missing parameters
         */
        public function setErrorParametersMissing() {
            $this->setErrorMsg('Es sind nicht alle nötigen Parameter gesetzt.');
            $this->setErrorCode('PARAMETERS_MISSING');
            $this->setErrorTrace();
        }

        /**
         * missing parameters
         */
        public function setErrorEscalationMissing() {
            $this->setErrorMsg('Bitte definieren Sie einen Eskalationszeitpunkt.');
            $this->setErrorCode('ESCALATION_MISSING');
            $this->setErrorTrace();
        }

        /**
         * duplicate object
         */
        public function setErrorDuplicateObject() {
            $this->setErrorMsg('Das Objekt existiert bereits und kann nicht noch mal angelegt werden.');
            $this->setErrorCode('DUPLICATE_OBJECT');
            $this->setErrorTrace();
        }

        /**
         * duplicate api key
         */
        public function setErrorDuplicateApiKey() {
            $this->setErrorMsg('Der API Schlüssel wird mehrfach benutzt?');
            $this->setErrorCode('DUPLICATE_API_KEY');
            $this->setErrorTrace();
        }

        /**
         * duplicate email in client
         */
        public function setErrorDuplicateEmailInClient() {
            $this->setErrorMsg('Diese E-Mail Adresse ist bereits in Verwendung.');
            $this->setErrorCode('DUPLICATE_EMAIL_IN_CLIENT');
            $this->setErrorTrace();
        }

        /**
         * undefined exception
         */
        public function setErrorException() {
            $this->setErrorMsg('Exception.');
            $this->setErrorCode('EXCEPTION');
            $this->setErrorTrace();
        }

        /**
         * @return string
         */
        public function getErrorMsg(): string
        {
            return $this->errorMsg;
        }

        /**
         * @param string $errorMsg
         */
        public function setErrorMsg(string $errorMsg)
        {
            $this->errorMsg = $errorMsg;
        }

        /**
         * @return string
         */
        public function getErrorCode(): string
        {
            return $this->errorCode;
        }

        /**
         * @param string $errorCode
         */
        public function setErrorCode(string $errorCode)
        {
            $this->errorCode = $errorCode;
        }

        /**
         * @return string
         */
        public function getErrorTrace(): string
        {
            if(APPLICATION_SHOW_ERRORTRACE) {
                return $this->errorTrace;
            }
            else {
                return '';
            }
        }

        /**
         * set backtrace
         */
        public function setErrorTrace()
        {
            if(APPLICATION_SHOW_ERRORTRACE) {
                $trace = debug_backtrace();
                unset($trace[0]);
                $this->errorTrace = print_r($trace, true);
            }
            else {
                $this->errorTrace = '';
            }
        }
    }
