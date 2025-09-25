<?php
//putenv('AWS_EC2_METADATA_DISABLED=true');
//putenv('AWS_ACCESS_KEY_ID=dummy');
//putenv('AWS_SECRET_ACCESS_KEY=dummy'); // Halim

// Version
define('VERSION', '3.0.2.0');

// Configuration
if (is_file('config.php')) {
	require_once('config.php');
}

// Install
if (!defined('DIR_APPLICATION')) {
	header('Location: ../install/index.php');
	exit;
}

// Startup
require_once(DIR_SYSTEM . 'startup.php');

start('admin');