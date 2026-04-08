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

use \Joomla\Utilities\ArrayHelper;
use \Joomla\CMS\Language\Text;

abstract class JHtmlListhelper
{
	static function toggle($value = 0, $view, $field, $i)
	{
		$states = array(
			0 => array('icon-remove', Text::_('Toggle'), 'inactive btn-danger'),
			1 => array('icon-checkmark', Text::_('Toggle'), 'active btn-success')
		);

		$state  = ArrayHelper::getValue($states, (int) $value, $states[0]);
		$text   = '<span aria-hidden="true" class="' . $state[0] . '"></span>';
		$html   = '<a href="#" class="btn btn-micro ' . $state[2] . '"';
		$html  .= 'onclick="return toggleField(\'cb'.$i.'\',\'' . $view . '.toggle\',\'' . $field . '\')" title="' . Text::_($state[1]) . '">' . $text . '</a>';

		return $html;
	}
}
