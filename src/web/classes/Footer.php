<?php

    class Footer {

        private $text;
        private $links = '<a href="https://github.com/Pitlug/Unified-Inventory-System-UIS.git" class="">Github</a>';

        public function __construct($text="") {
            $this->text = $text;
            $this->text .= "<br>© 2025 UIS. All rights reserved.<br>";
        }

        public function render() {

            return "<footer><p>{$this->text}<br><br>{$this->links}</p></footer>";

        }

    }
?>