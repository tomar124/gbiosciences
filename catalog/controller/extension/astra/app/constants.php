<?php

namespace AstraPrefixed;

use AstraPrefixed\GetAstra\Client\Helper\CommonHelper;
\defined('ASTRAROOT') ?: \define('ASTRAROOT', \dirname(__DIR__) . \DIRECTORY_SEPARATOR);
// Define doc root
$astraPath = __DIR__ . \DIRECTORY_SEPARATOR;
$astraBasePath = '';
$astraPossibleBreakWords = ['/wp-content/', '/catalog/controller/extension/', '/sites/all/modules/', '/modules/', '/app/code/', '/astra', '/getastra-premium'];
foreach ($astraPossibleBreakWords as $astraPossibleBreakWord) {
    if (\false !== \strpos($astraPath, $astraPossibleBreakWord)) {
        $astraBasePath = \strstr($astraPath, $astraPossibleBreakWord, \true) . \DIRECTORY_SEPARATOR;
        break;
    }
}
if (empty($astraBasePath)) {
    $astraBasePath = $_SERVER['DOCUMENT_ROOT'] ?? \getcwd();
}
\defined('ASTRA_DOC_ROOT') ?: \define('ASTRA_DOC_ROOT', $astraBasePath);
\defined('ASTRA_APP_ID') ?: \define('ASTRA_APP_ID', \md5(__DIR__ . \DIRECTORY_SEPARATOR . 'settings.php' . CommonHelper::customGetEnv('ASTRA_APP_ENV')));
