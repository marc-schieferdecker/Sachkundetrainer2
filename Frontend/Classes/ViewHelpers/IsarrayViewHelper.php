<?php
    namespace LetsShoot\Sachkundetrainer\Frontend\ViewHelpers;

    use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

    class IsarrayViewHelper extends AbstractConditionViewHelper
    {
        public function initializeArguments() {
            parent::initializeArguments();
            $this->registerArgument('haystack', 'mixed', 'View helper haystack', true);
        }

        public function render() {
            $haystack = $this->arguments['haystack'];

            if(is_array($haystack)) {
                return $this->renderThenChild();
            } else {
                return $this->renderElseChild();
            }
        }

        static protected function evaluateCondition($arguments = null) {
            $haystack = $arguments['haystack'];

            if(!is_array($haystack)) {
                return false;
            }

            if(is_array($haystack)) {
                return true;
            } else {
                return false;
            }
        }
    }