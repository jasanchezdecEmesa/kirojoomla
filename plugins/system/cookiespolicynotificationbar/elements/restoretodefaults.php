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

defined('_JEXEC') or die;
use Joomla\CMS\Version;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\FormField;

class JFormFieldrestoretodefaults extends FormField {
	
	protected $type = 'restoretodefaults';

	protected function getInput()
	{
		$html = '';

		// Get Joomla! version
		$jversion = new Version();
		$short_version = explode('.', $jversion->getShortVersion()); // 3.8.10
		$mini_version = $short_version[0].'.'.$short_version[1]; // 3.8

		if (version_compare( $mini_version, "2.5", "<=")):
			// j25
			$j25 = true;
			$j3x = false;
		else:
			// j3x
			$j25 = false;
			$j3x = true;
		endif;

		if ($j25)
		{
			$html .= '<div style="display: block;border: 2px solid red;clear: both;padding: 4px;">This "Restore to Defaults" feature is not supported in Joomla! 2.5</div>';
		}
		else
		{
			// load js
			Factory::getDocument()->addScript(Uri::root(true).'/plugins/system/cookiespolicynotificationbar/assets/js/admin.min.js');

			$html .= '<p><a class="btn btn-danger cpnb-restore-to-detafaults-btn" data-cpnb-restore-to-defaults-confirmation-msg="'.Text::_('PLG_SYSTEM_CPNB_RESTORE_TO_DEFAULTS_CONFIRMATION_MSG').'"><strong>'.Text::_('PLG_SYSTEM_CPNB_RESTORE_TO_DEFAULTS_BTN').'</strong></a></p>';
			$html .= '<div class="cpnb-settings-restored-to-default"></div>';
		}
		
		return $html;		
	}
}