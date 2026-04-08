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

class JFormFieldjedreview extends FormField {
	
	protected $name = 'jedreview';
	
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

	function getInput_J4()
	{
		$this->description = 'Thank you very much 💗';
		$html  = '';
		$html .= sprintf(Text::_('It would be much appreciated if you can leave a review on <a href="%s" target="_blank">Joomla! Extensions Directory</a>.'), $this->element['jed_url'],  Text::_($this->element['real_name']));

		return $html;
	}

	function getLabel_J4()
	{
		return Text::_('W357FRM_HEADER_JED_REVIEW_AND_RATING');
	}

	protected function getInput_J3()
	{
		return '';
	}

	protected function getLabel_J3()
	{	
		$html  = '';		
		
		if (!empty($this->element['jed_url']))
		{
			if (version_compare( JVERSION, "2.5", "<="))
			{
				// j25
				$html .= '<div class="w357frm_leave_review_on_jed" style="clear:both;padding-top:20px;">'.sprintf(Text::_('W357FRM_LEAVE_REVIEW_ON_JED'), $this->element['jed_url'], Text::_($this->element['real_name'])).'</div>';
			}
			else
			{
				// j3x
				$html .= '<div class="w357frm_leave_review_on_jed">'.sprintf(Text::_('W357FRM_LEAVE_REVIEW_ON_JED'), $this->element['jed_url'], Text::_($this->element['real_name'])).'</div>';
			}
		}
		
		return $html;	
	}

}