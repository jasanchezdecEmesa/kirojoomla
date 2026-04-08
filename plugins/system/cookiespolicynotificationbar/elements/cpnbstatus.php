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

defined('JPATH_BASE') or die;
		
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Form\FormField;

jimport( 'joomla.form.form' );

class JFormFieldcpnbstatus extends FormField {
	
	protected $type = 'cpnbstatus';

	protected function getLabel()
	{
		// BEGIN: Check if CPNB plugin exists
		jimport('joomla.plugin.helper');
		if(!PluginHelper::isEnabled('system', 'cookiespolicynotificationbar')):
			return Text::_('<div style="border:1px solid red; padding:10px; width: 50%"><strong style="color:red;">The plugin is unpublished.</strong><br>The plugin should be enabled to display the input text fields for each of your active languages. Please, enable the plugin first and then try to navigate to this tab again!</div>');
		else:
			return '';	
		endif;
		// END: Check if CPNB plugin exists
	}

	protected function getInput() 
	{
		return '';
	}
	
}