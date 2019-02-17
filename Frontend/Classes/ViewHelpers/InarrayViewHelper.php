<?php
    namespace LetsShoot\Sachkundetrainer\Frontend\ViewHelpers;

    use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

    class InarrayViewHelper extends AbstractConditionViewHelper
    {
        public function initializeArguments()
        {
            parent::initializeArguments();
            $this->registerArgument('haystack', 'mixed', 'View helper haystack', true);
            $this->registerArgument('needle', 'string', 'View helper needle', true);
            $this->registerArgument('type', 'string', 'Type of needle', true);
        }

        public function render() {
            $needle = $this->arguments['needle'];
            $haystack = $this->arguments['haystack'];
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

            if(in_array($needle, $haystack)) {
                return $this->renderThenChild();
            } else {
                return $this->renderElseChild();
            }
        }

        static protected function evaluateCondition($arguments = null)
        {
            $needle = $arguments['needle'];
            $haystack = $arguments['haystack'];
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

            if(in_array($needle, $haystack)) {
                return true;
            } else {
                return false;
            }
        }
    }