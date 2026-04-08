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

class JFormFieldw357note extends FormField {
	
	protected $type = 'w357note';

	function getInput()
	{
		if (version_compare(JVERSION, '4.0', '>='))
		{
			return $this->getInput_J4();
		}
		else
		{
			return $this->getInput_J3();
		}
	}

	function getLabel()
	{
		if (version_compare(JVERSION, '4.0', '>='))
		{
			return $this->getLabel_J4();
		}
		else
		{
			return $this->getLabel_J3();
		}
	}

	protected function getLabel_J3()
	{	
		return Text::_('W357FRM_APIKEY_DESCRIPTION');
	}
	
	protected function getInput_J3()
	{
		return ' ';
	}

	protected function getLabel_J4()
	{
		return '';
	}

	protected function getInput_J4()
	{
		return Text::_('W357FRM_APIKEY_DESCRIPTION');
	}
}