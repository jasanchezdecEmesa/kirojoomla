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

 
defined('JPATH_PLATFORM') or die;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Form\FormField;

class JFormFieldloadmodalbehavior extends FormField 
{
	protected $type = 'loadmodalbehavior';

	protected function getLabel()
	{
		return '';
	}

	protected function getInput() 
	{
		if (version_compare(JVERSION, '4.0', 'lt'))
		{
			HTMLHelper::_('behavior.modal');
		}
	}
}