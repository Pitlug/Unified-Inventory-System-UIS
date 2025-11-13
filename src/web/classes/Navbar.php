<?php

    class NavBar {
        private $links = [];
        private $lHidden = '';
        private $dHidden = '';
        private $themeTxt = '';
        private $defaultNavLinks = [
            "Home" => ["url" => "/index.php", "icon" => "bi bi-house-fill"],
            /*
            "Inventory" => ["url" => "/inventory.php", "icon" => "bi bi-box-seam-fill"],
            "Orders" => ["url" => "/orders.php", "icon" => "bi bi-clipboard2-check-fill"],
            "Users" => ["url" => "/users.php", "icon" => "bi bi-person-badge-fill"],
            */
            //"Login" => ["url" => "/login.php", "icon" => "bi bi-unlock2-fill"]
        ];

        public function __construct($installdir="",$links = []) {
            echo var_dump($_SESSION);

            if ($_SESSION['loggedin'] === false){
                $this->links = ["Login" => ["url" => "/login.php", "icon" => "bi bi-unlock2-fill"]];

            }
            if ($_SESSION['credentialLevel'] === 0){
                $this->links = array_merge($this->defaultNavLinks, ["Users" => ["url" => "/users.php", "icon" => "bi bi-person-badge-fill"],"Inventory" => ["url" => "/inventory.php", "icon" => "bi bi-box-seam-fill"],
                "Orders" => ["url" => "/orders.php", "icon" => "bi bi-clipboard2-check-fill"],"Logout" => ["url" => "/includes/logout.php", "icon" => "bi bi-unlock2-fill"]]);
            }else{
                $this->links = array_merge($this->defaultNavLinks, ["Inventory" => ["url" => "/inventory.php", "icon" => "bi bi-box-seam-fill"],
                "Orders" => ["url" => "/orders.php", "icon" => "bi bi-clipboard2-check-fill"],"Logout" => ["url" => "/includes/logout.php", "icon" => "bi bi-unlock2-fill"]]);
            }
        }

        public function themeChange($theme){
            if($theme=='dark'){
                $this->lHidden = '';
                $this->dHidden = 'hidden';//hide dark logo on dark bg
                $this->themeTxt = '☀️ Light Mode';
            }else{
                $this->dHidden = '';
                $this->lHidden = 'hidden';//hide light logo on light bg
                $this->themeTxt = '🌙 Dark Mode';
            }
        }

        public function render() {
            $this->__construct();
            $html = "<nav class='navbar navbar-expand-lg navbar'>";
            $html .= "<a class='navbar-brand' href='".$GLOBALS['webRoot'].$this->links["Home"]["url"]."'>";
            $html .= "<img class='logo {$this->dHidden}' id='logo' src='".$GLOBALS['imgUrl']."/logo.png' alt='Logo' width='100px.'>";
            $html .= "<img class='logo {$this->lHidden}' id='logo-light' src='".$GLOBALS['imgUrl']."/logo-light.png' alt='Logo' width='100px.'>";
            $html .= "</a>";
            $html .= "<div class='collapse navbar-collapse' id='navbarNav'>";            
            $html .= "<ul class='navbar-nav ml-auto'>";
        
            $html .= "<li class='nav-item'><button type='button' id='mode-toggle' class='btn'>{$this->themeTxt}</button></li>";
            foreach ($this->links as $text => $info) {
                
                $url = $GLOBALS['webRoot'].$info['url'];
                $icon = isset($info['icon']) ? "<i class='{$info['icon']}'></i> " : "";
                $html .= "<li class='nav-item'><a class='navbar-link' href='{$url}'>{$icon}{$text}</a></li>";
            }

            $html .= "</ul></div></nav>";
            return $html;
        }
    }
