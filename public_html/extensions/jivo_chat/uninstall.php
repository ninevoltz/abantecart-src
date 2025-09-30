<?php

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

(version_compare(VERSION, '1.4.0') >= 0) ? $this->cache->remove('*') : $this->cache->delete('*');
