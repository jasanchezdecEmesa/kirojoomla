<?php
/* ======================================================
 # Cookies Policy Notification Bar for Joomla! - v4.3.5 (pro version)
 # -------------------------------------------------------
 # For Joomla! CMS (v3.x)
 # Author: Web357 (Yiannis Christodoulou)
 # Copyright (©) 2014-2024 Web357. All rights reserved.
 # License: GNU/GPLv3, http://www.gnu.org/licenses/gpl-3.0.html
 # Website: https:/www.web357.com
 # Demo: https://demo.web357.com/joomla/browse/cookies-policy-notification-bar
 # Support: support@web357.com
 # Last modified: Monday 27 May 2024, 01:57:48 PM
 ========================================================= */
// No direct access
defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;

// BEGIN: Check if Web357 Framework plugin exists
jimport('joomla.plugin.helper');
if (!PluginHelper::isEnabled('system', 'web357framework')) {
    $web357framework_required_msg = Text::_('<p>The <strong>"Web357 Framework"</strong> is required for this extension and must be active. Please, download and install it from <a href="http://downloads.web357.com/?item=web357framework&type=free">here</a>. It\'s FREE!</p>');
    Factory::getApplication()->enqueueMessage($web357framework_required_msg, 'warning');
    return false;
}
// END: Check if Web357 Framework plugin exists

$user = (version_compare(JVERSION, "4.0", ">=")) ? Factory::getApplication()->getIdentity() : Factory::getUser();
if (!$user->authorise('core.manage', 'com_cookiespolicynotificationbar'))
{
	throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'));
}

// -- ########### -- //

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Cookiespolicynotificationbar', JPATH_COMPONENT_ADMINISTRATOR);
JLoader::register('CookiespolicynotificationbarHelper', JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'cookiespolicynotificationbar.php');

$controller = BaseController::getInstance('Cookiespolicynotificationbar');
$controller->execute(Factory::getApplication()->input->get('task'));
$controller->redirect();