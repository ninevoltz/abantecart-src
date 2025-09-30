<?php

/* Hooks */
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ExtensionJivoChat extends Extension
{
    public $data = [];

    public function __construct()
    {
        $this->registry = Registry::getInstance();
    }
}
