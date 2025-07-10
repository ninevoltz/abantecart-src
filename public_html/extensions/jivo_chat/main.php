<?php

/* Main extension driver containing details about extension files */

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

if (!class_exists('ExtensionJivoChat')) {
    include 'core/jivo_chat.php';
}

$languages = [
    'admin' => ['jivo_chat/jivo_chat'],
    'storefront' => [],
];

$templates = [
    'storefront' => [
        'common/head.post.tpl',
    ],
    'admin' => [],
];
