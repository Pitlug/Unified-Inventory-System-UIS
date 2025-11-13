<?php
    include_once 'Navbar.php';
    include_once 'Header.php';
    include_once 'Footer.php';
    include_once __DIR__.'/../includes/sitevars.php';
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
            $this->passedContent = $content;
            $this->header = new Header($pageName,$styles,$jsfiles);
            $this->navBar = new Navbar();
            $this->footer = new Footer();
            if(isset($_COOKIE['theme']) && $_COOKIE['theme']=='dark'){
                $this->bodyTag = '<body class="dark-mode">';
                $this->navBar->themeChange('dark');
            }else{
                $this->bodyTag = '<body>';
                $this->navBar->themeChange('light');
            }
            $this->header->addJS('mode-toggle.js');
            
        }

        public function standardize(){
            $this->header->addStyle('standardize.css');
        }

        public function render() {
            if ($this->pageName!='Home'){
                GetCredlevel();
            }
            
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