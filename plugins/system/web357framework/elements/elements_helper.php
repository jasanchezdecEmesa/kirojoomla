<?php
/* ======================================================
 # Web357 Framework for Joomla! - v1.9.2 (free version)
 # -------------------------------------------------------
 # For Joomla! CMS (v3.x)
 # Author: Web357 (Yiannis Christodoulou)
 # Copyright (©) 2014-2024 Web357. All rights reserved.
 # License: GNU/GPLv3, http://www.gnu.org/licenses/gpl-3.0.html
 # Website: https:/www.web357.com
 # Demo: https://demo.web357.com/joomla/
 # Support: support@web357.com
 # Last modified: Monday 27 May 2024, 01:57:48 PM
 ========================================================= */

 
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

// Autoload
require_once(__DIR__.'/../autoload.php');

// CSS
Factory::getDocument()->addStyleSheet(URI::root(true).'/media/plg_system_web357framework/css/style.min.css?v=20240527015748');

// BEGIN: Loading plugin language file
$lang = Factory::getLanguage();
$current_lang_tag = $lang->getTag();
$lang = Factory::getLanguage();
$extension = 'plg_system_web357framework';
$base_dir = JPATH_ADMINISTRATOR;
$language_tag = (!empty($current_lang_tag)) ? $current_lang_tag : 'en-GB';
$reload = true;
$lang->load($extension, $base_dir, $language_tag, $reload);
// END: Loading plugin language file

 // Check if extension=php_curl.dll is enabled in PHP
function isCurl(){
	if (function_exists('curl_version')):
		return true;
	else:
		return false;
	endif;
}

// Check if allow_url_fopen is enabled in PHP
function allowUrlFopen(){
	if(ini_get('allow_url_fopen')):
		return true;
	else:
		return false;
	endif;
}