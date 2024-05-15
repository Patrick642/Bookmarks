<?php
/* Base paths */
define('ROOT_DIR', __DIR__ . '/../');
define('BASE_URL', (empty($_SERVER['HTTPS']) ? 'http' : 'https') . '://' . $_SERVER['HTTP_HOST']);
define('LOG_ERRORS', ROOT_DIR . '/log/errors.log');

/* Includes */
define('PAGE_HEADER', ROOT_DIR . '/src/View/inc/header.phtml');
define('PAGE_NAVBAR', ROOT_DIR . '/src/View/inc/navbar.phtml');
define('PAGE_FOOTER', ROOT_DIR . '/src/View/inc/footer.phtml');