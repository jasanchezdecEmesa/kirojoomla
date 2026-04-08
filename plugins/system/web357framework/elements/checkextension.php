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

 
defined('JPATH_BASE') or die;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;

require_once(JPATH_PLUGINS . DIRECTORY_SEPARATOR . "system" . DIRECTORY_SEPARATOR . "web357framework" . DIRECTORY_SEPARATOR . "elements" . DIRECTORY_SEPARATOR . "elements_helper.php");

jimport( 'joomla.form.form' );

class JFormFieldcheckextension extends FormField {
	
	protected $type = 'checkextension';

	protected function getLabel()
	{
		$option = (string) $this->element["option"];

		if (!empty($option) && !$this->isActive($option))
		{
            return '<div style="color:red">'.sprintf(Text::_('W357FRM_EXTENSION_IS_NOT_ACTIVE'), $option).'</div>';
		}
		else
		{
            return '<div style="color:darkgreen">'.sprintf(Text::_('W357FRM_EXTENSION_IS_ACTIVE'), $option).'</div>';
		}
	}

	// Check if the component is installed and is enabled
	public function isActive($option) // e.g. $option = com_k2
	{
		if (!empty($option))
		{
			jimport('joomla.component.helper');
			if(!ComponentHelper::isEnabled($option))
			{
				return false;
			}
			else
			{
				return true;
			}
		}
		else
		{
			die('The extension name is not detected.');
		}
		
	}

	protected function getInput() 
	{
		return '';
	}
	
}