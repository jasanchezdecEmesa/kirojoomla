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
		
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Form\FormField;

jimport( 'joomla.form.form' );

class JFormFieldw357frmrk extends FormField {
	
	protected $type = 'w357frmrk';

	protected function getLabel()
	{
		return '';
	}

	protected function getInput() 
	{
		// BEGIN: Check if Web357 Framework plugin exists
		jimport('joomla.plugin.helper');
		if(!PluginHelper::isEnabled('system', 'web357framework'))
		{
			$web357framework_required_msg = Text::_('<p>The <strong>"Web357 Framework"</strong> is required for this extension and must be active. Please, download and install it from <a href="http://downloads.web357.com/?item=web357framework&type=free">here</a>. It\'s FREE!</p>');
			Factory::getApplication()->enqueueMessage($web357framework_required_msg, 'error');

			throw new \Exception('The Web357 Framework - System Plugin is required for this extension and should be enabled.');
		}
		else
		{
			// Call the Web357 Framework Helper Class
			$web357framework_class_file = JPATH_PLUGINS.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'web357framework'.DIRECTORY_SEPARATOR.'web357framework.class.php';
			if (is_file($web357framework_class_file)) 
			{
				require_once($web357framework_class_file);
				$w357frmwrk = new Web357FrameworkHelperClass;

				// API Key Checker
				$w357frmwrk->apikeyChecker();

				return '';	
			}
			else
			{
				throw new \Exception('The Web357 Framework is required for this extension. The file "'.$web357framework_class_file.'" does not exists. Please, download and install the plugin from web357.com.');
			}
		}
		// END: Check if Web357 Framework plugin exists
	}
}