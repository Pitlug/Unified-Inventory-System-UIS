<?php
    class Header {
        private $headContent;
        private $cssContent;
        private $jsContent;
        public function __construct($pageName,$extraStyles,$jsFiles) {
            $cssUrl = $GLOBALS['cssUrl'];
            $jsUrl = $GLOBALS['jsUrl'];

            $this->headContent = '<html><head>
            <title>'.$pageName.'</title>
            <link rel="icon" type="image/png" href="../web/images/logo.png">';

            if(isset($extraStyles) && is_array($extraStyles) && count($extraStyles)>0){
                foreach($extraStyles as $cssfile){
                    $this->cssContent.='<link rel="stylesheet" type="text/css" href="'.$cssUrl.$cssfile.'">';
                }
            }

            $this->cssContent .='<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
                                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">';

            if(isset($jsFiles) && is_array($jsFiles) && count($jsFiles)>0 ){
                foreach($jsFiles as $jsfile){
                    $this->jsContent.='<script type="module" src="'.$jsUrl.$jsfile.'"></script>';
                }
            }
            $this->jsContent.='<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>';
        }

        public function addStyle($styleSheet){
            $cssUrl = $GLOBALS['cssUrl'];
            $this->cssContent.='<link rel="stylesheet" type="text/css" href="'.$cssUrl.$styleSheet.'">';
        }

        public function addJS($jsfile){
            $jsUrl = $GLOBALS['jsUrl'];
            $this->jsContent.='<script src="'.$jsUrl.$jsfile.'"></script>';
        }

        public function render(){
            $this->headContent.=$this->cssContent;
            $this->headContent.=$this->jsContent;
            $this->headContent.='</head>';
            return $this->headContent;
        }
    }