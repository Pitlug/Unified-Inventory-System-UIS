<?php

    class Footer {

        private $text;
        private $links = '<a href="sitemap" class="">Sitemap</a>';

        public function __construct($text="") {
            $this->text = $text;
            $this->text .= "<br>Copyright 2025<br>";
        }

        public function render() {

            return "<footer><p>{$this->text}<br><br>{$this->links}</p></footer>";

        }

    }
?>