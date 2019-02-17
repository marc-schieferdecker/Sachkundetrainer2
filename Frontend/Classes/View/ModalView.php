<?php
    namespace LetsShoot\Sachkundetrainer\Frontend\View;

    use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
    use TYPO3Fluid\Fluid\View\TemplatePaths;
    use TYPO3Fluid\Fluid\View\TemplateView;

    class ModalView extends TemplateView {
        /**
         * ErrorView constructor.
         * @param string $controllerName
         * @param string $controllerAction
         * @param RenderingContextInterface|null $context
         */
        public function __construct(string $controllerName = 'Modal', string $controllerAction = 'Modal', RenderingContextInterface $context = null)
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
        public function setBody(string $body) {
            $this->assign('body', $body);
        }

        /**
         * Set a default success msg
         */
        public function setDefaultMsgSuccess() {
            $this->setTitle('Aktion erfolgreich');
            $this->setBody('Das hat geklappt.');
        }

        /**
         * Set a default 'data saved' msg
         */
        public function setDefaultMsgSaved() {
            $this->setTitle('Speichern erfolgreich');
            $this->setBody('Die Daten wurden gespeichert.');
        }
    }