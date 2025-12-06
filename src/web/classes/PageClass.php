<?php
    include_once 'Navbar.php';
    include_once 'Header.php';
    include_once 'Footer.php';
    include_once __DIR__.'../../../sitevars.php';
    class PageClass {
        private $pageContent;
        private $passedContent;
        private $bodyTag;
        private $pageName;
        private $header;
        private $navBar;
        private $footer;

        public function __construct($pageName,$content,$styles=[],$jsfiles=[]) {
            $this->pageName = $pageName;

            GetCredlevel($this->pageName);
            $this->passedContent = $content;
            $this->header = new Header($pageName,$styles,$jsfiles);
            $this->navBar = new Navbar();
            $this->footer = new Footer();
            $this->header->addJS('mode-toggle.js');
            
        }

        public function checkCredentials($userCredLevel, $credLevelRequired){
            if(!($userCredLevel <= $credLevelRequired)){
                header('Location:'.$GLOBALS['webRoot'].'/unauthorizedAccess.php');
            }
        }

        public function standardize(){
            $this->header->addStyle('standardize.css');
        }

        public function render() {
            $this->pageContent = $this->header->render();
            $this->pageContent .= $this->navBar->render();    
            $this->pageContent .= $this->bodyTag;
            $this->pageContent .= $this->passedContent;
            $this->pageContent .= $this->footer->render();
            $this->pageContent .= '</body>';
            return $this->pageContent;
        }

    }
?>