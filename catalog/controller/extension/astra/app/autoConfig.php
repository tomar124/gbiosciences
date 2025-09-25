<?php

namespace AstraPrefixed;

use AstraPrefixed\GetAstra\Client\Helper\Cms\AbstractCmsHelper;
//CMS mysql creds detection
if (isset($astraDependencyErrors['recommendations']['pdo_missing']) && isset($astraDependencyErrors['recommendations']['mysql_creds'])) {
    $abstractCmsHelper = new AbstractCmsHelper();
    $cms = $abstractCmsHelper->getCmsName();
    $cmsHelper = $abstractCmsHelper->getCmsInstance($cms);
    $result = $cmsHelper->getDatabaseCreds();
    if (empty($result)) {
        if (\ASTRA_DEBUG_MODE && \ASTRA_API_ROUTE) {
            unset($astraDependencyErrors['recommendations']['pdo_missing'], $astraDependencyErrors['recommendations']['mysql_creds']);
            $astraDependencyErrors['errors']['pdo_missing'] = 'pdo_sqlite & SQLite server extensions required (easy)';
            $astraDependencyErrors['errors']['mysql_creds'] = 'If pdo_sqlite cannot be installed, then MySQL database connection details are required (advance)';
            astraErrorTemplateRender($astraDependencyErrors);
        } else {
            return;
        }
    } else {
        foreach ($result as $key => $envVar) {
            \putenv($key . "=" . $envVar);
        }
    }
}
