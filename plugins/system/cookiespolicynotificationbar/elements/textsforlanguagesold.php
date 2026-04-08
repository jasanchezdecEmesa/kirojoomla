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
use Joomla\CMS\Version;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Component\ComponentHelper;

class JFormFieldTextsforlanguagesold extends FormField {
	
	protected $type = 'Textsforlanguagesold';

	protected function getInput()
	{	
		return '';
	}
	
	protected function getLabel()
	{
		$html  = '';
		
		// Get language tag
		$language = Factory::getLanguage();
		$language_tag = str_replace("-", "_", $language->get('tag'));
		$language_tag = !empty($language_tag) ? $language_tag : "en_GB";
		
		// Get languages and load form
		jimport( 'joomla.language.helper' );
		$languages = JLanguageHelper::getLanguages();
		if (!empty($languages) && count($languages) > 1):
			// Get language details
			$num = 0;
			foreach ($languages as $tag => $language):
				
				// get language name
				$language_name = $language->title_native;
				$language->lang_code = str_replace('-', '_', $language->lang_code);
				
				// load form fields
				$html .= $this->loadLangFormFields($language_name, $language->lang_code, $this->value, $num);
			$num++;
			endforeach;
		else:
			// Get language details
			$language = Factory::getLanguage();
			$frontend_language_tag = ComponentHelper::getParams('com_languages')->get('site');
			$frontend_language_default_tag = $frontend_language_tag;
			$frontend_language_tag = str_replace("-", "_", $frontend_language_tag);
			$frontend_language_tag = !empty($frontend_language_tag) ? $frontend_language_tag : "en_GB";
			$lang = new stdClass();
			$lang->known_languages = Factory::getLanguage()->getKnownLanguages();
			$known_lang_name = $lang->known_languages[$frontend_language_default_tag]['name'];
			$known_lang_tag = $lang->known_languages[$frontend_language_default_tag]['tag'];
			$known_lang_name = !empty($known_lang_name) ? $known_lang_name : 'English';
			$known_lang_tag = !empty($known_lang_tag) ? $known_lang_tag : 'en-GB';
			$frontend_language_tag = !empty($frontend_language_tag) ? $frontend_language_tag : $known_lang_tag;
			$language_name = $this->getLanguageNameByTag($frontend_language_default_tag); 
			$language_name = !empty($language_name) ? str_replace(' ('.str_replace('_', '-',$language_tag).')', '', $language_name) : $known_lang_name;

			// load form fields
			$html .= $this->loadLangFormFields($language_name, $frontend_language_tag, $this->value, 0);
				
		endif;
		
		return $html;
	}

	public function getDefaultLanguageName()
	{
		$db = Factory::getDBO();
		$query = "SELECT title_native "
		."FROM #__languages "
		."WHERE published = 1"
		;
		$db->setQuery($query);
		$db->execute();

		return $db->loadResult();
	}
	
	public function getLanguageNameByTag($tag)
	{
		$db = Factory::getDBO();
		$query = "SELECT title_native "
		."FROM #__languages "
		."WHERE lang_code = '".$tag."' AND published = 1"
		;
		$db->setQuery($query);
		$db->execute();
		$result = $db->loadResult();

		if ($result !== null)
		{
			// If there are more than one language
			return $result;
		}
		else
		{
			// If there is only one language
			return $this->getDefaultLanguageName();
		}
	}

	public function getLanguageImage($lang_code)
	{
		$db = Factory::getDBO();
		$query = "SELECT image "
		."FROM #__languages "
		."WHERE lang_code = '".$lang_code."' AND published = 1"
		;
		$db->setQuery($query);
		$db->execute();
		$result = $db->loadResult();
		
		// If there are more than one language
		if ($result !== null):
			return $result;
		// If there is only one language
		else:
			return '';
		endif;

	}
	
	public function loadLangFormFields($language_name = "English", $lang_code = "en_GB", $value = '', $num = 0)
	{
		$html = '';

		// get default site language name by tag.
		$frontend_language_tag = ComponentHelper::getParams('com_languages')->get('site');
		$default_site_language_name = $this->getLanguageNameByTag($frontend_language_tag);

		if (!empty($default_site_language_name) && $default_site_language_name == $language_name):
			$display_language_name = $language_name." (Default Site language)";
		else:
			$display_language_name = $language_name;
		endif;
		
		// flag
		$juri_base = str_replace('/administrator', '', Uri::base());
		
		// Get Joomla! version
		$jversion = new Version();
		$short_version = explode('.', $jversion->getShortVersion()); // 3.8.10
		$mini_version = $short_version[0].'.'.$short_version[1]; // 3.8
		if (!version_compare( $mini_version, "2.5", "<=")) :
			// joomla 3.x
			$lang_code_img = strtolower($lang_code);
		else:
			// joomla 2.5
			$lang_code_img = strtolower(substr($lang_code, 0, 2));
		endif;
		
		$lang_img = "<img src='".$juri_base."media/mod_languages/images/".$lang_code_img.".gif' alt='".$language_name."' title='".$language_name."' style='margin-right: 5px;' />";
	
		// get plugin params
		$db = Factory::getDBO();
		$db->setQuery("SELECT params FROM #__extensions WHERE element = 'cookiespolicynotificationbar' AND folder = 'system'");
		$plugin = $db->loadObject();
		$params = new Registry();
		$params->loadString($plugin->params);
		
		// header
		$html .= '<div style="display:none;" class="w357frm_param_header w357_small_header vj34x com_plugins" style="margin-top: '.($num>0?30:0).'px;"><label id="jform_params___field8-lbl" for="jform_params___field8" class="">'.$lang_img.' '.Text::_('J357_PLG_SYSTEM_COOKIESPOLICYNOTIFICATIONBAR_TEXTS_FOR').' '.$display_language_name.'</label></div>';

		# Lang Param 1 (header_message)
		$header_message_value_old = $params->get('header_message_'.$lang_code);
		$header_message_value_new = (isset($value['header_message_'.$lang_code])) ? $value['header_message_'.$lang_code] : $header_message_value_old;

		// get the value from params
		if (!empty($header_message_value_new)):
			$header_message_value = $header_message_value_new;
		elseif (!empty($header_message_value_old)):
			$header_message_value = $header_message_value_old;
		else:	
			$header_message_value = Text::_('J357_PLG_SYSTEM_COOKIESPOLICYNOTIFICATIONBAR_DEFAULT_MESSAGE');
		endif;
		
		$html .= '<label style="display:none;position: relative; top: 30px;" id="jform_params_textsforlanguages_header_message_'.$lang_code.'-lbl" for="jform_params_textsforlanguages_header_message_'.$lang_code.'" class="hasTooltip" title="" data-original-title="<strong>'.Text::_('J357_PLG_SYSTEM_COOKIESPOLICYNOTIFICATIONBAR_HEADER_MESSAGE_LBL').'</strong><br />'.Text::_('J357_PLG_SYSTEM_COOKIESPOLICYNOTIFICATIONBAR_HEADER_MESSAGE_DESC').'">'.Text::_('J357_PLG_SYSTEM_COOKIESPOLICYNOTIFICATIONBAR_HEADER_MESSAGE_LBL').'</label>';
		$html .= '<div class="controls"><textarea style="display:none;" name="jform[params][textsforlanguages][header_message_'.$lang_code.']" id="jform_params_textsforlanguages_header_message_'.$lang_code.'" rows="6" cols="50" filter="raw">'.$header_message_value.'</textarea></div>';
		
		# Lang Param 2 (buttonText)
		$button_text_value_old = $params->get('buttonText_'.$lang_code);
		$button_text_value_new = (isset($value['button_text_'.$lang_code])) ? $value['button_text_'.$lang_code] : $button_text_value_old;

		// get the value from params
		if (!empty($button_text_value_new)):
			$button_text_value = $button_text_value_new;
		elseif (!empty($button_text_value_old)):
			$button_text_value = $button_text_value_old;
		else:	
			$button_text_value = Text::_('J357_PLG_SYSTEM_COOKIESPOLICYNOTIFICATIONBAR_BUTTON_DEFAULT_TEXT_VALUE');
		endif;
		
		$html .= '<label style="display:none;position: relative; top: 30px;" id="jform_params_textsforlanguages_button_text_'.$lang_code.'-lbl" for="jform_params_textsforlanguages_button_text_'.$lang_code.'" class="hasTooltip" title="" data-original-title="<strong>'.Text::_('J357_PLG_SYSTEM_COOKIESPOLICYNOTIFICATIONBAR_BUTTON_TEXT_LBL').'</strong><br />'.Text::_('J357_PLG_SYSTEM_COOKIESPOLICYNOTIFICATIONBAR_BUTTON_TEXT_DESC').'">'.Text::_('J357_PLG_SYSTEM_COOKIESPOLICYNOTIFICATIONBAR_BUTTON_TEXT_LBL').'</label>';
		$html .= '<div class="controls"><input type="hidden" name="jform[params][textsforlanguages][button_text_'.$lang_code.']" id="jform_params_textsforlanguages_button_text_'.$lang_code.'" size="20" value="'.$button_text_value.'" /></div>';

		# Lang Param 3 (buttonMoreText)
		$button_more_text_value_old = $params->get('buttonMoreText_'.$lang_code);
		$button_more_text_value_new = (isset($value['button_more_text_'.$lang_code])) ? $value['button_more_text_'.$lang_code] : $button_more_text_value_old;

		// get the value from params
		if (!empty($button_more_text_value_new)):
			$button_more_text_value = $button_more_text_value_new;
		elseif (!empty($button_more_text_value_old)):
			$button_more_text_value = $button_more_text_value_old;
		else:	
			$button_more_text_value = Text::_('J357_PLG_SYSTEM_COOKIESPOLICYNOTIFICATIONBAR_BUTTON_MORETEXT_DEFAULT_VALUE');
		endif;
		
		$html .= '<label style="display:none;position: relative; top: 30px;" id="jform_params_textsforlanguages_button_more_text_'.$lang_code.'-lbl" for="jform_params_textsforlanguages_button_more_text_'.$lang_code.'" class="hasTooltip" title="" data-original-title="<strong>'.Text::_('J357_PLG_SYSTEM_COOKIESPOLICYNOTIFICATIONBAR_BUTTON_MORETEXT_LBL').'</strong><br />'.Text::_('J357_PLG_SYSTEM_COOKIESPOLICYNOTIFICATIONBAR_BUTTON_MORETEXT_DESC').'">'.Text::_('J357_PLG_SYSTEM_COOKIESPOLICYNOTIFICATIONBAR_BUTTON_MORETEXT_LBL').'</label>';
		$html .= '<div class="controls"><input type="hidden" name="jform[params][textsforlanguages][button_more_text_'.$lang_code.']" id="jform_params_textsforlanguages_button_more_text_'.$lang_code.'" size="20" value="'.$button_more_text_value.'" /></div>';

		# Lang Param 4 (buttonMoreLink)
		$button_more_link_value_old = $params->get('buttonMoreLink_'.$lang_code);
		$button_more_link_value_new = (isset($value['button_more_link_'.$lang_code])) ? $value['button_more_link_'.$lang_code] : $button_more_link_value_old;

		// get the value from params
		if (!empty($button_more_link_value_new)):
			$button_more_link_value = $button_more_link_value_new;
		elseif (!empty($button_more_link_value_old)):
			$button_more_link_value = $button_more_link_value_old;
		else:	
			$button_more_link_value = '';
		endif;
		
		$html .= '<label style="display:none;position: relative; top: 30px;" id="jform_params_textsforlanguages_button_more_link_'.$lang_code.'-lbl" for="jform_params_textsforlanguages_button_more_link_'.$lang_code.'" class="hasTooltip" title="" data-original-title="<strong>'.Text::_('J357_PLG_SYSTEM_COOKIESPOLICYNOTIFICATIONBAR_BUTTON_MORELINK_LBL').'</strong><br />'.Text::_('J357_PLG_SYSTEM_COOKIESPOLICYNOTIFICATIONBAR_BUTTON_MORELINK_DESC').'">'.Text::_('J357_PLG_SYSTEM_COOKIESPOLICYNOTIFICATIONBAR_BUTTON_MORELINK_LBL').'</label>';
		$html .= '<div class="controls"><input type="hidden" name="jform[params][textsforlanguages][button_more_link_'.$lang_code.']" id="jform_params_textsforlanguages_button_more_link_'.$lang_code.'" size="20" value="'.$button_more_link_value.'" /></div>';

		return $html;
	}
}