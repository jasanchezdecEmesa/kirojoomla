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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\HTML\Helpers\Sidebar;
class CookiespolicynotificationbarHelper
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param   string  $vName  string
	 *
	 * @return void
	 */
	public static function addSubmenu($vName = '')
	{
		// Set sidebar action - New in 3.0
		if (version_compare(JVERSION, '5.0', '>=')) {
			Sidebar::addEntry(Text::_('COM_COOKIESPOLICYNOTIFICATIONBAR_TITLE_COOKIESPOLICYNOTIFICATIONBAR'), 'index.php?option=com_cookiespolicynotificationbar&view=cookiespolicynotificationbar', $vName == 'cookiespolicynotificationbar');
			Sidebar::addEntry(Text::_('COM_COOKIESPOLICYNOTIFICATIONBAR_CONFIGURATION_SIDEBAR_LABEL'), 'index.php?option=com_config&view=component&component=com_cookiespolicynotificationbar', $vName == 'settingscore');
		}
		else
		{
			JHtmlSidebar::addEntry(Text::_('COM_COOKIESPOLICYNOTIFICATIONBAR_TITLE_COOKIESPOLICYNOTIFICATIONBAR'), 'index.php?option=com_cookiespolicynotificationbar&view=cookiespolicynotificationbar', $vName == 'cookiespolicynotificationbar');
			JHtmlSidebar::addEntry(Text::_('COM_COOKIESPOLICYNOTIFICATIONBAR_CONFIGURATION_SIDEBAR_LABEL'), 'index.php?option=com_config&view=component&component=com_cookiespolicynotificationbar', $vName == 'settingscore');
		}
	}

	/**
	 * Gets the files attached to an item
	 *
	 * @param   int     $pk     The item's id
	 *
	 * @param   string  $table  The table's name
	 *
	 * @param   string  $field  The field's name
	 *
	 * @return  array  The files
	 */
	public static function getFiles($pk, $table, $field)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);

		$query
			->select($field)
			->from($table)
			->where('id = ' . (int) $pk);

		$db->setQuery($query);

		return explode(',', $db->loadResult());
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return    JObject
	 *
	 * @since    1.6
	 */
	public static function getActions()
	{
		$user = (version_compare(JVERSION, "4.0", ">=")) ? Factory::getApplication()->getIdentity() : Factory::getUser();
		$result = new CMSObject();

		$assetName = 'com_cookiespolicynotificationbar';

		$actions = array(
			'core.admin', 'core.manage', 'core.delete'
		);

		foreach ($actions as $action)
		{
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}
}

