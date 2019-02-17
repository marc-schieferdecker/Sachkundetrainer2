<?php
    namespace LetsShoot\Sachkundetrainer\Frontend\ViewHelpers;

    use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

    class ArraycontainsViewHelper extends AbstractConditionViewHelper
    {
        public function initializeArguments()
        {
            parent::initializeArguments();
            $this->registerArgument('haystack', 'mixed', 'View helper haystack', true);
            $this->registerArgument('needle', 'string', 'View helper needle', true);
            $this->registerArgument('field', 'string', 'View helper field', true);
            $this->registerArgument('type', 'string', 'Type of needle', true);
        }

        public function render() {
            $needle = $this->arguments['needle'];
            $haystack = $this->arguments['haystack'];
            $field = $this->arguments['field'];
            $type = $this->arguments['type'];

            if(!is_array($haystack)) {
                return false;
            }

            if($type=='int' || $type=='integer') {
                $needle = intval($needle);
            }
            if($type=='float' || $type=='double') {
                $needle = floatval($needle);
            }
            if($type=='string') {
                $needle = (string)($needle);
            }

            $found = false;
            foreach( $haystack AS $hay ) {
                if(!is_array($hay)) {
                    $hay = (array)$hay;
                }
                if(isset($hay[$field]) && $hay[$field] == $needle) {
                    $found = true;
                    break;
                }
            }

            if($found) {
                return $this->renderThenChild();
            } else {
                return $this->renderElseChild();
            }
        }

        static protected function evaluateCondition($arguments = null)
        {
            $needle = $arguments['needle'];
            $haystack = $arguments['haystack'];
            $field = $arguments['field'];
            $type = $arguments['type'];

            if(!is_array($haystack)) {
                return false;
            }

            if($type=='int' || $type=='integer') {
                $needle = intval($needle);
            }
            if($type=='float' || $type=='double') {
                $needle = floatval($needle);
            }
            if($type=='string') {
                $needle = (string)($needle);
            }

            $found = false;
            foreach( $haystack AS $hay ) {
                if(!is_array($hay)) {
                    $hay = (array)$hay;
                }
                if(isset($hay[$field]) && $hay[$field] == $needle) {
                    $found = true;
                    break;
                }
            }

            return $found;
        }
    }