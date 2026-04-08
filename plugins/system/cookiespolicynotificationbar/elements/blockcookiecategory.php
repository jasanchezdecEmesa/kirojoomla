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
use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;

jimport( 'joomla.form.form' );

class JFormFieldblockcookiecategory extends FormField {
	
	protected $type = 'blockcookiecategory';

	protected function getInput() 
	{	
		return $this->fetchElement($this->name, $this->value, $this->element);
	}

	function fetchElement($name, $value, &$node)
    {
		// get th params from db
		$db = Factory::getDBO();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('params'));
		$query->from($db->quoteName('#__extensions'));
		$query->where($db->quoteName('element').' = '.$db->quote('cookiespolicynotificationbar'));
		$query->where($db->quoteName('folder').' = '.$db->quote('system'));
		$db->setQuery($query);
		$extension_params = $db->loadResult();

		if (!empty($extension_params))
		{
			$extension_params_json = json_decode($extension_params);
			$cookie_categories_group = isset($extension_params_json->cookie_categories_group) ? $extension_params_json->cookie_categories_group : '';

			if(!empty($cookie_categories_group) && is_object($cookie_categories_group))
			{
				$options = array();
				foreach ($cookie_categories_group as $k=>$v)
				{
					$options[] = HTMLHelper::_('select.option', $v->cookie_category_id, $v->cookie_category_name);
				}

				$value = $this->value;

				// If the value is a string -> Only one result
				if (is_string($value))
				{
					$value = array($value);
				}
				elseif (is_object($value))
				{
					// If the value is an object, let's get its properties.
					$value = get_object_vars($value);
				}
			}
			else
			{
				// first installation
				$options = array();
				$options[] = HTMLHelper::_('select.option', 'required-cookies', 'Required Cookies');			
				$options[] = HTMLHelper::_('select.option', 'analytical-cookies', 'Analytical Cookies');			
				$options[] = HTMLHelper::_('select.option', 'social-media-cookies', 'Social Media');			
				$options[] = HTMLHelper::_('select.option', 'targeted-advertising-cookies', 'Targeted Advertising Cookies');
			}
		}
		else
		{
			$options = array();
			$options[] = HTMLHelper::_('select.option', 'required-cookies', 'Required Cookies');			
			$options[] = HTMLHelper::_('select.option', 'analytical-cookies', 'Analytical Cookies');			
			$options[] = HTMLHelper::_('select.option', 'social-media-cookies', 'Social Media');			
			$options[] = HTMLHelper::_('select.option', 'targeted-advertising-cookies', 'Targeted Advertising Cookies');			
		}

		return HTMLHelper::_('select.genericlist', $options, $this->name, 'class="form-select"', 'value', 'text', $value, $this->id);
    }
}