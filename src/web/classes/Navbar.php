<?php

    class NavBar {
        private $links = [];
        private $lHidden = '';
        private $dHIdden = '';
        private $themeTxt = '';
        private /*static*/ $defaultNavLinks = [
            "Home" => ["url" => "/index.php", "icon" => "bi bi-house-fill"],
            "Inventory" => ["url" => "/inventory.php", "icon" => "bi bi-box-seam-fill"],
            "Orders" => ["url" => "/orders.php", "icon" => "bi bi-clipboard2-check-fill"],
            "Users" => ["url" => "/users.php", "icon" => "bi bi-person-badge-fill"],
            "Login" => ["url" => "/login.php", "icon" => "bi bi-unlock2-fill"]
        ];

        public function __construct($installdir="",$links = []) {
            /* Yoinked from gamers2025 needs review
            global $_SESSION,$urlForNavBar;
            if (!is_array($links) || count($links) == 0 ){
                $this->links = self::$defaultNavLinks;
                foreach($this->links as $key => $values){
                    $this->links[$key]["url"] = $urlForNavBar.$this->links[$key]["url"];
                }
            } else {
                $this->links = $links;
            }
            
            if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"]==true){
                if(isset($_SESSION["isAdmin"]) && $_SESSION["isAdmin"]==1){
                    $this->links["Settings"] = ["url" => $urlForNavBar."/web/admin.php?page=settings","icon" => "fas fa-cog"]; 
                }

                //remove login from array of links
                unset($this->links["Login"]);
                //add a logout feature
                $this->links["Logout"] = ["url" => $urlForNavBar."/web/index.php?page=logout","icon" => "fas fa-key"]; 
            }*/
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
            $this->links = $this->defaultNavLinks;
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
