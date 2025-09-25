<?php
// HTTP
define('HTTP_SERVER', 'https://gbiosciences.local/admin/');
define('HTTP_CATALOG', 'https://gbiosciences.local/');

// HTTPS
define('HTTPS_SERVER', 'https://gbiosciences.local/admin/');
define('HTTPS_CATALOG', 'https://gbiosciences.local/');

// DIR
define('DIR_ROOT', 'D:/xampp/htdocs/gbiosciences/');
define('DIR_APPLICATION', DIR_ROOT . 'admin/');
define('DIR_SYSTEM', DIR_ROOT . 'system/');
define('DIR_IMAGE', DIR_ROOT . 'image/');
define('DIR_STORAGE', DIR_ROOT . 'system/storage/');
define('DIR_CATALOG', DIR_ROOT . 'catalog/');
define('DIR_LANGUAGE', DIR_APPLICATION . 'language/');
define('DIR_TEMPLATE', DIR_APPLICATION . 'view/template/');
define('DIR_CONFIG', DIR_SYSTEM . 'config/');
define('DIR_CACHE', DIR_STORAGE . 'cache/');
define('DIR_DOWNLOAD', DIR_STORAGE . 'download/');
define('DIR_LOGS', DIR_STORAGE . 'logs/');
define('DIR_MODIFICATION', DIR_STORAGE . 'modification/');
define('DIR_SESSION', DIR_STORAGE . 'session/');
define('DIR_UPLOAD', DIR_STORAGE . 'upload/');

// DB
define('DB_DRIVER', 'mysqli');
define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
//define('DB_USERNAME', 'gbiolive');
//define('DB_PASSWORD', 'G(5gTMq#XIRzAEF+');

define('DB_DATABASE', 'gbiosciences');
define('DB_PORT', '3306');
define('DB_PREFIX', 'oc_');

// OpenCart API
define('OPENCART_SERVER', 'https://www.opencart.com/');

// CDN
define('CDN_SERVER', 'https://cdn.gbiosciences.com/');

define('AWS_REGION_CODE_LIST', [
    'us-east-1',
    'us-east-2',
    'us-east-1',
    'us-west-1',
    'us-west-2',
    'ap-east-1',
    'ap-south-1',
    'ap-northeast-3',
    'ap-northeast-2',
    'ap-southeast-1',
    'ap-southeast-2',
    'ap-northeast-1',
    'ca-central-1',
    'cn-north-1',
    'cn-northwest-1',
    'eu-central-1',
    'eu-west-1',
    'eu-west-2',
    'eu-west-3',
    'eu-north-1',
    'sa-east-1',
    'me-south-1'
]);

define('DEFAULT_REGION', 'us-west-2');
define('DEFAULT_BUCKET', 'gbiosciences');
