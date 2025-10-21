<?php
    include_once 'Navbar.php';
    include_once 'Header.php';
    include_once 'Footer.php';
    include_once __DIR__."\..\includes\sitefunctions.php";
    includeFiles();
    class PageClass {
        private $pageContent;
        private $passedContent;
        private $pageName;
        private $header;
        private $navBar;
        private $footer;

        public function __construct($pageName,$content,$styles=[],$jsfiles=[]) {
            $this->pageName = $pageName;
            $this->passedContent = $content;
            $this->header = new Header($pageName,$styles,$jsfiles);
            $this->navBar = new Navbar();
            $this->footer = new Footer();
        }

        public function standardize(){
            $this->header->addStyle('standardize.css');
        }

        public function render() {
            $this->pageContent = $this->header->render();
            $this->pageContent .= $this->navBar->render();    
            $this->pageContent .= $this->passedContent;
            $this->pageContent .= $this->footer->render();
            return $this->pageContent;
        }

    }
?>