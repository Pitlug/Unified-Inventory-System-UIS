<?php

    class NavBar {
        private $links = [];
        private /*static*/ $defaultNavLinks = [
            "Home" => ["url" => "/index.php", "icon" => "bi bi-0-circle-fill"],
            "Inventory" => ["url" => "/inventory.php", "icon" => "bi bi-0-circle-fill"],
            "Orders" => ["url" => "/orders.php", "icon" => "bi bi-0-circle-fill"],
            "Users" => ["url" => "/users.php", "icon" => "bi bi-0-circle-fill"],
            "Login" => ["url" => "/login.php", "icon" => "bi bi-0-circle-fill"]
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

        public function render() {
            $this->links = $this->defaultNavLinks;
            $html = "<nav class='navbar navbar-expand-lg navbar'>";
            $html .= "<a class='navbar-brand' href='".$GLOBALS['webRoot'].$this->links["Home"]["url"]."'>";
            $html .= "<img id='logo' src='".$GLOBALS['imgUrl']."/logo-light.png' alt='Logo' width='100px.'>";
            $html .= "</a>";
            $html .= "<div class='collapse navbar-collapse' id='navbarNav'>";            
            $html .= "<ul class='navbar-nav ml-auto'>";
        
            foreach ($this->links as $text => $info) {
                $url = $GLOBALS['webRoot'].$info['url'];
                $icon = isset($info['icon']) ? "<i class='{$info['icon']}'></i> " : "";
                $html .= "<li class='nav-item'><a class='navbar-link' href='{$url}'>{$icon}{$text}</a></li>";
            }

            $html .= "</ul></div></nav>";
            return $html;
        }
    }
