<?php
    namespace LetsShoot\Sachkundetrainer\Frontend\View;

    use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
    use TYPO3Fluid\Fluid\View\TemplatePaths;
    use TYPO3Fluid\Fluid\View\TemplateView;

    class ErrorView extends TemplateView {
        /**
         * ErrorView constructor.
         * @param string $controllerName
         * @param string $controllerAction
         * @param RenderingContextInterface|null $context
         */
        public function __construct(string $controllerName = 'Error', string $controllerAction = 'Error', RenderingContextInterface $context = null)
        {
            // Construct TemplateView
            parent::__construct($context);

            // Create base paths to fluid templates
            $paths = new TemplatePaths();
            $paths->setLayoutRootPaths(array(APPLICATION_LAYOUTS_PATH));
            $paths->setTemplateRootPaths(array(APPLICATION_TEMPLATES_PATH));
            $paths->setPartialRootPaths(array(APPLICATION_PARTIALS_PATH));

            // Set paths and MVC context
            $renderingContext = $this->getRenderingContext();
            $renderingContext->setTemplatePaths($paths);
            $renderingContext->setControllerName($controllerName);
            $renderingContext->setControllerAction($controllerAction);
            $this->setRenderingContext($renderingContext);

            // Set default title
            $this->setDefaultTitle();
        }

        /**
         * @param string $title
         */
        public function setTitle(string $title) {
            $this->assign('title', $title);
        }

        /**
         * @param string $msg
         */
        public function setMessage(string $msg) {
            $this->assign('message', $msg);
        }

        /**
         * @param string $trace
         */
        public function setTrace(string $trace) {
            $this->assign('trace', $trace);
        }

        public function setPasswordToShort() {
            $this->assign('title', 'Kennwort zu kurz');
            $this->assign('message', 'Das von Ihnen gewählte Kennwort ist zu kurz. Bitte wählen Sie ein Kennwort mit mindestens 6 Zeichen Länge.');
        }

        public function setPasswordNoMatch() {
            $this->assign('title', 'Keine Übereinstimmung mit Prüfkennwort');
            $this->assign('message', 'Das von Ihnen gewählte Kennwort stimmt nicht mit dem Prüfkennwort überein.');
        }

        public function setRightsMissing() {
            $this->assign('title', 'Keine Berechtigung');
            $this->assign('message', 'Um auf den von Ihnen angewählten Bereich zugreifen zu können, benötigen Sie höhere Benutzerrechte.');
        }

        public function setIDParameterMissing() {
            $this->assign('title', 'ID Parameter fehlt');
            $this->assign('message', 'Es wurde keine Datenbank ID übergeben. Deswegen kann nicht auf die Funktion zugegriffen werden.');
        }

        public function setParametersMissing() {
            $this->assign('title', 'Pflichtfelder nicht befüllt');
            $this->assign('message', 'Sie haben nicht alle benötigten Felder befüllt.');
        }

        public function setCanNotLoadEntry() {
            $this->assign('title', 'Kann Datensatz nicht laden');
            $this->assign('message', 'Der Datensatz konnte nicht geladen werden.');
        }

        public function setCanNotLoadQuestionMaybeAllAnswered() {
            $this->assign('title', 'Kann keine Frage laden');
            $this->assign('message', 'Es konnte keine Frage geladen werden. Vielleicht haben Sie die Option "doppelte Fragen unterdrücken" aktiviert und nun alle Frage beantwortet. Bitte schauen Sie mal in den Einstellungen.');
        }

        public function setCanNotLoadQuestionMaybeAllAnsweredInTopic() {
            $this->assign('title', 'Kann keine Frage laden');
            $this->assign('message', 'Es konnte in diesem Themenbereich keine Frage geladen werden. Vielleicht haben Sie die Option "doppelte Fragen unterdrücken" aktiviert und nun alle Frage beantwortet. Bitte schauen Sie mal in den Einstellungen.');
        }

        public function setConfigArrayProblem() {
            $this->assign('title', 'Fehler');
            $this->assign('message', 'Mit der Konfiguration stimmt etwas nicht. Bitte wenden Sie sich an einen Administrator.');
        }

        /**
         * Set default title
         */
        public function setDefaultTitle()
        {
            $this->assign('title', 'Fehler');
        }
    }