<?php

const PKG_NAME = 'SCSS';
const PKG_NAME_LOWER = 'scss';

const PKG_VERSION = '1.0.4';
const PKG_RELEASE = 'pl';
const PKG_AUTO_INSTALL = false;
const PKG_NAMESPACE_PATH = '{core_path}components/' . PKG_NAME_LOWER . '/';

/* define paths */
if (isset($_SERVER['MODX_BASE_PATH'])) {
	define('MODX_BASE_PATH', $_SERVER['MODX_BASE_PATH']);
}elseif (file_exists(dirname(dirname(dirname(__FILE__))) . '/core')) {
	define('MODX_BASE_PATH', dirname(dirname(dirname(__FILE__))) . '/');
}else {
	define('MODX_BASE_PATH', dirname(dirname(dirname(dirname(__FILE__)))) . '/');
}
const MODX_CORE_PATH = MODX_BASE_PATH . 'core/';
const MODX_MANAGER_PATH = MODX_BASE_PATH . 'manager/';
const MODX_CONNECTORS_PATH = MODX_BASE_PATH . 'connectors/';

/* define urls */
const MODX_BASE_URL = '/';
const MODX_CORE_URL = MODX_BASE_URL . 'core/';
const MODX_MANAGER_URL = MODX_BASE_URL . 'manager/';
const MODX_CONNECTORS_URL = MODX_BASE_URL . 'connectors/';
const MODX_ASSETS_URL = MODX_BASE_URL . 'assets/';

/* define build options */
//const BUILD_MENU_UPDATE = false;
//const BUILD_ACTION_UPDATE = false;
const BUILD_SETTING_UPDATE = false;
//const BUILD_CHUNK_UPDATE = false;

const BUILD_SNIPPET_UPDATE = true;
const BUILD_PLUGIN_UPDATE = true;
//const BUILD_EVENT_UPDATE = true;
//const BUILD_POLICY_UPDATE = true;
//const BUILD_POLICY_TEMPLATE_UPDATE = true;
//const BUILD_PERMISSION_UPDATE = true;

//const BUILD_CHUNK_STATIC = false;
const BUILD_SNIPPET_STATIC = false;
const BUILD_PLUGIN_STATIC = false;

$BUILD_RESOLVERS = [
    //'parser',
];