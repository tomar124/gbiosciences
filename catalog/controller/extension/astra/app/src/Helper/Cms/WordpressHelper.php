<?php

/**
 * This file is part of the Astra Security Suite.
 *
 *  Copyright (c) 2019 (https://www.getastra.com/)
 *
 *  For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */
/**
 * @author HumansofAstra-WZ <help@getastra.com>
 * @date   2019-04-05
 */
namespace AstraPrefixed\GetAstra\Client\Helper\Cms;

use AstraPrefixed\GetAstra\Client\Helper\Cms\AbstractCmsHelper;
use AstraPrefixed\GetAstra\Client\Helper\StringHelper;
class WordpressHelper
{
    private $path;
    private $version;
    private $locale;
    public function __construct($path)
    {
        //If scan was started from within WP, we'll have to load the global variable
        if (isset($GLOBALS['wp_version'])) {
            global $wp_local_package;
            global $wp_version;
        }
        $this->path = $path;
        include_once $this->path . 'wp-includes/version.php';
        $this->locale = isset($wp_local_package) ? $wp_local_package : 'en_US';
        $this->version = isset($wp_version) ? $wp_version : \false;
    }
    public function getName()
    {
        return 'wordpress';
    }
    public function getVersion()
    {
        return $this->version;
    }
    public function getLocale()
    {
        return $this->locale;
    }
    public function getDatabaseCreds()
    {
        if (!\file_exists($this->path . 'wp-config.php') && \is_readable($this->path . 'wp-config.php')) {
            return [];
        }
        $contents = \file_get_contents($this->path . 'wp-config.php');
        $dbname = StringHelper::getStringBetween($contents, "define('DB_NAME', '", "');");
        $dbuser = StringHelper::getStringBetween($contents, "define('DB_USER', '", "');");
        $dbpassword = StringHelper::getStringBetween($contents, "define('DB_PASSWORD', '", "');");
        $dbhost = StringHelper::getStringBetween($contents, "define('DB_HOST', '", "');");
        $array = ['ASTRA_DB_DATABASE' => $dbname, 'ASTRA_DB_USERNAME' => $dbuser, 'ASTRA_DB_PASSWORD' => $dbpassword, 'ASTRA_DB_HOST' => $dbhost, 'ASTRA_DB_PREFIX' => 'astra_', 'ASTRA_DB_CONNECTION' => 'mysql'];
        foreach ($array as $key => $val) {
            if (empty($val)) {
                return [];
            }
        }
        return $array;
    }
}
