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

use Joomla\CMS\Factory;
use Joomla\Utilities\IpHelper;
use Joomla\CMS\Version;
use Joomla\Filesystem\File;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Form\Form;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Language\LanguageHelper;

class plgSystemCookiesPolicyNotificationBar extends CMSPlugin
{
    protected $_plugin;
	protected $_params;

	// joomla vars
	var $user = '';
	var $host = '';
	var $base_url = '';
	var $current_url = '';
	var $session = '';
	var $customizer = '';
	var $isIframe = '';
	var $p = '';

	// joomla config
	var $joomla_caching = '';

	// get params
	var $position = '';
	var $show_close_x_icon = '';
	var $hide_after_time = '';
	var $duration = '';
	var $animate_duration = '';
	var $limit = '';
	var $header_message = '';
	var $buttonText = '';
	var $buttonMoreText = '';
	var $buttonMoreLink = '';
	var $btn_font_size = '';
	var $btn_border_radius = '';
	var $ok_btn_normal_font_color = '';
	var $ok_btn_hover_font_color = '';
	var $ok_btn_normal_bg_color = '';
	var $ok_btn_hover_bg_color = '';
	var $decline_btn_normal_font_color = '';
	var $decline_btn_hover_font_color = '';
	var $decline_btn_normal_bg_color = '';
	var $decline_btn_hover_bg_color = '';
	var $cancel_btn_normal_font_color = '';
	var $cancel_btn_hover_font_color = '';
	var $cancel_btn_normal_bg_color = '';
	var $cancel_btn_hover_bg_color = '';
	var $settings_btn_normal_font_color = '';
	var $settings_btn_hover_font_color = '';
	var $settings_btn_normal_bg_color = '';
	var $settings_btn_hover_bg_color = '';
	var $more_btn_normal_font_color = '';
	var $more_btn_hover_font_color = '';
	var $more_btn_normal_bg_color = '';
	var $more_btn_hover_bg_color = '';
	var $fontColor = '';
	var $linkColor = '';
	var $fontSize = '';
	var $backgroundColor = '';
	var $borderColor = '';
	var $borderWidth = '';
	var $body_cover = '';
	var $overlay_state = '';
	var $overlay_color = '';
	var $center_alignment = '';
	var $height = '';
	var $cookie_name = '';
	var $show_in_iframes = '';
	var $custom_css = '';
	var $buttons_ordering = '';
	var $custom_js = '';
	var $include_continents = '';
	var $exclude_continents = '';
	var $include_countries = '';
	var $exclude_countries = '';
	var $include_menu_items_show_notification_bar = '';
	var $exclude_menu_items_show_notification_bar = '';
	var $include_menu_items = '';
	var $exclude_menu_items = '';
	var $blockCookies = '';
	var $allowSessionCookies = '';
	var $autoAcceptAfterScrolling = '';
	var $numOfScrolledPixelsBeforeAutoAccept = '';
	var $reloadPageAfterAccept = '';
	var $enableConfirmationAlerts = '';
	var $enableConfirmationAlertsForAcceptBtn = '';
	var $enableConfirmationAlertsForDeclineBtn = '';
	var $enableConfirmationAlertsForDeleteBtn = '';
	var $enableGeoIP2Webservice = '';
	var $disableReCAPTCHACookies = '';
	var $enable_shortcode_functionality = '';
	var $shortcode_tag = '';
	var $store_acceptance_logs_into_db = '';
	var $store_ip_address_into_db = '';
	var $always_display = '';
	var $expiration_cookieSettings = '';
	var $expiration_cookieAccept = '';
	var $expiration_cookieDecline = '';
	var $expiration_cookieCancel = '';
	var $block_cookies_group = '';
	var $hide_cookies_from_table = '';
	var $force_allow_cookies = '';
	var $itemid = '';
	var $shortcode_is_enabled_on_this_page = '';
	var $mini_version = '';
	var $please_wait_txt = '';

	// get cpnb method
	var $dt = '';
	var $cpnb_method = '';
	var $cpnb_btn_area = '';

	// Cookies Manager (Modal Window)
	var $modalState = '';
	var $modal_include_menu_items = '';
	var $modal_exclude_menu_items = '';
	var $modalFloatButtonState = '';
	var $modalFloatButtonPosition = '';
	var $modalIsVisibleForLoggedInUsers = '';
	var $modalHashLink = '';
	var $modalMenuItemSelectedBgColor = '';
	var $modalSaveChangesButtonColorAfterChange = '';
	var $modalFloatButtonIconSrc = '';
	var $modalFloatButtonIconType = '';
	var $modalFloatButtonIconFontAwesomeLoadFromCDN = '';
	var $modalFloatButtonIconFontAwesomeName = '';
	var $modalFloatButtonIconFontAwesomeSize = '';
	var $modalFloatButtonIconFontAwesomeColor = '';
	var $modalFloatButtonIconUikitName = '';
	var $modalFloatButtonIconUikitSize = '';
	var $modalFloatButtonIconUikitColor = '';
	var $save_settings_btn_normal_font_color = '';
	var $save_settings_btn_hover_font_color = '';
	var $save_settings_btn_normal_bg_color = '';
	var $save_settings_btn_hover_bg_color = '';
	var $cookie_categories_group = '';

	// Custom Class attributes for each button
	var $accept_button_class_notification_bar = '';
	var $decline_button_class_notification_bar = '';
	var $cancel_button_class_notification_bar = '';
	var $settings_button_class_notification_bar = '';
	var $moreinfo_button_class_notification_bar = '';
	var $accept_button_class_notification_bar_modal_window = '';
	var $decline_button_class_notification_bar_modal_window = '';
	var $save_button_class_notification_bar_modal_window = '';
	var $accept_button_class_notification_bar_cookies_info_table = '';
	var $delete_button_class_notification_bar_cookies_info_table = '';
	var $reload_button_class_notification_bar_cookies_info_table = '';

	public function __construct(&$subject, $config = [])
	{
		// joomla vars
		jimport('joomla.environment.uri' );
		$this->user = (version_compare(JVERSION, "4.0", ">=")) ? Factory::getApplication()->getIdentity() : Factory::getUser();
		$this->host = Uri::root(true);

		// Get the full URL
		$this->base_url = Uri::base();
		
		// Get the current language tag (e.g., "en-GB")
		$currentLangTag = Factory::getLanguage()->getTag();

		// Get the active language object using the current language tag
		$activeLang = LanguageHelper::getLanguages('lang_code', $currentLangTag);

		// Check if the active language array is not empty and contains the current language tag
		if (!empty($activeLang) && isset($activeLang[$currentLangTag]))
		{
			// Get the SEF (Search Engine Friendly) language code
			$langCode = $activeLang[$currentLangTag]->sef;

			// Assuming your language URLs are structured with the language code directly after the base URL
			$this->base_url = rtrim($this->base_url, '/') . '/' . $langCode . '/';
		}
		
		$this->current_url = Uri::current();
		$this->session = Factory::getSession();
		$this->customizer = Factory::getApplication()->input->post->get('customizer', '', 'string');

		// get plugin params
		$db = Factory::getDBO();	
		$db->setQuery("SELECT params FROM #__extensions WHERE element = 'cookiespolicynotificationbar' AND folder = 'system'");
		$this->_plugin = $db->loadObject();
		$this->_params = new Registry();
		$this->_params->loadString($this->_plugin->params);

		// get joomla config
		$config = Factory::getConfig();
		if ($config instanceof \Joomla\Registry\Registry) {
			$config = $config->toArray();
			$config_cache = (int) isset($config['caching']) ? $config['caching'] : 0;
		}
		else
		{
			$config_cache = $config->get('caching');
		}

		$this->joomla_caching = $config_cache || $this->isPageCachePluginEnabled() ? 1 : 0;

		// get params
		$this->position = $this->_params->get('position', 'bottom');
		$this->show_close_x_icon = $this->_params->get('show_close_x_icon', '1');
		$this->hide_after_time = $this->_params->get('hide_after_time', 'yes');
		$this->duration = $this->_params->get('duration', '60');
		$this->animate_duration = $this->_params->get('animate_duration', '1000');
		$this->limit = $this->_params->get('limit', '0');	
		$this->btn_font_size = $this->_params->get('btn_font_size', '12px');
		$this->btn_border_radius = $this->_params->get('btn_border_radius', '4px');
		$this->ok_btn_normal_font_color = $this->_params->get('ok_btn_normal_font_color', '#FFFFFF');
		$this->ok_btn_hover_font_color = $this->_params->get('ok_btn_hover_font_color', '#FFFFFF');
		$this->ok_btn_normal_bg_color = $this->_params->get('ok_btn_normal_bg_color', 'rgba(59, 137, 199, 1)');
		$this->ok_btn_hover_bg_color = $this->_params->get('ok_btn_hover_bg_color', 'rgba(49, 118, 175, 1)');
		$this->decline_btn_normal_font_color = $this->_params->get('decline_btn_normal_font_color', '#FFFFFF');
		$this->decline_btn_hover_font_color = $this->_params->get('decline_btn_hover_font_color', '#FFFFFF');
		$this->decline_btn_normal_bg_color = $this->_params->get('decline_btn_normal_bg_color', 'rgba(119, 31, 31, 1)');
		$this->decline_btn_hover_bg_color = $this->_params->get('decline_btn_hover_bg_color', 'rgba(175, 38, 20, 1)');
		$this->cancel_btn_normal_font_color = $this->_params->get('cancel_btn_normal_font_color', '#FFFFFF');
		$this->cancel_btn_hover_font_color = $this->_params->get('cancel_btn_hover_font_color', '#FFFFFF');
		$this->cancel_btn_normal_bg_color = $this->_params->get('cancel_btn_normal_bg_color', 'rgba(90, 90, 90, 1)');
		$this->cancel_btn_hover_bg_color = $this->_params->get('cancel_btn_hover_bg_color', 'rgba(54, 54, 54, 1)');
		$this->settings_btn_normal_font_color = $this->_params->get('settings_btn_normal_font_color', '#FFFFFF');
		$this->settings_btn_hover_font_color = $this->_params->get('settings_btn_hover_font_color', '#FFFFFF');
		$this->settings_btn_normal_bg_color = $this->_params->get('settings_btn_normal_bg_color', 'rgba(90, 90, 90, 1)');
		$this->settings_btn_hover_bg_color = $this->_params->get('settings_btn_hover_bg_color', 'rgba(54, 54, 54, 1)');
		$this->more_btn_normal_font_color = $this->_params->get('more_btn_normal_font_color', '#FFFFFF');
		$this->more_btn_hover_font_color = $this->_params->get('more_btn_hover_font_color', '#FFFFFF');
		$this->more_btn_normal_bg_color = $this->_params->get('more_btn_normal_bg_color', 'rgba(123, 138, 139, 1)');
		$this->more_btn_hover_bg_color = $this->_params->get('more_btn_hover_bg_color', 'rgba(105, 118, 119, 1)');
		$this->fontColor = $this->_params->get('fontColor', '#F1F1F3');
		$this->linkColor = $this->_params->get('linkColor', '#FFFFFF');
		$this->fontSize = $this->_params->get('fontSize', '12px');
		$this->body_cover = $this->_params->get('body_cover', '1');
		$this->overlay_state = $this->_params->get('overlay_state', '0');
		$this->overlay_color = $this->_params->get('overlay_color', 'rgba(10, 10, 10, 0.3)');
		$this->center_alignment = $this->_params->get('center_alignment', '0');
		$this->backgroundColor = $this->_params->get('backgroundColor', 'rgba(50, 58, 69, 1)');
		$this->borderColor = $this->_params->get('borderColor', 'rgba(32, 34, 38, 1)');
		$this->borderWidth = $this->_params->get('borderWidth', '1');
		$this->custom_css = $this->_params->get('custom_css', '');
		$this->custom_js = $this->_params->get('custom_js', '');
		$this->height = $this->_params->get('height', '');
		$this->cookie_name = $this->_params->get('cookie_name', 'cookiesDirective');
		$this->show_in_iframes = $this->_params->get('show_in_iframes', '0');
		$this->include_continents = $this->_params->get('include_continents', '');
		$this->exclude_continents = $this->_params->get('exclude_continents', '');
		$this->include_countries = $this->_params->get('include_countries', '');
		$this->exclude_countries = $this->_params->get('exclude_countries', '');
		$this->include_menu_items_show_notification_bar = $this->_params->get('include_menu_items_show_notification_bar', '');
		$this->exclude_menu_items_show_notification_bar = $this->_params->get('exclude_menu_items_show_notification_bar', '');
		$this->include_menu_items = $this->_params->get('include_menu_items', '');
		$this->exclude_menu_items = $this->_params->get('exclude_menu_items', '');
		$this->blockCookies = $this->_params->get('blockCookies', '0');	
		$this->allowSessionCookies = $this->_params->get('allowSessionCookies', '1');
		$this->autoAcceptAfterScrolling = $this->_params->get('autoAcceptAfterScrolling', '0');	
		$this->numOfScrolledPixelsBeforeAutoAccept = $this->_params->get('numOfScrolledPixelsBeforeAutoAccept', '300');	
		$this->reloadPageAfterAccept = $this->_params->get('reloadPageAfterAccept', '0');	
		$this->enableConfirmationAlerts = $this->_params->get('enableConfirmationAlerts', '0');	
		$this->enableConfirmationAlertsForAcceptBtn = $this->_params->get('enableConfirmationAlertsForAcceptBtn', '0');
		$this->enableConfirmationAlertsForDeclineBtn = $this->_params->get('enableConfirmationAlertsForDeclineBtn', '1');
		$this->enableConfirmationAlertsForDeleteBtn = $this->_params->get('enableConfirmationAlertsForDeleteBtn', '1');
		$this->enableGeoIP2Webservice = $this->_params->get('enableGeoIP2Webservice', '0');	
		$this->disableReCAPTCHACookies = $this->_params->get('disableReCAPTCHACookies', '0');	
		$this->enable_shortcode_functionality = $this->_params->get('enable_shortcode_functionality', '0');
		$this->shortcode_tag = $this->_params->get('shortcode_tag', 'cookiesinfo');
		$this->store_acceptance_logs_into_db = $this->_params->get('store_acceptance_logs_into_db', '1');
		$this->store_ip_address_into_db = $this->_params->get('store_ip_address_into_db', '1');
		$this->always_display = $this->_params->get('always_display', '0');
		$this->expiration_cookieSettings = $this->_params->get('expiration_cookieSettings', '365');
		$this->expiration_cookieAccept = $this->_params->get('expiration_cookieAccept', '365');
		$this->expiration_cookieDecline = $this->_params->get('expiration_cookieDecline', '180');
		$this->expiration_cookieCancel = $this->_params->get('expiration_cookieCancel', '3');
		$this->block_cookies_group = $this->_params->get('block_cookies_group', '');
		$this->hide_cookies_from_table = $this->_params->get('hide_cookies_from_table', '');

		// store force allowed cookies into array
		$force_allow_cookies_prm = $this->_params->get('force_allow_cookies', ''); // Example: _g(a|at|id)[_a-zA-Z0-9]*  OCSESSID
		if (!empty($force_allow_cookies_prm))
		{
			$force_allow_cookies = preg_replace(array('/\r\n/', '/\r\n/'), '#PH', $force_allow_cookies_prm);
			$force_allow_cookies = str_replace('\r\n', '#PH', $force_allow_cookies);
			$this->force_allow_cookies = explode('#PH', $force_allow_cookies);
		}

		// Cookies Manager (Modal Window)
		$this->modalState = $this->_params->get('modalState', '0');	
		$this->modal_include_menu_items = $this->_params->get('modal_include_menu_items', '');
		$this->modal_exclude_menu_items = $this->_params->get('modal_exclude_menu_items', '');
		$this->modalFloatButtonState = $this->_params->get('modalFloatButtonState', '1');
		$this->modalFloatButtonPosition = $this->_params->get('modalFloatButtonPosition', 'bottom_left');
		$this->modalIsVisibleForLoggedInUsers = $this->_params->get('modalIsVisibleForLoggedInUsers', '1');
		$this->modalHashLink = $this->_params->get('modalHashLink', 'cookies');	
		$this->modalMenuItemSelectedBgColor = $this->_params->get('modalMenuItemSelectedBgColor', 'rgba(200, 200, 200, 1)');	
		$this->modalSaveChangesButtonColorAfterChange = $this->_params->get('modalSaveChangesButtonColorAfterChange', 'rgba(13, 92, 45, 1)');	
		$this->modalFloatButtonIconSrc = $this->_params->get('modalFloatButtonIconSrc', 'media/plg_system_cookiespolicynotificationbar/icons/cpnb-cookies-manager-icon-1-64x64.png');	
		$this->modalFloatButtonIconType = $this->_params->get('modalFloatButtonIconType', 'image');	
		$this->modalFloatButtonIconFontAwesomeLoadFromCDN = $this->_params->get('modalFloatButtonIconFontAwesomeLoadFromCDN', '1');	
		$this->modalFloatButtonIconFontAwesomeName = $this->_params->get('modalFloatButtonIconFontAwesomeName', 'fas fa-cookie-bite');	
		$this->modalFloatButtonIconFontAwesomeSize = $this->_params->get('modalFloatButtonIconFontAwesomeSize', 'fa-lg');	
		$this->modalFloatButtonIconFontAwesomeColor = $this->_params->get('modalFloatButtonIconFontAwesomeColor', 'rgba(61, 47, 44, 0.84)');	
		$this->modalFloatButtonIconUikitName = $this->_params->get('modalFloatButtonIconUikitName', 'cog');	
		$this->modalFloatButtonIconUikitSize = $this->_params->get('modalFloatButtonIconUikitSize', '1');	
		$this->modalFloatButtonIconUikitColor = $this->_params->get('modalFloatButtonIconUikitColor', 'rgba(61, 47, 44, 0.84)');

		$this->save_settings_btn_normal_font_color = $this->_params->get('save_settings_btn_normal_font_color', '#fff');	
		$this->save_settings_btn_hover_font_color = $this->_params->get('save_settings_btn_hover_font_color', '#fff');	
		$this->save_settings_btn_normal_bg_color = $this->_params->get('save_settings_btn_normal_bg_color', 'rgba(133, 199, 136, 1)');	
		$this->save_settings_btn_hover_bg_color = $this->_params->get('save_settings_btn_hover_bg_color', 'rgba(96, 153, 100, 1)');	

		// Custom Class attributes for each button
		$this->accept_button_class_notification_bar = $this->_params->get('accept_button_class_notification_bar', 'cpnb-accept-btn');	
		$this->decline_button_class_notification_bar = $this->_params->get('decline_button_class_notification_bar', 'cpnb-decline-btn');	
		$this->cancel_button_class_notification_bar = $this->_params->get('cancel_button_class_notification_bar', 'cpnb-cancel-btn');	
		$this->settings_button_class_notification_bar = $this->_params->get('settings_button_class_notification_bar', 'cpnb-settings-btn');	
		$this->moreinfo_button_class_notification_bar = $this->_params->get('moreinfo_button_class_notification_bar', 'cpnb-moreinfo-btn');	
		$this->accept_button_class_notification_bar_modal_window = $this->_params->get('accept_button_class_notification_bar_modal_window', 'cpnb-accept-btn-m');	
		$this->decline_button_class_notification_bar_modal_window = $this->_params->get('decline_button_class_notification_bar_modal_window', 'cpnb-decline-btn-m');	
		$this->save_button_class_notification_bar_modal_window = $this->_params->get('save_button_class_notification_bar_modal_window', 'cpnb-save-btn-m');	
		$this->accept_button_class_notification_bar_cookies_info_table = $this->_params->get('accept_button_class_notification_bar_cookies_info_table', 'cpnb-accept-btn-cit');	
		$this->delete_button_class_notification_bar_cookies_info_table = $this->_params->get('delete_button_class_notification_bar_cookies_info_table', 'cpnb-delete-btn-cit');	
		$this->reload_button_class_notification_bar_cookies_info_table = $this->_params->get('reload_button_class_notification_bar_cookies_info_table', 'cpnb-reload-btn-cit');	

		// Check if the shortcode is enabled
		$this->shortcode_is_enabled_on_this_page = false;

		/**
		 * BEGIN: Buttons sorting
		 */
		// sample data if there the ordering has not been set yet
		$buttons_ordering_group_array_sample_data['buttons_ordering_group0'] = new stdClass;
		$buttons_ordering_group_array_sample_data['buttons_ordering_group0']->button_id = "ok";
		$buttons_ordering_group_array_sample_data['buttons_ordering_group0']->button_name = "Accept (Ok)";

		$buttons_ordering_group_array_sample_data['buttons_ordering_group1'] = new stdClass;
		$buttons_ordering_group_array_sample_data['buttons_ordering_group1']->button_id = "decline";
		$buttons_ordering_group_array_sample_data['buttons_ordering_group1']->button_name = "Decline";

		$buttons_ordering_group_array_sample_data['buttons_ordering_group2'] = new stdClass;
		$buttons_ordering_group_array_sample_data['buttons_ordering_group2']->button_id = "cancel";
		$buttons_ordering_group_array_sample_data['buttons_ordering_group2']->button_name = "Cancel (ask me later)";

		$buttons_ordering_group_array_sample_data['buttons_ordering_group3'] = new stdClass;
		$buttons_ordering_group_array_sample_data['buttons_ordering_group3']->button_id = "settings";
		$buttons_ordering_group_array_sample_data['buttons_ordering_group3']->button_name = "Settings";

		$buttons_ordering_group_array_sample_data['buttons_ordering_group4'] = new stdClass;
		$buttons_ordering_group_array_sample_data['buttons_ordering_group4']->button_id = "moreinfo";
		$buttons_ordering_group_array_sample_data['buttons_ordering_group4']->button_name = "More info";

		// data from db
		$buttons_ordering_group = $this->_params->get('buttons_ordering_group', '');

		if (!empty($buttons_ordering_group) && is_object($buttons_ordering_group))
		{
			$num=0;
			$buttons_ordering_group_jtext = array();
			foreach($buttons_ordering_group as $group=>$obj)
			{
				$buttons_ordering_group_jtext['cookie_categories_group'.$num] = new stdClass;
				$buttons_ordering_group_jtext['cookie_categories_group'.$num]->button_id = $obj->button_id;
				$buttons_ordering_group_jtext['cookie_categories_group'.$num]->button_name = Text::_($obj->button_name);
				$num++;
			}

			$buttons_ordering_group = (object) $buttons_ordering_group_jtext;
		}

		$buttons_ordering_group = (!empty($buttons_ordering_group) && is_object($buttons_ordering_group)) ? $buttons_ordering_group : (object) $buttons_ordering_group_array_sample_data;
		$buttons_ordering_arr = array();
		foreach ($buttons_ordering_group as $k=>$obj)
		{
			$buttons_ordering_arr[] = $obj->button_id;
		}
		$this->buttons_ordering = json_encode($buttons_ordering_arr);

		/**
		 * END: Buttons sorting
		 */

		// Get Joomla! version
		$jversion = new Version();
		$short_version = explode('.', $jversion->getShortVersion()); // 3.8.10
		$this->mini_version = $short_version[0].'.'.$short_version[1]; // 3.8

		// get cpnb method
		$this->dt = Factory::getApplication()->input->post->get('dt', '', 'INT');
		$this->cpnb_method = Factory::getApplication()->input->post->get('cpnb_method', '', 'STRING');
		$this->cpnb_btn_area = Factory::getApplication()->input->post->get('cpnb_btn_area', '', 'STRING');

		/**
		 * Adds a logger for the "plg_system_cookiespolicynotificationbar" plugin if JDEBUG is enabled.
		 * The logger writes log messages to a text file named "plg_system_cookiespolicynotificationbar.log.php".
		 * The logger is set to log all types of messages.
		 */
		if (JDEBUG) {
			Log::addLogger(['text_file' => 'plg_system_cookiespolicynotificationbar.log.php'], Log::ALL, ['plg_system_cookiespolicynotificationbar']);
		}

		return parent::__construct($subject, $config);
	}

	/**
	 * Gets data from an IP address
	 * 1. 'country_name' => Country Name (e.g. Greece)
	 * 2. 'country_code' => Country Code (e.g. GR)
	 * 3. 'continent' => Continent (e.g. EU) 
	 *
	 * @param   string  $ip      The IP address to look up
	 * @param   string  $type  country_name, country_code, continent.
	 *
	 * @return  mixed  A string with the name or code of the country, or the continent
	 */
	private function getDataFromGeoIP($type = 'country_name')
	{
		$result = 'XX';

		// If the GeoIP provider is not loaded return "XX" (no country detected)
		if (!class_exists('Web357GeoIp2'))
		{
			return $result;
		}

		// Get the correct IP address of the client
		$ip = IpHelper::getIp();

		if (!filter_var($ip, FILTER_VALIDATE_IP))
		{
			$ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
		}

		// Use GeoIP to detect the country
		$geoip = new \Web357GeoIp2();

		switch ($type) {
			case 'country_name':
				$result = $geoip->getCountryName($ip);
				break;
			case 'country_code':
				$result = $geoip->getCountryCode($ip);
				break;
			case 'continent':
				$result = $geoip->getContinent($ip);
				break;
			default:
				$result = $geoip->getCountryName($ip);
				break;
		}

		// If detection failed, return "XX" (no country detected)
		if (empty($result))
		{
			$result = 'XX';
		}

		return $result;
	}

	function blockCookies()
	{
		// Block Cookies (If the User does not accept the cookies policy, block all the cookies of this website.)
		if (Factory::getApplication()->isClient('site') && $this->blockCookies)
		{
			// get page body
			if (version_compare($this->mini_version, "4.0", ">="))
			{
				// j4
				$page = Factory::getApplication()->GetBody();
			}
			else
			{
				$page = JResponse::GetBody();
			}

			// Get the current language
			$lang = Factory::getLanguage();
			$lang_tag = $lang->getTag();
			$current_lang_tag = (!empty($lang_tag)) ? $lang_tag : 'en-GB';
			
			// Get cookies
			$jcookies = Factory::getApplication()->input->cookie->getArray();
			$cpnb_cookiesSettings_cookie_db_params = array();
			$cpnb_cookiesSettings_cookie_status_db_params = array('required-cookies'=>1);

			if (!empty($this->cookie_categories_group))
			{
				foreach ($this->cookie_categories_group as $group)
				{
					$cpnb_cookiesSettings_cookie_db_params[$group->cookie_category_id] = $group->cookie_category_checked_by_default;
					$cpnb_cookiesSettings_cookie_status_db_params[$group->cookie_category_id] = $group->cookie_category_status;
				}
			}

			// get cookie settings from cookies
			if (isset($jcookies['cpnb_cookiesSettings']))
			{
				$cpnb_cookiesSettings_cookie_arr = (array) json_decode($jcookies['cpnb_cookiesSettings'], true);
				if (!empty($cpnb_cookiesSettings_cookie_arr))
				{
					$cpnb_cookiesSettings_cookie = $cpnb_cookiesSettings_cookie_arr;
				}
				else
				{
					$cpnb_cookiesSettings_cookie = $cpnb_cookiesSettings_cookie_db_params;
				}
			}
			else
			{
				$cpnb_cookiesSettings_cookie = $cpnb_cookiesSettings_cookie_db_params;
			}

			// Get Block Cookies
			if (!empty($this->block_cookies_group) && is_object($this->block_cookies_group))
			{
				foreach($this->block_cookies_group as $group=>$cookie_obj)
				{
					// works only for active (Enabled) categories
					if (!empty($cookie_obj->blockcookiecategory) && isset($cpnb_cookiesSettings_cookie_status_db_params[$cookie_obj->blockcookiecategory]) && $cpnb_cookiesSettings_cookie_status_db_params[$cookie_obj->blockcookiecategory] == 1) // check if category is enabled
					{
						$cookie_category_state = 0;
						if ($cookie_obj->blockcookiecategory)
						{
							if (isset($cpnb_cookiesSettings_cookie[$cookie_obj->blockcookiecategory]))
							{
								$cookie_category_state = (int) $cpnb_cookiesSettings_cookie[$cookie_obj->blockcookiecategory];
							}
						}

						$js_code = '';
						$block_cookie_load_script_time = (isset($cookie_obj->block_cookie_load_script_time)) ? $cookie_obj->block_cookie_load_script_time : 'after';
						$block_cookie_language = (isset($cookie_obj->block_cookie_language) && !empty($cookie_obj->block_cookie_language)) ? $cookie_obj->block_cookie_language : '*';
						if ($cookie_obj->block_cookie_status && ($block_cookie_language === '*' || $block_cookie_language === $current_lang_tag) && !empty($cookie_obj->block_cookie_js_code) && $cookie_category_state === 0)
						{
							
							// if state is 0 block the cookies by blocking the js code.
							$js_code .= "\n<!-- BEGIN: ".$cookie_obj->block_cookie_name." -->\n";
							if ($block_cookie_load_script_time === 'alltime' || $block_cookie_load_script_time === 'before')
							{
								$js_code .= $cookie_obj->block_cookie_js_code;
							}
							else
							{
								$js_code .= "<!-- The cookies are disabled for the ".$cookie_obj->blockcookiecategory." category . -->";
							}
							$js_code .= "\n<!-- END: ".$cookie_obj->block_cookie_name." -->\n";
						}
						elseif ($cookie_obj->block_cookie_status && ($block_cookie_language === '*' || $block_cookie_language === $current_lang_tag) && !empty($cookie_obj->block_cookie_js_code) && ($cookie_category_state === 1 || $cookie_category_state === 2) && ($block_cookie_load_script_time === 'after' || $block_cookie_load_script_time === 'alltime'))
						{
							$js_code .= "\n<!-- BEGIN: ".$cookie_obj->block_cookie_name." -->\n";
							$js_code .= $cookie_obj->block_cookie_js_code;
							$js_code .= "\n<!-- END: ".$cookie_obj->block_cookie_name." -->\n";
						}

						// Cookie placement (in head/body)
						$block_cookie_placement = (isset($cookie_obj->block_cookie_placement)) ? $cookie_obj->block_cookie_placement : 'head';
						if ($block_cookie_placement == 'body_bottom')
						{
							$page = str_replace('</body>', $js_code.'</body>', $page);
						}
						elseif ($block_cookie_placement == 'body_top')
						{
							$page = preg_replace('/<body[^>]*>/', '$0'.$js_code, $page);
						}
						else
						{
							$page = str_replace('</head>', $js_code.'</head>', $page);
						}
					}
				}
			}

			// Block scripts under <cpnb data-cpnb-cookie-category-id="analytical-cookies"></cpnb> tags, by specific cookie categories
			$matches_arr_specific_cats = array();
			$messages_arr_specific_cats = array();
			if (!empty($this->cookie_categories_group))
			{
				foreach ($this->cookie_categories_group as $group)
				{
					$cookie_category_state = 0;
					if (isset($cpnb_cookiesSettings_cookie[$group->cookie_category_id]))
					{
						$cookie_category_state = (int) $cpnb_cookiesSettings_cookie[$group->cookie_category_id];
					}

					if ($group->cookie_category_status == 1 
							&& ($cookie_category_state === 0)
							&& !empty($group->cookie_category_id) 
							&& (in_array($group->cookie_category_checked_by_default, array(0,1))) /*ensure that the category is not locked in the plugin settings*/
							//&& ($group->cookie_category_checked_by_default == 0 && $group->cookie_category_checked_by_default != -2) /*ensure that the category is not locked in the plugin settings*/
						)
					{
						// Get content from cpnb tags <cpnb>blocked code goes here...</cpnb>
						$pattern = '/<cpnb[^>]*data-cpnb-cookie-category-id="('.$group->cookie_category_id.')"[^>]*data-cpnb-no-consent-message="([^"]*)"[^>]*>.*?<\/cpnb>/si';
						preg_match_all($pattern, $page, $matches_specific_cats);
						
						$matches_arr_specific_cats[] = array_filter($matches_specific_cats[0]);
						$messages_arr_specific_cats[] = array_filter($matches_specific_cats[2]);
					}
				}
			}
			
			if (!empty($matches_arr_specific_cats))
			{
				$matches_arr_specific_cats = array_values(array_filter($matches_arr_specific_cats));
				$messages_arr_specific_cats = array_values(array_filter($messages_arr_specific_cats));

				$code_specific_arr = array();
				if(!empty($matches_arr_specific_cats))
				{
					for ($i=0; $i<count($matches_arr_specific_cats);$i++)
					{
						if (isset($matches_arr_specific_cats[$i]))
						{
							for ($j=0; $j<count($matches_arr_specific_cats[$i]);$j++)
							{
								$noConsentMessage = isset($messages_arr_specific_cats[$i][$j]) ? $messages_arr_specific_cats[$i][$j] : '';
								$code = $matches_arr_specific_cats[$i][$j];
								$code_specific_arr[] = ['noConsentMessage' => $noConsentMessage, 'code' => $code];
							}
						}
					}
				}

				if (!empty($code_specific_arr))
				{
					foreach ($code_specific_arr as $k=>$v)
					{
						$code = $v['code'];
						$noConsentMessage = !empty($v['noConsentMessage']) ? $v['noConsentMessage'] : '<!-- The cookies have been disabled (specific category). -->';
						$page = str_replace($code, $noConsentMessage, $page);
					}
				}
			}

			// Block scripts under <cpnb></cpnb> tags, without specific cookie categories
			// Get content from cpnb tags <cpnb>blocked code goes here...</cpnb>
			preg_match_all("/<cpnb(?:[^>]*data-cpnb-no-consent-message=\"([^\"]*)\")?.*?>.*?<\/cpnb>/si", $page, $matches);
			$matches_arr = array_filter($matches[0]);
		 	$messages_arr = array_filter($matches[1]);

			$code_arr = array();
			if(!empty($matches_arr))
			{
				for ($i=0; $i<count($matches_arr);$i++)
				{
					if (isset($matches_arr[$i]))
					{
						$noConsentMessage = isset($messages_arr[$i]) ? $messages_arr[$i] : '';
						$code_arr[] = ['noConsentMessage' => $noConsentMessage, 'code' => $matches_arr[$i]];
					}
				}
			}

			if (!array_key_exists($this->cookie_name, $jcookies) || array_key_exists('cpnbCookiesDeclined', $jcookies)) // check if the cookie name exists in cookies. If does not exists, block the cookies. Also, check if the cookies are declined and then block the cookies.
			{
				if (!empty($code_arr))
				{
					foreach ($code_arr as $k=>$v)
					{
						$code = $v['code'];
						$noConsentMessage = !empty($v['noConsentMessage']) ? $v['noConsentMessage'] : '<!-- The cookies have been disabled (by cpnb tags functionality). -->';
						$page = str_replace($code, $noConsentMessage, $page);
					}
				}
			}

			$page = preg_replace('/<cpnb\sdata-cpnb-cookie-category-id="[A-za-z0-9-]+">/', '', $page);
			$page = str_replace("<cpnb>", "", $page);
			$page = str_replace("</cpnb>", "", $page);

			// set page body
			if (version_compare($this->mini_version, "4.0", ">="))
			{
				// j4
				Factory::getApplication()->SetBody($page);
			}
			else
			{
				JResponse::SetBody($page);
			}
		}
	}

	function onAfterRender()
	{
		// Access only in specific pages
		if ($this->checkIncludeExclude())
		{
			return true;
		}

		// Check if the site is offline
		if (Factory::getApplication()->getCfg('offline') == 1)
		{
			return true; 
		}

		// Check if the IP can see the plugin (notification bar)
		if (Factory::getApplication()->isClient('site') && $this->ipCanSeeThePlugin() === false)
		{
			return true;
		}

		// Check if is iFrame (for Yootheme builder)
		$this->isIframe = false;
		$server_http_referer = (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : '';
		$server_http_sec_fetch_dest = (isset($_SERVER['HTTP_SEC_FETCH_DEST']) && !empty($_SERVER['HTTP_SEC_FETCH_DEST'])) ? $_SERVER['HTTP_SEC_FETCH_DEST'] : '';
		if (strpos($server_http_referer, 'p=customizer') !== false || (isset($server_http_sec_fetch_dest) && $server_http_sec_fetch_dest == 'iframe'))
		{
			$this->isIframe = true;
		}
	
		// block cookies
		return $this->blockCookies();
	}

	function onAfterDispatch()
	{
		$document = Factory::getDocument();

		// Menu Item ID
		$this->itemid = Factory::getApplication()->input->get('Itemid', '', 'INT');

		// get plugin's id from db
		$db = Factory::getDBO();	
		$query = "SELECT extension_id FROM #__extensions WHERE type='plugin' AND element = 'cookiespolicynotificationbar' AND folder='system'";
		$db->setQuery($query);
		$db->execute();
		$extension_id = $db->loadResult();

		// get plugin's id from browser
		$layout = Factory::getApplication()->input->get('layout');
		$view = Factory::getApplication()->input->get('view');

		// Only for back-end
		if (Factory::getApplication()->isClient('administrator') && $layout == 'edit' && $view == 'plugin' && Factory::getApplication()->input->get('extension_id') == $extension_id)
		{
			// Load CSS only for the admin panel
			$document->addStyleSheet($this->host.'/plugins/system/cookiespolicynotificationbar/assets/css/admin.min.css');
			
			// add some inline css (hide the language tabs from the older version, but do not remove them)
			$inline_css_style = "ul#myTabTabs li:nth-of-type(1n+8), div#myTabContent div.tab-pane:nth-of-type(1n+8), div.pane-sliders div.panel:nth-of-type(1n+8) { display: none !important; }";
			$document->addStyleDeclaration($inline_css_style, 'text/css');
			
			// -- ########### -- //
		}

		// Access only in specific pages
		if ($this->checkIncludeExclude())
		{
			return true;
		}

		// sample data if there are not any categories yet
		$cookie_categories_group_array_sample_data['cookie_categories_group0'] = new stdClass;
		$cookie_categories_group_array_sample_data['cookie_categories_group0']->cookie_category_id = "required-cookies";
		$cookie_categories_group_array_sample_data['cookie_categories_group0']->cookie_category_name = "Required Cookies";
		$cookie_categories_group_array_sample_data['cookie_categories_group0']->cookie_category_description = "The Required or Functional cookies relate to the functionality of our websites and allow us to improve the service we offer to you through our websites, for example by allowing you to carry information across pages of our website to avoid you having to re-enter information, or by recognizing your preferences when you return to our website.";
		$cookie_categories_group_array_sample_data['cookie_categories_group0']->cookie_category_checked_by_default = "2";
		$cookie_categories_group_array_sample_data['cookie_categories_group0']->cookie_category_status = "1";

		$cookie_categories_group_array_sample_data['cookie_categories_group1'] = new stdClass;
		$cookie_categories_group_array_sample_data['cookie_categories_group1']->cookie_category_id = "analytical-cookies";
		$cookie_categories_group_array_sample_data['cookie_categories_group1']->cookie_category_name = "Analytical Cookies";
		$cookie_categories_group_array_sample_data['cookie_categories_group1']->cookie_category_description = "Analytical cookies allow us to recognize and to count the number of visitors to our website, to see how visitors move around the website when they are using it and to record which content viewers view and are interested in. This helps us to determine how frequently particular pages and advertisements are visited and to determine the most popular areas of our website. This helps us to improve the service which we offer to you by helping us make sure our users are finding the information they are looking for, by providing anonymized demographic data to third parties in order to target advertising more appropriately to you, and by tracking the success of advertising campaigns on our website.";
		$cookie_categories_group_array_sample_data['cookie_categories_group1']->cookie_category_checked_by_default = "1";
		$cookie_categories_group_array_sample_data['cookie_categories_group1']->cookie_category_status = "1";

		$cookie_categories_group_array_sample_data['cookie_categories_group2'] = new stdClass;
		$cookie_categories_group_array_sample_data['cookie_categories_group2']->cookie_category_id = "social-media-cookies";
		$cookie_categories_group_array_sample_data['cookie_categories_group2']->cookie_category_name = "Social Media";
		$cookie_categories_group_array_sample_data['cookie_categories_group2']->cookie_category_description = "These cookies allow you to share Website content with social media platforms (e.g., Facebook, Twitter, Instagram). We have no control over these cookies as they are set by the social media platforms themselves.";
		$cookie_categories_group_array_sample_data['cookie_categories_group2']->cookie_category_checked_by_default = "1";
		$cookie_categories_group_array_sample_data['cookie_categories_group2']->cookie_category_status = "1";

		$cookie_categories_group_array_sample_data['cookie_categories_group3'] = new stdClass;
		$cookie_categories_group_array_sample_data['cookie_categories_group3']->cookie_category_id = "targeted-advertising-cookies";
		$cookie_categories_group_array_sample_data['cookie_categories_group3']->cookie_category_name = "Targeted Advertising Cookies";
		$cookie_categories_group_array_sample_data['cookie_categories_group3']->cookie_category_description = "Advertising and targeting cookies are used to deliver advertisements more relevant to you, but can also limit the number of times you see an advertisement and be used to chart the effectiveness of an ad campaign by tracking users’ clicks. They can also provide security in transactions. They are usually placed by third-party advertising networks with a website operator’s permission but can be placed by the operator themselves. They can remember that you have visited a website, and this information can be shared with other organizations, including other advertisers. They cannot determine who you are though, as the data collected is never linked to your profile.";
		$cookie_categories_group_array_sample_data['cookie_categories_group3']->cookie_category_checked_by_default = "1";
		$cookie_categories_group_array_sample_data['cookie_categories_group3']->cookie_category_status = "1";

		// data from db
		$cookie_categories_group = $this->_params->get('cookie_categories_group', '');
		
		// Multilingual support for the Cookie Category Names and Descriptions in the Cookies Manager (modal window).
		if (!empty($cookie_categories_group) && is_object($cookie_categories_group))
		{
			$num=0;
			$cookie_categories_group_jtext = array();
			foreach($cookie_categories_group as $group=>$obj)
			{
				$cookie_categories_group_jtext['cookie_categories_group'.$num] = new stdClass;
				$cookie_categories_group_jtext['cookie_categories_group'.$num]->cookie_category_id = $obj->cookie_category_id;
				$cookie_categories_group_jtext['cookie_categories_group'.$num]->cookie_category_name = Text::_($obj->cookie_category_name);
				$cookie_categories_group_jtext['cookie_categories_group'.$num]->cookie_category_description = Text::_($obj->cookie_category_description);
				$cookie_categories_group_jtext['cookie_categories_group'.$num]->cookie_category_checked_by_default = $obj->cookie_category_checked_by_default;
				$cookie_categories_group_jtext['cookie_categories_group'.$num]->cookie_category_status = $obj->cookie_category_status;
			$num++;
			}

			$cookie_categories_group = (object) $cookie_categories_group_jtext;
		}

		$this->cookie_categories_group = (!empty($cookie_categories_group) && is_object($cookie_categories_group)) ? $cookie_categories_group : (object) $cookie_categories_group_array_sample_data;

	}

	function ipCanSeeThePlugin()
	{
		// geoip functionality
		if ($this->enableGeoIP2Webservice && (
			!empty($this->include_continents) || 
			!empty($this->exclude_continents) || 
			!empty($this->include_countries) || 
			!empty($this->exclude_countries)
			))
		{
			// require geoip2 library
			require_once(__DIR__ . '/lib/geoip2/geoip2.php');
			require_once(__DIR__ . '/lib/vendor/autoload.php');

			// get IP's details
			$country_name = $this->getDataFromGeoIP('country_name'); // Greece
			$country_code = $this->getDataFromGeoIP('country_code'); // GR
			$continent_code = $this->getDataFromGeoIP('continent'); // EU

			// Include Continents
			if (!empty($this->include_continents))
			{
				if (in_array($continent_code, $this->include_continents))
				{
					return true;
				}
				else
				{
					return false;
				}
			}
					
			// Exclude Continents
			if (!empty($this->exclude_continents))
			{
				if (!in_array($continent_code, $this->exclude_continents))
				{
					return true;
				}
				else
				{
					return false;
				}
			}

			// Include Countries
			if (!empty($this->include_countries))
			{
				if (in_array($country_code, $this->include_countries))
				{
					return true;
				}
				else
				{
					return false;
				}
			}
					
			// Exclude Countries
			if (!empty($this->exclude_countries))
			{
				if (!in_array($country_code, $this->exclude_countries))
				{
					return true;
				}
				else
				{
					return false;
				}
			}
		}

		return true;
	}

	function onAfterInitialise()
	{
		// Load the plugin language file
		$app = Factory::getApplication();
		$lang = Factory::getLanguage();
		$current_lang_tag = $lang->getTag();
		$extension = 'plg_system_cookiespolicynotificationbar';
		$base_dir = JPATH_SITE.'/plugins/system/cookiespolicynotificationbar/';
		$language_tag = (!empty($current_lang_tag)) ? $current_lang_tag : 'en-GB';
		$reload = true;
		$lang->load($extension, $base_dir, $language_tag, $reload);

		// Check if the site is offline
		if (Factory::getApplication()->getCfg('offline') == 1)
		{
			return true; 
		}

		// Check if the IP can see the plugin (notification bar)
		if (Factory::getApplication()->isClient('site') && $this->ipCanSeeThePlugin() === false)
		{
			return true;
		}

		// Redirect after action
		if ($this->cpnb_method == 'cpnbCookiesDeclined' || $this->cpnb_method == 'cpnbCookiesCancelled' || $this->cpnb_method == 'cpnbCookiesDeleted' || $this->cpnb_method == 'cpnbCookiesAccepted' || $this->cpnb_method == 'cpnbReload' || $this->cpnb_method == 'cpnbCookiesManagerSaveSettings')
		{
			// clean the cache
			if ($this->dt) {
				$this->cpnbCleanCache();
			}
		}

		if (Factory::getApplication()->isClient('site') && $this->blockCookies) // for frontend only
		{
			// get the value of the cookie "cpnbCookiesDeclined"
			$cpnbCookiesDeclined = Factory::getApplication()->input->cookie->get('cpnbCookiesDeclined', '', 'INT');

			// Delete all cookies
			$jcookies = Factory::getApplication()->input->cookie->getArray();

			if (isset($jcookies))
			{
				if (!isset($jcookies[$this->cookie_name]) && !isset($jcookies['cpnbCookiesCancelled']))
				{
					// block joomla session cookies
					if (!$this->allowSessionCookies)
					{
						header_remove('Set-Cookie');
					}

					foreach ($jcookies as $key=>$val)
					{
						if (
							$key != 'cpnbCookiesDeclined' && 
							$key != 'cpnbCookiesCancelled' && 
							$key != 'cpnb_cookiesSettings' && 
							//!in_array($key, $this->force_allow_cookies) &&
							// substr($key, 0, 3) != '_ga' && 
							// substr($key, 0, 4) != '_gid' && 
							// $key != 'OCSESSID' && // mojishop cart

							$this->isIframe === FALSE && // avoid block sessions when you're inside an iFrame (e.g. Yootheme builder)

							// ignore the language filter system plugin
							$key !=  $app->input->cookie->get(JApplicationHelper::getHash('language')) &&
							$key != Factory::getSession()->get('plg_system_languagefilter.language')
							)
						{

							$force_delete = true;
							if (!empty($this->force_allow_cookies))
							{
								foreach($this->force_allow_cookies as $force_allow_cookie_pattern) 
								{
									if (preg_match("/$force_allow_cookie_pattern/", $key))
									{
										$force_delete = false;
									}
								}
							}

							if (isset($jcookies[$key]) && $force_delete === true)
							{
								// Delete only the Persistent cookies and avoid Session Cookies (Avoid logged out of Joomla Administrator)
								if (!$this->allowSessionCookies || !$this->isSessionCookie($val))
								{
									setcookie($key, '', time() - 1000, '/', $this->getFullDomain()); // example: test.google.com
									setcookie($key, '', time() - 1000, '/', $this->getDomainOnly()); // example: google.com
									Factory::getApplication()->input->cookie->set($key, '', time() - 1000, Factory::getApplication()->get('cookie_path', '/'), Factory::getApplication()->get('cookie_domain'), Factory::getApplication()->isSSLConnection());
								}
							}
						}
					}
				}
			}
		}

		// SAVE SETTINGS from the COOKIES MANAGER
		if ($this->cpnb_method == 'cpnbCookiesManagerSaveSettings')
		{
			Log::add(Text::_('cpnbCookiesManagerSaveSettings'), Log::INFO, 'plg_system_cookiespolicynotificationbar');

			if (isset($jcookies))
			{
				// Store the visitor decision into the database.
				if ($this->store_acceptance_logs_into_db && isset($jcookies['cpnb_cookiesSettings']))
				{
					$this->storeDecision(Text::_('PLG_SYSTEM_CPNB_UPDATED_DB_VALUE'), $jcookies['cpnb_cookiesSettings']);
				}
				
				// block joomla session cookies
				if (!$this->allowSessionCookies)
				{
					header_remove('Set-Cookie');
				}

				// Delete all cookies
				foreach ($jcookies as $key=>$val)
				{
					if (!$this->isSessionCookie($val) && $key != $this->cookie_name && $key != 'cpnbCookiesDeclined' && $key != 'cpnbCookiesCancelled' && $key != 'cpnb_cookiesSettings') // check if has not already declined by the user 
					{
						$force_delete = true;
						if (!empty($this->force_allow_cookies))
						{
							foreach($this->force_allow_cookies as $force_allow_cookie_pattern) 
							{
								if (preg_match("/$force_allow_cookie_pattern/", $key))
								{
									$force_delete = false;
								}
							}
						}

						if (isset($jcookies[$key]) && $force_delete === true)
						{
							// Delete only the Persistent cookies and avoid Session Cookies (Avoid logged out of Joomla Administrator)
							if (!$this->allowSessionCookies || !$this->isSessionCookie($val))
							{
								setcookie($key, '', time() - 1000, '/', $this->getFullDomain()); // example: test.google.com
								setcookie($key, '', time() - 1000, '/', $this->getDomainOnly()); // example: google.com
								Factory::getApplication()->input->cookie->set($key, '', time() - 1000, Factory::getApplication()->get('cookie_path', '/'), Factory::getApplication()->get('cookie_domain'), Factory::getApplication()->isSSLConnection());
							}
						}
					}
				}
			}
		}

		// COOKIES DECLINED IN THE COOKIES MANAGER (Modal Window)
		if ($this->cpnb_method == 'cpnbCookiesDeclined' && $this->cpnb_btn_area == 'cookiesManager')
		{
			Log::add(Text::_('cpnbCookiesDeclined in the Cookies Manager'), Log::INFO, 'plg_system_cookiespolicynotificationbar');
			if (isset($jcookies))
			{
				// Store the visitor decision into the database.
				if ($this->store_acceptance_logs_into_db && isset($jcookies['cpnb_cookiesSettings']))
				{
					$this->storeDecision(Text::_('PLG_SYSTEM_CPNB_DECLINED_DB_VALUE'), $jcookies['cpnb_cookiesSettings']);
				}

				// block joomla session cookies
				if (!$this->allowSessionCookies)
				{
					header_remove('Set-Cookie');
				}

				// Delete all cookies
				foreach ($jcookies as $key=>$val)
				{
					if (!$this->isSessionCookie($val) && $key != $this->cookie_name && $key != 'cpnbCookiesDeclined' && $key != 'cpnbCookiesCancelled' && $key != 'cpnb_cookiesSettings') // check if has not already declined by the user 
					{
						$force_delete = true;
						if (!empty($this->force_allow_cookies))
						{
							foreach($this->force_allow_cookies as $force_allow_cookie_pattern) 
							{
								if (preg_match("/$force_allow_cookie_pattern/", $key))
								{
									$force_delete = false;
								}
							}
						}

						if (isset($jcookies[$key]) && $force_delete === true)
						{
							// Delete only the Persistent cookies and avoid Session Cookies (Avoid logged out of Joomla Administrator)
							if (!$this->allowSessionCookies || !$this->isSessionCookie($val))
							{
								setcookie($key, '', time() - 1000, '/', $this->getFullDomain()); // example: test.google.com
								setcookie($key, '', time() - 1000, '/', $this->getDomainOnly()); // example: google.com
								Factory::getApplication()->input->cookie->set($key, '', time() - 1000, Factory::getApplication()->get('cookie_path', '/'), Factory::getApplication()->get('cookie_domain'), Factory::getApplication()->isSSLConnection());
							}
						}
					}
				}
			}
		}		

		// COOKIES DECLINED
		if ($this->cpnb_method == 'cpnbCookiesDeclined')
		{
			Log::add(Text::_('cpnbCookiesDeclined'), Log::INFO, 'plg_system_cookiespolicynotificationbar');
			if (isset($jcookies))
			{
				// Store the visitor decision into the database.
				if ($this->store_acceptance_logs_into_db && isset($jcookies['cpnb_cookiesSettings']))
				{
					$this->storeDecision(Text::_('PLG_SYSTEM_CPNB_DECLINED_DB_VALUE'), $jcookies['cpnb_cookiesSettings']);
				}
			}
		}

		// COOKIES CANCELLED
		if ($this->cpnb_method == 'cpnbCookiesCancelled')
		{
			Log::add(Text::_('cpnbCookiesCancelled'), Log::INFO, 'plg_system_cookiespolicynotificationbar');
			// Do something with "Cancel" button
		}

		// COOKIES DELETED
		if ($this->cpnb_method == 'cpnbCookiesDeleted')
		{
			Log::add(Text::_('cpnbCookiesDeleted'), Log::INFO, 'plg_system_cookiespolicynotificationbar');
			if (isset($jcookies))
			{
				// block joomla session cookies
				if (!$this->allowSessionCookies)
				{
					header_remove('Set-Cookie');
				}

				// Delete all cookies
				foreach ($jcookies as $key=>$val)
				{
					$force_delete = true;
					if (!empty($this->force_allow_cookies))
					{
						foreach($this->force_allow_cookies as $force_allow_cookie_pattern) 
						{
							if (preg_match("/$force_allow_cookie_pattern/", $key))
							{
								$force_delete = false;
							}
						}
					}

					if (isset($jcookies[$key]) && $force_delete === true)
					{
						// Delete only the Persistent cookies and avoid Session Cookies (Avoid logged out of Joomla Administrator)
						if (!$this->allowSessionCookies || !$this->isSessionCookie($val))
						{
							setcookie($key, '', time() - 1000, '/', $this->getFullDomain()); // example: test.google.com
							setcookie($key, '', time() - 1000, '/', $this->getDomainOnly()); // example: google.com
							Factory::getApplication()->input->cookie->set($key, '', time() - 1000, Factory::getApplication()->get('cookie_path', '/'), Factory::getApplication()->get('cookie_domain'),  Factory::getApplication()->isSSLConnection());
						}
					}
				}
			}
		}

		// COOKIES ACCEPTED
		if ($this->cpnb_method == 'cpnbCookiesAccepted')
		{
			Log::add(Text::_('cpnbCookiesAccepted'), Log::INFO, 'plg_system_cookiespolicynotificationbar');
			// Store the visitor decision into the database.
			if ($this->store_acceptance_logs_into_db && isset($jcookies['cpnb_cookiesSettings']))
			{
				$this->storeDecision(Text::_('PLG_SYSTEM_CPNB_ACCEPTED_DB_VALUE'), $jcookies['cpnb_cookiesSettings']);
			}
		}

		// RELOAD PAGE
		if ($this->cpnb_method == 'cpnbReload')
		{
			Log::add(Text::_('cpnbReload'), Log::INFO, 'plg_system_cookiespolicynotificationbar');
		}

	}

	/**
	 * Display Cookies Information (if the shortcode functionality is enabled)
	 */
	public function showCookiesInformation()
	{
		// Access only in specific pages
		if ($this->checkIncludeExclude())
		{
			return ''; 
		}

		// Check if the IP can see the plugin (notification bar)
		if (Factory::getApplication()->isClient('site') && $this->ipCanSeeThePlugin() === false)
		{
			return '';
		}

		// Display Cookies (non Ajax/jQuery functionality)
		$html  = '';
		$html .= '<div class="cpnb-cookies-info cpnb-text-center">';
		$html .= $this->getShortcodeCookiesTable();
		$html .= '</div>';

		return $html;
	}

	function onBeforeCompileHead()
	{
		// Access only in specific pages
		if ($this->checkIncludeExclude())
		{
			return true;
		}

		// joomla vars
		$document = Factory::getDocument();

		// get cookies
		$jcookies = Factory::getApplication()->input->cookie->getArray();

		// Check if the site is offline
		if ($this->isOffline())
		{
			return true; 
		}

		// Check if the IP can see the plugin (notification bar)
		if (Factory::getApplication()->isClient('site') && $this->ipCanSeeThePlugin() === false)
		{
			return true;
		}

		// Include or Exclude pages Cookies Manager (Modal Window) 
		$this->checkIncludeExcludeModalCookiesManager();

		// Show or Hide the Cookies Manager (Modal Window) to the Logged in Users
		$this->checkShowOrHideToTheLoggedInUsersModalCookiesManager();

		// Do not enable the plugin in the popup window
		$cpnb_popup_window = Factory::getApplication()->input->get('cpnb');
		if ($cpnb_popup_window)
		{
			return true; 
		}

		// for frontend only and if User don't press the accept button yet.
		if (Factory::getApplication()->isClient('site') && ($this->always_display || $this->modalState || !isset($jcookies[$this->cookie_name]) || (isset($jcookies[$this->cookie_name]) && $this->enable_shortcode_functionality && $this->shortcode_is_enabled_on_this_page)) && empty($this->customizer))
		{
			// Do not display the notification bar in the same page with the shortcode cookies info table.
			// if ($this->shortcode_is_enabled_on_this_page == true)
			// {
			// 	$style = '/* BEGIN: Cookies Policy Notification Bar - J! system plugin (Powered by: Web357.com) */'."\n";
			// 	$style .= '.cpnb-inner { display: none !important; }'."\n";
			// 	$style .= '/* END: Cookies Policy Notification Bar - J! system plugin (Powered by: Web357.com) */'."\n";
			// 	$document->addStyleDeclaration($style, 'text/css');
			// }
			
			// Get language tag
			$language = Factory::getLanguage();
			$language_tag = str_replace("-", "_", $language->get('tag'));
			$language_tag = !empty($language_tag) ? $language_tag : "en_GB";

			// Load the plugin language file to get the translations of each language
			$lang = Factory::getLanguage();
			$extension = 'plg_system_cookiespolicynotificationbar';
			$base_dir = JPATH_SITE.'/plugins/system/cookiespolicynotificationbar/';
			$lang_load_tag = str_replace('_', '-', $language_tag);
			$reload = true;
			$lang->load($extension, $base_dir, $lang_load_tag, $reload);

			// Load the fontawesome icons
			if ($this->modalFloatButtonIconType == 'fontawesome_icon' && $this->modalFloatButtonIconFontAwesomeLoadFromCDN && version_compare(JVERSION, '4.0', 'lt'))
			{
				$document->addStyleSheet('//use.fontawesome.com/releases/v5.15.4/css/all.css', [], ['crossorigin' => 'anonymous', 'media' => 'all']);
			}

			// Style (CSS)
			$document->addStyleSheet($this->host.'/plugins/system/cookiespolicynotificationbar/assets/css/cpnb-style.min.css', [], ['media' => 'all']);
	
			// Vanilla JS (Non Ajax/jQuery functionality)
			$options = array();
			$attribs = array();
			$attribs['mime'] = "text/javascript";

			$document->addScript($this->host.'/plugins/system/cookiespolicynotificationbar/assets/js/cookies-policy-notification-bar.min.js', $options, $attribs);

			// Simple tooltips made of pure CSS https://kazzkiq.github.io/balloon.css/
			if ($this->modalState && $this->modalFloatButtonState)
			{
				$document->addStyleSheet($this->host.'/plugins/system/cookiespolicynotificationbar/assets/css/balloon.min.css', [], ['media' => 'all']);
			}
			
			// get params for each language
			$message_prm = $this->_params->get('header_message_'.$language_tag, Text::_('J357_PLG_SYSTEM_COOKIESPOLICYNOTIFICATIONBAR_HEADER_MESSAGE_DEFAULT'));
			$message = preg_replace("/\r\n|\r|\n/",'<br/>', $message_prm);
			$ok_btn = $this->_params->get('ok_btn_'.$language_tag, '1');
			$button_text = $this->_params->get('button_text_'.$language_tag, Text::_('J357_PLG_SYSTEM_COOKIESPOLICYNOTIFICATIONBAR_BUTTON_DEFAULT_TEXT_VALUE'));
			$decline_btn = $this->_params->get('decline_btn_'.$language_tag, '1');
			$decline_btn_text = $this->_params->get('decline_btn_text_'.$language_tag, Text::_('PLG_SYSTEM_CPNB_DECLINE_BTN_DEFAULT_TEXT_VALUE'));
			$cancel_btn = $this->_params->get('cancel_btn_'.$language_tag, '0');
			$cancel_btn_text = $this->_params->get('cancel_btn_text_'.$language_tag, Text::_('PLG_SYSTEM_CPNB_CANCEL_BTN_DEFAULT_TEXT_VALUE'));
			$settings_btn = $this->_params->get('settings_btn_'.$language_tag, '1');
			$settings_btn_text = $this->_params->get('settings_btn_text_'.$language_tag, Text::_('PLG_SYSTEM_CPNB_SETTINGS_BTN_DEFAULT_TEXT_VALUE'));
			$more_info_btn = $this->_params->get('more_info_btn_'.$language_tag, '1');
			$button_more_text = $this->_params->get('button_more_text_'.$language_tag, Text::_('J357_PLG_SYSTEM_COOKIESPOLICYNOTIFICATIONBAR_BUTTON_MORETEXT_DEFAULT_VALUE'));
			$more_info_btn_type = $this->_params->get('more_info_btn_type_'.$language_tag, 'custom_text');
			$button_more_link = $this->_params->get('button_more_link_'.$language_tag, '');
			$cpnb_modal_menu_item = (int) $this->_params->get('cpnb_modal_menu_item_'.$language_tag, '');
			$link_target = $this->_params->get('link_target_'.$language_tag, '_self');
			$popup_width = $this->_params->get('popup_width_'.$language_tag, '800');
			$popup_height = $this->_params->get('popup_height_'.$language_tag, '600');
			$custom_text = html_entity_decode($this->_params->get('custom_text_'.$language_tag, Text::_('J357_PLG_SYSTEM_COOKIESPOLICYNOTIFICATIONBAR_CUSTOM_TEXT_DEFAULT')));
			$allow_cookies_confirmation_alert_txt = $this->_params->get('allow_cookies_confirmation_alert_txt_'.$language_tag, Text::_('PLG_SYSTEM_CPNB_ALLOW_COOKIES_CONFIRMATION'));
			$delete_cookies_confirmation_alert_txt = $this->_params->get('delete_cookies_confirmation_alert_txt_'.$language_tag, Text::_('PLG_SYSTEM_CPNB_DELETE_COOKIES_CONFIRMATION'));

			// Cookies Manager (Modal Window)
			$float_icon_button_txt = $this->_params->get('float_icon_button_txt_'.$language_tag, Text::_('PLG_SYSTEM_CPNB_FLOAT_ICON_BUTTON_TEXT_DEFAULT_TEXT'));
			$cookies_manager_heading_txt = $this->_params->get('cookies_manager_heading_txt_'.$language_tag, Text::_('PLG_SYSTEM_CPNB_COOKIES_MANAGER_HEADING_TEXT_DEFAULT_TEXT'));
			$cookies_category_checkbox_label_txt = $this->_params->get('cookies_category_checkbox_label_txt_'.$language_tag, Text::_('JENABLED'));
			$cookies_category_locked_txt = $this->_params->get('cookies_category_locked_txt_'.$language_tag, Text::_('PLG_SYSTEM_CPNB_COOKIES_CATEGORY_LOCKED_TEXT_DEFAULT'));
			$allow_cookies_btn_text = $this->_params->get('allow_cookies_btn_text_'.$language_tag, Text::_('PLG_SYSTEM_CPNB_ALLOW_COOKIES'));
			$decline_cookies_btn_text = $this->_params->get('decline_cookies_btn_text_'.$language_tag, Text::_('PLG_SYSTEM_CPNB_DECLINE_COOKIES'));
			$save_settings_btn_text = $this->_params->get('save_settings_btn_text_'.$language_tag, Text::_('PLG_SYSTEM_CPNB_SAVE_SETTINGS'));
			$locked_cookies_category_confirmation_alert_txt = $this->_params->get('locked_cookies_category_confirmation_alert_txt_'.$language_tag, Text::_('PLG_SYSTEM_CPNB_LOCKED_COOKIES_CATEGORY_CONFIRMATION'));

			// OnContentPrepare Advanced for each language.
			$article = new stdClass;
			$article->text = $custom_text;
			$context = array();
			$params = new CMSObject();
			PluginHelper::importPlugin('content');
			Factory::getApplication()->triggerEvent('onContentPrepareAdvanced', array($context, &$article, &$params, 0));
			$custom_text = $article->text;

			// build link for menu item option
			if ($cpnb_modal_menu_item > 0 && $more_info_btn_type == 'menu_item')
			{
				if (version_compare($this->mini_version, "4.0", ">="))
				{
					// j4
					$router = Factory::getApplication()->getRouter();
				}
				else
				{
					$router = JApplication::getInstance('site')->getRouter();
				}

				$url = $router->build('index.php?Itemid='.$cpnb_modal_menu_item);
				$url = $url->toString();
				$button_more_destination_url = str_replace('/administrator', '', $url);
			}
			elseif ($more_info_btn_type == 'link')
			{
				$button_more_destination_url = $button_more_link;
			}
			else
			{
				$button_more_destination_url = '';
			}

			// build the config
			$cpnb_config = array(
				'w357_joomla_caching' => $this->joomla_caching,
				'w357_position' => $this->position,
				'w357_show_close_x_icon' => $this->show_close_x_icon,
				'w357_hide_after_time' => $this->hide_after_time,
				'w357_duration' => $this->duration,
				'w357_animate_duration' => $this->animate_duration,
				'w357_limit' => $this->limit,
				'w357_message' => $message,
				'w357_display_ok_btn' => $ok_btn,
				'w357_buttonText' => $button_text,
				'w357_display_decline_btn' => $decline_btn,
				'w357_buttonDeclineText' => $decline_btn_text,
				'w357_display_cancel_btn' => $cancel_btn,
				'w357_buttonCancelText' => $cancel_btn_text,
				'w357_display_settings_btn' => ($this->modalState) ? $settings_btn : 0,
				'w357_buttonSettingsText' => $settings_btn_text,
				'w357_buttonMoreText' => $button_more_text,
				'w357_buttonMoreLink' => $button_more_destination_url,
				'w357_display_more_info_btn' => $more_info_btn,
				'w357_fontColor' => $this->fontColor,
				'w357_linkColor' => $this->linkColor,
				'w357_fontSize' => $this->fontSize,
				'w357_backgroundColor' => $this->backgroundColor,
				'w357_borderWidth' => $this->borderWidth,
				'w357_body_cover' => $this->body_cover,
				'w357_overlay_state' => $this->overlay_state,
				'w357_overlay_color' => $this->overlay_color,
				'w357_height' => $this->height,
				'w357_cookie_name' => $this->cookie_name,
				'w357_link_target' => $link_target,
				'w357_popup_width' => $popup_width,
				'w357_popup_height' => $popup_height,
				'w357_customText' => $custom_text,
				'w357_more_info_btn_type' => $more_info_btn_type,
				'w357_blockCookies' => $this->blockCookies,
				'w357_autoAcceptAfterScrolling' => $this->autoAcceptAfterScrolling,
				'w357_numOfScrolledPixelsBeforeAutoAccept' => $this->numOfScrolledPixelsBeforeAutoAccept,
				'w357_reloadPageAfterAccept' => $this->reloadPageAfterAccept,
				'w357_enableConfirmationAlerts' => $this->enableConfirmationAlerts,
				'w357_enableConfirmationAlertsForAcceptBtn' => (int) ($this->enableConfirmationAlerts && $this->enableConfirmationAlertsForAcceptBtn),
				'w357_enableConfirmationAlertsForDeclineBtn' => (int) ($this->enableConfirmationAlerts && $this->enableConfirmationAlertsForDeclineBtn),
				'w357_enableConfirmationAlertsForDeleteBtn' => (int) ($this->enableConfirmationAlerts && $this->enableConfirmationAlertsForDeleteBtn),
				'w357_confirm_allow_msg' => htmlspecialchars(preg_replace("/[\n\r]/", "", addslashes(Text::_($allow_cookies_confirmation_alert_txt))), ENT_QUOTES),
				'w357_confirm_delete_msg' => htmlspecialchars(preg_replace("/[\n\r]/", "", addslashes(Text::_($delete_cookies_confirmation_alert_txt))), ENT_QUOTES),
				'w357_show_in_iframes' => $this->show_in_iframes,
				'w357_shortcode_is_enabled_on_this_page' => (int) $this->shortcode_is_enabled_on_this_page,
				'w357_base_url' => $this->base_url,
				'w357_current_url' => $this->current_url,
				'w357_always_display' => $this->always_display,
				'w357_show_notification_bar' => ($this->checkIncludeExcludeShowNotificationBar() === true ? false : true),
				'w357_expiration_cookieSettings' => $this->expiration_cookieSettings,
				'w357_expiration_cookieAccept' => $this->expiration_cookieAccept,
				'w357_expiration_cookieDecline' => $this->expiration_cookieDecline,
				'w357_expiration_cookieCancel' => $this->expiration_cookieCancel,
				'w357_accept_button_class_notification_bar' => $this->accept_button_class_notification_bar,
				'w357_decline_button_class_notification_bar' => $this->decline_button_class_notification_bar,
				'w357_cancel_button_class_notification_bar' => $this->cancel_button_class_notification_bar,
				'w357_settings_button_class_notification_bar' => $this->settings_button_class_notification_bar,
				'w357_moreinfo_button_class_notification_bar' => $this->moreinfo_button_class_notification_bar,
				'w357_accept_button_class_notification_bar_modal_window' => $this->accept_button_class_notification_bar_modal_window,
				'w357_decline_button_class_notification_bar_modal_window' => $this->decline_button_class_notification_bar_modal_window,
				'w357_save_button_class_notification_bar_modal_window' => $this->save_button_class_notification_bar_modal_window,
				'w357_buttons_ordering' => $this->buttons_ordering,
			);
			$cpnb_config_js  = "\n\n// BEGIN: Cookies Policy Notification Bar - J! system plugin (Powered by: Web357.com)\n";
			$cpnb_config_js .= "var cpnb_config = " . json_encode($this->utf8ize($cpnb_config), JSON_FORCE_OBJECT | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) . ";";
			$cpnb_config_js .= "\n// END: Cookies Policy Notification Bar - J! system plugin (Powered by: Web357.com)\n";
			$document->addScriptDeclaration($cpnb_config_js);

			// build the cookies categories JS variable
			$cpnb_cookies_categories_js  = "\n\n// BEGIN: Cookies Policy Notification Bar - J! system plugin (Powered by: Web357.com)\n";
			$cpnb_cookies_categories_js .= "var cpnb_cookiesCategories = " . json_encode($this->utf8ize($this->cookie_categories_group), JSON_FORCE_OBJECT | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) . ";";
			$cpnb_cookies_categories_js .= "\n// END: Cookies Policy Notification Bar - J! system plugin (Powered by: Web357.com)\n";
			$document->addScriptDeclaration($cpnb_cookies_categories_js);

			// build the Cookies Manager (Modal Window)
			$cpnb_modalManager = array(
				// base settings
				'w357_m_modalState' => $this->modalState,
				
				'w357_m_floatButtonState' => $this->modalFloatButtonState,
				'w357_m_floatButtonPosition' => $this->modalFloatButtonPosition,
				'w357_m_HashLink' => $this->modalHashLink,

				// styling
				'w357_m_modal_menuItemSelectedBgColor' => $this->modalMenuItemSelectedBgColor,
				'w357_m_saveChangesButtonColorAfterChange' => $this->modalSaveChangesButtonColorAfterChange,
				'w357_m_floatButtonIconSrc' => (Route::_(Uri::base(), true, true)).$this->modalFloatButtonIconSrc,
				'w357_m_FloatButtonIconType' => $this->modalFloatButtonIconType,
				'w357_m_FloatButtonIconFontAwesomeName' => $this->modalFloatButtonIconFontAwesomeName,
				'w357_m_FloatButtonIconFontAwesomeSize' => $this->modalFloatButtonIconFontAwesomeSize,
				'w357_m_FloatButtonIconFontAwesomeColor' => $this->modalFloatButtonIconFontAwesomeColor,
				'w357_m_FloatButtonIconUikitName' => $this->modalFloatButtonIconUikitName,
				'w357_m_FloatButtonIconUikitSize' => $this->modalFloatButtonIconUikitSize,
				'w357_m_FloatButtonIconUikitColor' => $this->modalFloatButtonIconUikitColor,

				// texts
				'w357_m_floatButtonText' => $float_icon_button_txt,
				'w357_m_modalHeadingText' => $cookies_manager_heading_txt,
				'w357_m_checkboxText' => $cookies_category_checkbox_label_txt,
				'w357_m_lockedText' => $cookies_category_locked_txt,
				'w357_m_EnableAllButtonText' => $allow_cookies_btn_text,
				'w357_m_DeclineAllButtonText' => $decline_cookies_btn_text,
				'w357_m_SaveChangesButtonText' => $save_settings_btn_text,
				'w357_m_confirmationAlertRequiredCookies' => $locked_cookies_category_confirmation_alert_txt
			);

			$cpnb_modalManager_js  = "\n\n// BEGIN: Cookies Policy Notification Bar - J! system plugin (Powered by: Web357.com)\n";
			$cpnb_modalManager_js .= "var cpnb_manager = " . json_encode($this->utf8ize($cpnb_modalManager), JSON_FORCE_OBJECT | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) . ";";
			$cpnb_modalManager_js .= "\n// END: Cookies Policy Notification Bar - J! system plugin (Powered by: Web357.com)\n";
			$document->addScriptDeclaration($cpnb_modalManager_js);

			// Custom Javacript
			if (!empty($this->custom_js))
			{
				if (strpos($this->custom_js, '<cpnb>') !== false && strpos($this->custom_js, '</cpnb>') !== false && isset($jcookies[$this->cookie_name])) 
				{
					// Get content from cpnb tags <cpnb>blocked code goes here...</cpnb>
					$this->custom_js = preg_replace("'<cpnb>(.*?)</cpnb>'si", "$1", $this->custom_js);
					$document->addScriptDeclaration($this->custom_js);
				}
				else
				{
					$document->addScriptDeclaration($this->custom_js);
				}
			}

			// BEGIN: (CSS)
			$style  = "";
			$style .= "\n\n".'/* BEGIN: Cookies Policy Notification Bar - J! system plugin (Powered by: Web357.com) */'."\n";

			// container
			$style .= '.cpnb-outer { border-color: '.$this->borderColor.'; }'."\n";
			$style .= '.cpnb-outer.cpnb-div-position-top { border-bottom-width: '.$this->borderWidth.'px; }'."\n";
			$style .= '.cpnb-outer.cpnb-div-position-bottom { border-top-width: '.$this->borderWidth.'px; }'."\n";
			$style .= '.cpnb-outer.cpnb-div-position-top-left, .cpnb-outer.cpnb-div-position-top-right, .cpnb-outer.cpnb-div-position-bottom-left, .cpnb-outer.cpnb-div-position-bottom-right { border-width: '.$this->borderWidth.'px; }'."\n";

			// message
			$style .= '.cpnb-message { color: '.$this->fontColor.'; }'."\n";
			$style .= '.cpnb-message a { color: '.$this->linkColor.' }'."\n";

			// default button
			$style .= '.cpnb-button, .cpnb-button-ok, .cpnb-m-enableAllButton { -webkit-border-radius: '.$this->btn_border_radius.'; -moz-border-radius: '.$this->btn_border_radius.'; border-radius: '.$this->btn_border_radius.'; font-size: '.$this->btn_font_size.'; color: '.$this->ok_btn_normal_font_color.'; background-color: '.$this->ok_btn_normal_bg_color.'; }'."\n";
			$style .= '.cpnb-button:hover, .cpnb-button:focus, .cpnb-button-ok:hover, .cpnb-button-ok:focus, .cpnb-m-enableAllButton:hover, .cpnb-m-enableAllButton:focus { color: '.$this->ok_btn_hover_font_color.'; background-color: '.$this->ok_btn_hover_bg_color.'; }'."\n";
			
			// decline button
			$style .= '.cpnb-button-decline, .cpnb-button-delete, .cpnb-button-decline-modal, .cpnb-m-DeclineAllButton { color: '.$this->decline_btn_normal_font_color.'; background-color: '.$this->decline_btn_normal_bg_color.'; }'."\n";
			$style .= '.cpnb-button-decline:hover, .cpnb-button-decline:focus, .cpnb-button-delete:hover, .cpnb-button-delete:focus, .cpnb-button-decline-modal:hover, .cpnb-button-decline-modal:focus, .cpnb-m-DeclineAllButton:hover, .cpnb-m-DeclineAllButton:focus { color: '.$this->decline_btn_hover_font_color.'; background-color: '.$this->decline_btn_hover_bg_color.'; }'."\n";

			// cancel button
			$style .= '.cpnb-button-cancel, .cpnb-button-reload, .cpnb-button-cancel-modal { color: '.$this->cancel_btn_normal_font_color.'; background-color: '.$this->cancel_btn_normal_bg_color.'; }'."\n";
			$style .= '.cpnb-button-cancel:hover, .cpnb-button-cancel:focus, .cpnb-button-reload:hover, .cpnb-button-reload:focus, .cpnb-button-cancel-modal:hover, .cpnb-button-cancel-modal:focus { color: '.$this->cancel_btn_hover_font_color.'; background-color: '.$this->cancel_btn_hover_bg_color.'; }'."\n";

			// settings button
			$style .= '.cpnb-button-settings, .cpnb-button-settings-modal { color: '.$this->settings_btn_normal_font_color.'; background-color: '.$this->settings_btn_normal_bg_color.'; }'."\n";
			$style .= '.cpnb-button-settings:hover, .cpnb-button-settings:focus, .cpnb-button-settings-modal:hover, .cpnb-button-settings-modal:focus { color: '.$this->settings_btn_hover_font_color.'; background-color: '.$this->settings_btn_hover_bg_color.'; }'."\n";

			// more button
			$style .= '.cpnb-button-more-default, .cpnb-button-more-modal { color: '.$this->more_btn_normal_font_color.'; background-color: '.$this->more_btn_normal_bg_color.'; }'."\n";
			$style .= '.cpnb-button-more-default:hover, .cpnb-button-more-modal:hover, .cpnb-button-more-default:focus, .cpnb-button-more-modal:focus { color: '.$this->more_btn_hover_font_color.'; background-color: '.$this->more_btn_hover_bg_color.'; }'."\n";

			// save settings button (only for the cookies manager modal window)
			$style .= '.cpnb-m-SaveChangesButton { color: '.$this->save_settings_btn_normal_font_color.'; background-color: '.$this->save_settings_btn_normal_bg_color.'; }'."\n";
			$style .= '.cpnb-m-SaveChangesButton:hover, .cpnb-m-SaveChangesButton:focus { color: '.$this->save_settings_btn_hover_font_color.'; background-color: '.$this->save_settings_btn_hover_bg_color.'; }'."\n";

			// center alignment
			if ($this->center_alignment)
			{
				$style .= '/* center alignment */'."\n";
				$style .= '.cpnb-message { text-align: center; float: none; display: inline-block; }'."\n";
				$style .= '.cpnb-buttons { display: inline-block; float: none; margin-left: 20px; }'."\n";
				$style .= '@media (max-width: 1580px) {'."\n";
				$style .= '  .cpnb-message { float: none; display: block; width: 100%; display: block; clear: both; margin-bottom: 15px; }'."\n";
				$style .= '  .cpnb-buttons { float: none; display: block; width: 100%; clear: both; text-align: center; margin-top: 0; margin-left: 0; margin-bottom: 10px; right: 0; position: relative; }'."\n";
				$style .= '}'."\n";
			}

			// "Categories" word as a toggle menu in the modal window (for small devices)
			$style .= '@media only screen and (max-width: 600px) {'."\n";
			$style .= '.cpnb-left-menu-toggle::after, .cpnb-left-menu-toggle-button {'."\n";
			$style .= 'content: "'.Text::_('PLG_SYSTEM_CPNB_CATEGORIES').'";'."\n";
			$style .= '}'."\n";
			$style .= '}'."\n";

			// Custom CSS styling
			if (!empty($this->custom_css))
			{
				$style .= '/* custom css */'."\n";
				$style .= $this->custom_css."\n";
			}

			$style .= '/* END: Cookies Policy Notification Bar - J! system plugin (Powered by: Web357.com) */'."\n";
			$document->addStyleDeclaration($style, 'text/css');
		}

		// Unset plg_captcha_recaptcha_invisible
		if (Factory::getApplication()->isClient('site') && $this->blockCookies && $this->disableReCAPTCHACookies)
		{
			$jcookies = Factory::getApplication()->input->cookie->getArray();
			if (isset($jcookies))
			{
				if (!isset($jcookies[$this->cookie_name]))
				{
					unset(Factory::getDocument()->_scripts[Uri::root(true) . '/media/plg_captcha_recaptcha_invisible/js/recaptcha.min.js']);
					unset(Factory::getDocument()->_scripts[Uri::root(true) . '/media/plg_captcha_recaptcha/js/recaptcha.min.js']);
				}
			}
		}
	}

	/**
	 * Avoid an empty string by json_encode
	 * https://stackoverflow.com/questions/19361282/why-would-json-encode-return-an-empty-string
	 */
	public function utf8ize($text = '')
	{
		if (extension_loaded('mbstring') && extension_loaded('iconv') && !empty($text))
		{
			if (is_array($text)) 
			{
				foreach ($text as $k=>$v) 
				{
					$text[$k] = $this->utf8ize($v);
				}
			} 
			else if (is_string($text)) 
			{
				return iconv(mb_detect_encoding($text), "UTF-8", $text);
			}
		}

		return $text;
	}
	
	// Check if the site is offline
	function isOffline()
	{
		if (Factory::getApplication()->getCfg('offline') == 1) // site is offline
		{
			if ($this->user->get('id') == 0) // disable if is guest, allow if a User has access in offline website
			{
				return true;
			}
		}
	}

	// Include or Exclude pages
	function checkIncludeExcludeModalCookiesManager()
	{
		$itemid = Factory::getApplication()->input->get('Itemid');
		
		// Include
		$modal_include_menu_items = $this->modal_include_menu_items;
		
		if (!empty($modal_include_menu_items) && !in_array($itemid, $modal_include_menu_items))
		{
			$this->modalState = false; // exit if the current menu item id is not in the included menu items list.
		}
		
		// Exclude
		$modal_exclude_menu_items = $this->modal_exclude_menu_items;
		if (!empty($modal_exclude_menu_items) && in_array($itemid, $modal_exclude_menu_items))
		{
			$this->modalState = false;  // exit if the current menu item id is in the excluded menu items list.
		}
	}

	// Show or Hide the Cookies Manager (Modal Window) to the Logged in Users
	function checkShowOrHideToTheLoggedInUsersModalCookiesManager()
	{
		$user = (version_compare(JVERSION, "4.0", ">=")) ? Factory::getApplication()->getIdentity() : Factory::getUser();
		$user_id = $user->get( 'id' );
		if($user_id > 0 && $this->modalIsVisibleForLoggedInUsers == 0)
		{
			$this->modalState = false;
		}
	}

	// Include or Exclude pages
	function checkIncludeExclude()
	{
		if (Factory::getApplication()->isClient('administrator')) {
			return true;
		}

		$itemid = Factory::getApplication()->input->get('Itemid');

		// Include
		$include_menu_items = $this->include_menu_items;
		
		if (!empty($include_menu_items) && !in_array($itemid, $include_menu_items))
		{
			return true; // exit if the current menu item id is not in the included menu items list.
		}
				
		// Exclude
		$exclude_menu_items = $this->exclude_menu_items;
		if (!empty($exclude_menu_items) && in_array($itemid, $exclude_menu_items))
		{
			return true; // exit if the current menu item id is in the excluded menu items list.
		}
	}

	// Include or Exclude show notification bar
	function checkIncludeExcludeShowNotificationBar()
	{
		if (Factory::getApplication()->isClient('administrator')) {
			return true;
		}

		$itemid = Factory::getApplication()->input->get('Itemid');
		
		// Include
		$include_menu_items_show_notification_bar = $this->include_menu_items_show_notification_bar;
		
		if (!empty($include_menu_items_show_notification_bar) && !in_array($itemid, $include_menu_items_show_notification_bar))
		{
			return true; // exit if the current menu item id is not in the included menu items list.
		}
				
		// Exclude
		$exclude_menu_items_show_notification_bar = $this->exclude_menu_items_show_notification_bar;
		if (!empty($exclude_menu_items_show_notification_bar) && in_array($itemid, $exclude_menu_items_show_notification_bar))
		{
			return true; // exit if the current menu item id is in the excluded menu items list.
		}
	}
	
	/**
	 * Clean the Joomla! Cache
	 */
	public function cpnbCleanCache()
	{
		if ($this->enable_shortcode_functionality || $this->blockCookies)
		{
			Factory::getCache()->clean('com_content');
			Factory::getCache()->clean('com_k2');		
			Factory::getCache()->clean('com_modules');
			Factory::getCache()->clean('com_plugins');
			Factory::getCache()->clean('com_mijoshop');
			Factory::getCache()->clean('mod_custom');
			Factory::getCache()->clean('plg_jch_optimize');
			Factory::getCache()->clean('_system');
			Factory::getCache()->clean('page');
			Log::add(Text::_('Cache cleaned after "'.$this->cpnb_method.'" action.'), Log::INFO, 'plg_system_cookiespolicynotificationbar');
		}
	}

	/**
	 * Display Cookies Information (if the shortcode functionality is enabled)
	 */
	public function onContentPrepare($context, &$article, &$params, $page = 0)
	{
		if ($this->enable_shortcode_functionality)
		{
			// Don't run this plugin when the content is being indexed
			if ($context === 'com_finder.indexer')
			{
				return true;
			}

			// Simple performance check to determine whether bot should process further
			if (strpos($article->text, $this->shortcode_tag) === false )
			{
				return true;
			}

			// Expression to search for (cookiesinfo)
			$regex = '/{'.$this->shortcode_tag.'}/i';

			// Find all instances
			preg_match($regex, $article->text, $matches);

			// No matches, skip this
			if ($matches)
			{
				$this->shortcode_is_enabled_on_this_page = true;
				$showCookiesInformation = $this->showCookiesInformation();
				$article->text = str_replace($matches[0], $showCookiesInformation, $article->text);
			}
		}
	}

	/**
	 * Display Cookies Information (if the shortcode functionality is enabled)
	 */
	public function onContentPrepareAdvanced($context, &$article, &$params, $page = 0)
	{

		if ($this->enable_shortcode_functionality)
		{
			// Don't run this plugin when the content is being indexed
			if ($context === 'com_finder.indexer')
			{
				return true;
			}

			// Simple performance check to determine whether bot should process further
			if (strpos($article->text, $this->shortcode_tag) === false )
			{
				return true;
			}
			
			// Expression to search for (cookiesinfo)
			$regex = '/{'.$this->shortcode_tag.'}/i';

			// Find all instances
			preg_match($regex, $article->text, $matches);

			// No matches, skip this
			if ($matches)
			{
				$this->shortcode_is_enabled_on_this_page = true;
				$showCookiesInformation = $this->showCookiesInformation();
				$article->text = str_replace($matches[0], $showCookiesInformation, $article->text);
			}
		}
	}

	public function getShortcodeCookiesTable() {
		// Get Cookies
		$jcookies = Factory::getApplication()->input->cookie->getArray(); 

		// Define vars
		$html = '';
		$cookies_num = 0;

		// Load the plugin language file
		$lang = Factory::getLanguage();
		$current_lang_tag = $lang->getTag();
		$lang = Factory::getLanguage();
		$extension = 'plg_system_cookiespolicynotificationbar';
		$base_dir = JPATH_SITE.'/plugins/system/cookiespolicynotificationbar/';
		$language_tag = (!empty($current_lang_tag)) ? $current_lang_tag : 'en-GB';
		$reload = true;
		$lang->load($extension, $base_dir, $language_tag, $reload);

		// Hide Cookies from Table
		if (!empty($this->hide_cookies_from_table))
		{
			$hide_cookies_from_table_arr = preg_replace('#\s+#', '', trim($this->hide_cookies_from_table));
			$hide_cookies_from_table_arr = explode(',', $hide_cookies_from_table_arr);
		}
		else
		{
			$hide_cookies_from_table_arr = array();
		}

		// Check if the user declined
		$cpnbCookiesDeclined = Factory::getApplication()->input->cookie->get('cpnbCookiesDeclined', '', 'INT');

		// get lang tag for the variable
		$lang_tag = str_replace("-", "_", $language_tag);
		$lang_tag = !empty($lang_tag) ? $lang_tag : "en_GB";

		// Control Shortcode's content
		$shortcode_text_before_accept_or_decline = html_entity_decode($this->_params->get('shortcode_text_before_accept_or_decline_'.$lang_tag, Text::_('PLG_SYSTEM_CPNB_TEXT_BEFORE_ACCEPT_DECLINE_DEFAULT')));
		$shortcode_text_after_accept = html_entity_decode($this->_params->get('shortcode_text_after_accept_'.$lang_tag, Text::_('PLG_SYSTEM_CPNB_TEXT_AFTER_ACCEPT_DEFAULT')));
		$shortcode_text_after_decline = html_entity_decode($this->_params->get('shortcode_text_after_decline_'.$lang_tag, Text::_('PLG_SYSTEM_CPNB_TEXT_AFTER_DECLINE_DEFAULT')));
		
		// Get HTML Code for each situation
		$buttons_before_accept_or_decline = $this->showButtons($this->_params, $lang_tag, 'before_accept_or_decline'); 
		$info_table_before_accept_or_decline = $this->showCookiesInfoTable($this->_params, $lang_tag, 'before_accept_or_decline', $hide_cookies_from_table_arr);
		$buttons_after_accept = $this->showButtons($this->_params, $lang_tag, 'after_accept');
		$info_table_after_accept = $this->showCookiesInfoTable($this->_params, $lang_tag, 'after_accept', $hide_cookies_from_table_arr);
		$buttons_after_decline = $this->showButtons($this->_params, $lang_tag, 'after_decline');
		$info_table_after_decline = $this->showCookiesInfoTable($this->_params, $lang_tag, 'after_decline', $hide_cookies_from_table_arr);

		// BEFORE ACCEPT OR DECLINE
		preg_match('/{cpnb_buttons}/i', $shortcode_text_before_accept_or_decline, $matches_btns_a);
		if ($matches_btns_a)
		{
			$shortcode_text_before_accept_or_decline = str_replace($matches_btns_a[0], $buttons_before_accept_or_decline, $shortcode_text_before_accept_or_decline);
		}
		preg_match('/{cpnb_cookies_info_table}/i', $shortcode_text_before_accept_or_decline, $matches_tbl_a);
		if ($matches_tbl_a)
		{
			$shortcode_text_before_accept_or_decline = str_replace($matches_tbl_a[0], $info_table_before_accept_or_decline, $shortcode_text_before_accept_or_decline);
		}
		
		// AFTER ACCEPT
		preg_match('/{cpnb_buttons}/i', $shortcode_text_after_accept, $matches_btns_b);
		if ($matches_btns_b)
		{
			$shortcode_text_after_accept = str_replace($matches_btns_b[0], $buttons_after_accept, $shortcode_text_after_accept);
		}
		preg_match('/{cpnb_cookies_info_table}/i', $shortcode_text_after_accept, $matches_tbl_b);
		if ($matches_tbl_b)
		{
			$shortcode_text_after_accept = str_replace($matches_tbl_b[0], $info_table_after_accept, $shortcode_text_after_accept);
		}

		// AFTER DECLINE
		preg_match('/{cpnb_buttons}/i', $shortcode_text_after_decline, $matches_btns_c);
		if ($matches_btns_c)
		{
			$shortcode_text_after_decline = str_replace($matches_btns_c[0], $buttons_after_decline, $shortcode_text_after_decline);
		}
		preg_match('/{cpnb_cookies_info_table}/i', $shortcode_text_after_decline, $matches_tbl_c);
		if ($matches_tbl_c)
		{
			$shortcode_text_after_decline = str_replace($matches_tbl_c[0], $info_table_after_decline, $shortcode_text_after_decline);
		}

		foreach ($jcookies as $cookie_name=>$cookie_value)
		{
			if (isset($jcookies[$cookie_name]))
			{
				// Display only Persistent cookies and avoid Session Cookies
				// Also do not display the cpnbCookiesDeclined cookie in the list
				if (!$this->isSessionCookie($cookie_value) && !in_array($cookie_name, $hide_cookies_from_table_arr) && $cookie_name != 'cpnbCookiesCancelled' && $cookie_name != 'cpnb_cookiesSettingsTemp')
				{
					$cookies_num++;
				}
			}
		}

		if (isset($jcookies[$this->cookie_name]) || isset($jcookies['cpnbCookiesDeclined']) || isset($jcookies['cpnbCookiesCancelled']))
		{
			if ($cookies_num == 0 && !$cpnbCookiesDeclined)
			{
				$html = $shortcode_text_before_accept_or_decline;
			}
			elseif ($cookies_num > 0 && !$cpnbCookiesDeclined)
			{
				// display the table only if there are cookies
				if ($cookies_num == 1 && isset($jcookies[$this->cookie_name]))
				{
					$html = $shortcode_text_after_accept;
				}
				elseif(isset($jcookies['cpnbCookiesCancelled']) || isset($jcookies['cpnb_cookiesSettingsTemp']))
				{
					$html = $shortcode_text_before_accept_or_decline;
				}
				elseif ($cookies_num == 1 && (!isset($jcookies[$this->cookie_name]) || !isset($jcookies['cpnbCookiesDeclined'])))
				{
					$html = $shortcode_text_before_accept_or_decline;
				}
				else
				{
					$html = $shortcode_text_after_accept;
				}
			}
			elseif ($cookies_num > 0 && $cpnbCookiesDeclined)
			{
				$html = $shortcode_text_after_decline;
			}
		}
		else
		{
			$html = $shortcode_text_before_accept_or_decline;
		}
		
		return $html;
	}

	/**
	 * Get the descriptions of cookies
	 */
	public function getCookieDescription($params, $lang_tag, $cookie_name = '')
	{
		$matches = array();
		$cookie_description = '';
		if (!empty($cookie_name))
		{
			$cookie_descriptions_group = $params->get('cookie_descriptions_group', '');

			if (!empty($cookie_descriptions_group) && is_object($cookie_descriptions_group))
			{
				foreach($cookie_descriptions_group as $group=>$cookie_obj)
				{
					if ($cookie_obj->cookie_status && ($cookie_obj->cookie_name == $cookie_name || (preg_match("/".$cookie_obj->cookie_name ."/", $cookie_name, $matches))))
					{
						if (!empty($cookie_obj->cookie_description))
						{
							$cookie_description = Text::_($cookie_obj->cookie_description);
						}
					}
				}
			}

			if ($cookie_name == $this->cookie_name)
			{
				$cookie_description = Text::_($params->get('accept_cookies_descrpiption_txt_'.$lang_tag, Text::_('PLG_SYSTEM_CPNB_ACCEPT_COOKIES_DESCRIPTION_DEFAULT_TEXT')));
			}
			elseif ($cookie_name == 'cpnbCookiesDeclined')
			{
				$cookie_description = Text::_($params->get('decline_cookies_descrpiption_txt_'.$lang_tag, Text::_('PLG_SYSTEM_CPNB_DECLINE_COOKIES_DESCRIPTION_DEFAULT_TEXT')));
			}
			elseif ($cookie_name == 'cpnb_cookiesSettings')
			{
				$cookie_description = Text::_($params->get('settings_cookies_descrpiption_txt_'.$lang_tag, Text::_('PLG_SYSTEM_CPNB_SETTINGS_COOKIES_DESCRIPTION_DEFAULT_TEXT')));
			}
		}

		return $cookie_description;
	}

	/**
	 * Get the expiration of cookie
	 */
	public function getCookieExpiration($params, $lang_tag, $cookie_name = '')
	{
		$cookie_expiration = '';
		if (!empty($cookie_name))
		{
			$cookie_descriptions_group = $params->get('cookie_descriptions_group', '');

			if (!empty($cookie_descriptions_group) && is_object($cookie_descriptions_group))
			{
				foreach($cookie_descriptions_group as $group=>$cookie_obj)
				{
					if ($cookie_obj->cookie_status && ($cookie_obj->cookie_name == $cookie_name || (preg_match("/".$cookie_obj->cookie_name ."/", $cookie_name, $matches))))
					{
						if (!empty($cookie_obj->cookie_expiration_time_value) && !empty($cookie_obj->cookie_expiration_time_txt))
						{
							$cookie_expiration = $cookie_obj->cookie_expiration_time_value . ' ' . $params->get($cookie_obj->cookie_expiration_time_txt . ($cookie_obj->cookie_expiration_time_value > 1 ? 's' :'').'_txt_'.$lang_tag, Text::_('PLG_SYSTEM_CPNB_'.strtoupper($cookie_obj->cookie_expiration_time_txt . ($cookie_obj->cookie_expiration_time_value > 1 ? 's' :'')).'_DEFAULT_TEXT'));
						}
					}
				}
			}

			if ($cookie_name == $this->cookie_name)
			{
				$cookie_expiration = Text::_($this->expiration_cookieAccept). ' ' . $params->get('days_txt_'.$lang_tag, Text::_('PLG_SYSTEM_CPNB_DAYS_DEFAULT_TEXT'));
			}
			elseif ($cookie_name == 'cpnbCookiesDeclined')
			{
				$cookie_expiration = Text::_($this->expiration_cookieDecline). ' ' . $params->get('days_txt_'.$lang_tag, Text::_('PLG_SYSTEM_CPNB_DAYS_DEFAULT_TEXT'));
			}
			elseif ($cookie_name == 'cpnb_cookiesSettings')
			{
				// Get Cookies
				$jcookies = Factory::getApplication()->input->cookie->getArray();

				// output
				$cookie_expiration = '';

				if (isset($jcookies[$this->cookie_name]))
				{
					$cookie_expiration .= Text::_($this->expiration_cookieAccept);
				}
				elseif (isset($jcookies['cpnbCookiesDeclined']))
				{
					$cookie_expiration .= Text::_($this->expiration_cookieDecline);
				}
				elseif (isset($jcookies['cpnbCookiesCancelled']))
				{
					$cookie_expiration .= Text::_($this->expiration_cookieCancel);
				}
				elseif (isset($jcookies['cpnb_cookiesSettings']))
				{
					$cookie_expiration .= Text::_($this->expiration_cookieSettings);
				}

				$cookie_expiration .= ' '.$params->get('days_txt_'.$lang_tag, Text::_('PLG_SYSTEM_CPNB_DAYS_DEFAULT_TEXT'));
			}
		}

		return $cookie_expiration;
	}

	/**
	 * Display the Info Table
	 * 
	 * STATUS
	 * before_accept_or_decline
	 * after_accept
	 * after_decline
	 */
	private function showCookiesInfoTable($params, $lang_tag, $status = '', $hide_cookies_from_table_arr = array())
	{
		// Other texts for translations
		$no_cookies_here_txt = $params->get('no_cookies_here_txt_'.$lang_tag, Text::_('PLG_SYSTEM_CPNB_NO_COOKIES_HERE'));
		
		// define vars
		$html = '';
		$html_row = '';
		$cookies_num = 0;

		// Get Cookies
		$jcookies = Factory::getApplication()->input->cookie->getArray();
	
		foreach ($jcookies as $cookie_name=>$cookie_value)
		{
			if (isset($jcookies[$cookie_name]))
			{
				// Display only Persistent cookies and avoid Session Cookies
				if (!$this->isSessionCookie($cookie_value) && !in_array($cookie_name, $hide_cookies_from_table_arr))
				{
					$cookie_description = $this->getCookieDescription($params, $lang_tag, $cookie_name);
					$cookie_expiration = $this->getCookieExpiration($params, $lang_tag, $cookie_name);

					$html_row .= '<tr>';
					$html_row .= '<td class="cpnb-cookie-name-col" data-label="'.Text::_('PLG_SYSTEM_CPNB_COOKIE_NAME').'">'.$cookie_name.'</td>';
					$html_row .= '<td class="cpnb-cookie-value-col" data-label="'.Text::_('PLG_SYSTEM_CPNB_COOKIE_VALUE').'">'.$cookie_value.'</td>';
					$html_row .= '<td class="cpnb-cookie-expiration-col" data-label="'.Text::_('PLG_SYSTEM_CPNB_COOKIE_EXPIRATION').'">'.(!empty($cookie_expiration) ? $cookie_expiration : Text::_('---')).'</td>';
					$html_row .= '<td class="cpnb-cookie-desc-col" data-label="'.Text::_('PLG_SYSTEM_CPNB_COOKIE_DESCRIPTION').'">'.(!empty($cookie_description) ? $cookie_description : Text::_('PLG_SYSTEM_CPNB_COOKIE_EMPTY_DESCRIPTION')).'</td>';

					$cookies_num++;
				}
			}
		}

		// display the table only if there are cookies
		if (isset($jcookies[$this->cookie_name]) || isset($jcookies['cpnbCookiesDeclined']) || isset($jcookies['cpnbCookiesCancelled']))
		{
			// Cookies are enabled - Delete
			$html .= '<div class="cpnb-margin cpnb-cookies-table-container">';
			$html .= '<table width="100%" border="1" cellpadding="5" cellspacing="5" class="cpnb-cookies-table">';
			$html .= '<thead>';
			$html .= '<tr>';
			$html .= '<th class="cpnb-cookie-name-heading-col" scope="col">'.Text::_('PLG_SYSTEM_CPNB_COOKIE_NAME').'</th>';
			$html .= '<th class="cpnb-cookie-value-heading-col" scope="col">'.Text::_('PLG_SYSTEM_CPNB_COOKIE_VALUE').'</th>';
			$html .= '<th class="cpnb-cookie-expiration-heading-col" scope="col">'.Text::_('PLG_SYSTEM_CPNB_COOKIE_EXPIRATION').'</th>';
			$html .= '<th class="cpnb-cookie-desc-heading-col" scope="col">'.Text::_('PLG_SYSTEM_CPNB_COOKIE_DESCRIPTION').'</th>';
			$html .= '</tr>';
			$html .= '</thead>';
			$html .= '<tbody>';
			$html .= $html_row;
			$html .= '</tbody>';
			$html .= '</table>';
			$html .= '</div>';
		}
		else
		{
			$html .= '<div class="cpnb-margin cpnb-no-cookies-here">';
			$html .= Text::_($no_cookies_here_txt);
			$html .= '</div>';
		}

		return $html;
	}

	/**
	 * Display the buttons
	 * 
	 * STATUS
	 * before_accept_or_decline
	 * after_accept
	 * after_decline
	 */
	private function showButtons($params, $lang_tag, $status = '')
	{
		// Other texts for translations
		$allow_cookies_btn_text = $params->get('allow_cookies_btn_text_'.$lang_tag, Text::_('PLG_SYSTEM_CPNB_ALLOW_COOKIES'));
		$delete_cookies_btn_text = $params->get('delete_cookies_btn_text_'.$lang_tag, Text::_('PLG_SYSTEM_CPNB_DELETE_COOKIES'));
		$reload_cookies_btn_text = $params->get('reload_cookies_btn_text_'.$lang_tag, Text::_('PLG_SYSTEM_CPNB_RELOAD'));

		// confirmation alert texts
		$allow_cookies_confirmation_alert_txt = $this->_params->get('allow_cookies_confirmation_alert_txt_'.$lang_tag, Text::_('PLG_SYSTEM_CPNB_ALLOW_COOKIES_CONFIRMATION'));
		$delete_cookies_confirmation_alert_txt = $this->_params->get('delete_cookies_confirmation_alert_txt_'.$lang_tag, Text::_('PLG_SYSTEM_CPNB_DELETE_COOKIES_CONFIRMATION'));

		// fix encoding issues
		$allow_cookies_confirmation_alert_txt = htmlspecialchars(preg_replace("/[\n\r]/", "", addslashes(Text::_($allow_cookies_confirmation_alert_txt))), ENT_QUOTES);
		$delete_cookies_confirmation_alert_txt = htmlspecialchars(preg_replace("/[\n\r]/", "", addslashes(Text::_($delete_cookies_confirmation_alert_txt))), ENT_QUOTES);

				$current_url = Uri::getInstance(Route::_(Uri::getInstance()->toString()));
                $query_vars = $current_url->getQuery(true);
				foreach($query_vars as $var_key => $var_value){
                    //unset previous key to prevent creating new key-value pairs while sanitizing the key
                    $current_url->delVar($var_key);
                    //sanitize key
                    $var_key_replaced = strip_tags(str_replace(array("'", '"', '<', '>'), array('%27', '%22', '%3C', '%3E'), $var_key));
                    $var_key_escaped = htmlspecialchars(addslashes($var_key_replaced));
                    //sanitize value
					if (!is_array($var_value))
					{
                    	$var_value_replaced = strip_tags(str_replace(array("'", '"', '<', '>'), array('%27', '%22', '%3C', '%3E'), $var_value));
	                    $var_value_escaped = htmlspecialchars(addslashes($var_value_replaced));
					}
					else
					{
						$var_value_escaped = $var_value;
					}

					//set the sanitized key-value pair
                    $current_url->setVar($var_key_escaped, $var_value_escaped);
                }

                // allow btn
                $accept_link = clone $current_url;
                $accept_cookies_expiration = 365;
                $allow_btn = '<button id="cpnb-accept-btn-cit" onClick="cpnb_warning_accept_button(\''.($this->joomla_caching).'\', \''.($this->enableConfirmationAlerts && $this->enableConfirmationAlertsForAcceptBtn).'\', \''.Text::_($allow_cookies_confirmation_alert_txt).'\', \''.$this->cookie_name.'\', \''. $accept_link->toString().'\', \''.$accept_cookies_expiration.'\', \''.$this->current_url.'\', \'shortcode_table\', \''.$this->reloadPageAfterAccept.'\')" class="cpnb-button cpnb-button-ok cpnb-margin-right '.$this->accept_button_class_notification_bar_cookies_info_table.'">'.Text::_($allow_cookies_btn_text).'</button>';

                // decline btn
                $decline_link = clone $current_url;
                $decline_cookies_expiration = 365;
                $decline_btn = '<button id="cpnb-decline-btn-cit" onClick="cpnb_warning_decline_button(\''.($this->joomla_caching).'\', \''.($this->enableConfirmationAlerts && $this->enableConfirmationAlertsForDeclineBtn).'\', \''.Text::_($delete_cookies_confirmation_alert_txt).'\', \''.$this->cookie_name.'\', \''.$decline_link->toString().'\', \''.$decline_cookies_expiration.'\')" class="cpnb-button cpnb-button-decline cpnb-margin-right">Decline button goes here</button>';

                // reload btn
                $reload_link = clone $current_url;
                $reload_btn = '<button id="cpnb-reload-btn-cit" onclick="window.location.href=\''.$reload_link->toString().'\'" class="cpnb-button cpnb-button-reload cpnb-margin-right '.$this->reload_button_class_notification_bar_cookies_info_table.'">'.Text::_($reload_cookies_btn_text).'</button>';

                // delete btn
                $delete_link = clone $current_url;
                $delete_btn = '<button id="cpnb-delete-btn-cit" onClick="cpnb_warning_delete_button(\''.$this->joomla_caching.'\', \''.($this->enableConfirmationAlerts && $this->enableConfirmationAlertsForDeleteBtn).'\', \''.Text::_($delete_cookies_confirmation_alert_txt).'\', \''.$this->cookie_name.'\', \''.$delete_link->toString().'\')" class="cpnb-button cpnb-button-delete cpnb-margin-right '.$this->delete_button_class_notification_bar_cookies_info_table.'">'.Text::_($delete_cookies_btn_text).'</button>';

		// display
		switch ($status)
		{
			case "before_accept_or_decline":
				$html = $allow_btn;
				break;
			case "after_accept":
				$html = $delete_btn . ' ' . $reload_btn;
				break;
			case "after_decline":
				$html = $allow_btn . ' ' . $delete_btn . ' ' . $reload_btn;
				break;
			default:
				$html = $allow_btn . ' ' .$decline_btn . ' ' . $delete_btn . ' ' . $reload_btn;
		}

		return $html;
	}

	/**
	 * Get the full domain of a URL
	 * Example: for https://tests.google.com it prints tests.google.com
	 */
	public function getFullDomain()
	{
		$url = Uri::base();
		$parse_url = parse_url($url);
		return $parse_url['host'];
	}

	/**
	 * Get only the domain of a URL
	 * Example: for https://tests.google.com it prints google.com, even if its a subdomain.
	 */
	public function getDomainOnly()
	{
		$url = Uri::base();
		$pieces = parse_url($url);
		$domain = isset($pieces['host']) ? $pieces['host'] : $pieces['path'];
		if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) 
		{
			return $regs['domain'];
		}
		return false;
	}

	/**
	 * What are the different types of cookies?
	 * A cookie can be classified by its lifespan and the domain to which it belongs. By lifespan, a cookie is either a: 
	 * - session cookie which is erased when the user closes the browser or
	 * - persistent cookie which remains on the user's computer/device for a pre-defined period of time.
	 * 
	 * return: true if is a session cookie.
	 */
	public static function isSessionCookie($cookie_name)
	{
		if (!empty($cookie_name))
		{
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select('COUNT('.$db->quoteName('session_id').')');
			$query->from($db->quoteName('#__session'));
			$query->where($db->quoteName('session_id'). ' = ' . $db->quote((string) $cookie_name));

			try
			{
				$db->setQuery($query);
				return (int) $db->loadResult();
			}
			catch (RuntimeException $e)
			{
				JError::raiseError(500, $e->getMessage());
				return false;
			}
		}
		else
		{
			return false;
		}
	}

	/**
	 * Store the visitor decision into the database.
	 */
	private function storeDecision($status = 'accepted', $cookiesinfo = '') // status: accepted or declined
	{
        // Get the correct IP address of the client
        $ip_address = IpHelper::getIp();

        if (!filter_var($ip_address, FILTER_VALIDATE_IP)) {
            $ip_address = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
        }

		// Get a db connection.
		$db = Factory::getDbo();
		
		// Create a new query object.
		$query = $db->getQuery(true);
		
		// Insert columns
		$columns = array('ip_address', 'status', 'datetime', 'user_id', 'cookiesinfo');
		
		// Insert values
		$values = array(); 
        $values['ip_address'] = ($this->store_ip_address_into_db) ? $db->quote($ip_address) : $db->quote(Text::_('PLG_SYSTEM_CPNB_IP_ADDRESS_NOT_STORED'));
		$values['state'] = $db->quote(Text::_($status));
		$values['datetime'] = $db->quote(Factory::getDate()->toSql());
		$user = (version_compare(JVERSION, "4.0", ">=")) ? Factory::getApplication()->getIdentity() : Factory::getUser();
		$values['user_id'] = (int) $user->id;
		$values['cookiesinfo'] = $db->quote($cookiesinfo);
		
		// Prepare the insert query.
		$query
			->insert($db->quoteName('#__plg_system_cookiespolicynotificationbar_logs'))
			->columns($db->quoteName($columns))
			->values(implode(',', $values));
		
		// Set the query using our newly populated query object and execute it.
		$db->setQuery($query);
		$db->execute();
	}

	function onContentPrepareForm($form, $data)
	{
		$app    = Factory::getApplication();
		$option = $app->input->get('option');
		$view 	= $app->input->get('view');
		$layout = $app->input->get('layout');

		if ($app->isClient('administrator') && $option == 'com_plugins' && $view = 'plugin' && $layout == 'edit')
		{
			if (!($form instanceof Form))
			{
				throw new \RuntimeException(Text::_('Error with Form.'));
			}

			// Check we are manipulating a valid form.
			$app = Factory::getApplication();

			if ($form->getName() != 'com_plugins.plugin' || isset($data->name) && $data->name != 'PLG_SYSTEM_COOKIESPOLICYNOTIFICATIONBAR') {
				return true;
			}

			// Tooltip behavior
			HTMLHelper::_('bootstrap.tooltip');

			// get the app
			$app = Factory::getApplication();
			$document = Factory::getDocument();

			// toolbar
			$toolbar = Toolbar::getInstance('toolbar');

			// Tooltip texts
			$import_params_tooltip = HTMLHelper::_('tooltipText', Text::_('PLG_SYSTEM_CPNB_MP_IMPORT_PARAMS_LBL'), Text::_('PLG_SYSTEM_CPNB_MP_IMPORT_PARAMS_DESC'));
			$export_params_tooltip = HTMLHelper::_('tooltipText', Text::_('PLG_SYSTEM_CPNB_MP_EXPORT_PARAMS_LBL'), Text::_('PLG_SYSTEM_CPNB_MP_EXPORT_PARAMS_DESC'));

			// Import Params (button in the toolbar)
			$form_action_import_url = Uri::getInstance(Route::_(Uri::getInstance()->toString())); // current url
			$form_action_import_url->setVar('mp_task', 'importParams');
			if ($this->joomla_caching) 
			{
				$form_action_import_url->setVar('dt', date('mdhis'));
			}
			$import_params_lbl = Text::_('PLG_SYSTEM_CPNB_MP_IMPORT_PARAMS_LBL');
			
			$import_params_btn_html = <<<HTML
			<form id="adminFormWeb357" name="adminFormWeb357" action="{$form_action_import_url}" method="post" enctype="multipart/form-data">

				<a class="btn btn-info btn-sm btn-small cpnb-mp-import-params-btn hasTooltip" title="{$import_params_tooltip}">
					<span class="icon-upload icon-white" aria-hidden="true"></span> {$import_params_lbl}
				</a>
				<input name="importParams" id="importParams" type="file" style="display: none;" />
			</form>
HTML;
			$toolbar->appendButton('Custom', $import_params_btn_html);

			// Export Optionjs (button in the toolbar)
			$export_params_lbl = Text::_('PLG_SYSTEM_CPNB_MP_EXPORT_PARAMS_LBL');
			$export_params_btn_html = <<<HTML
			<button data-mp-task="exportParams" class="btn btn-small btn-sm btn-primary cpnb-mp-export-params-btn hasTooltip" title="{$export_params_tooltip}" style="margin-left: 5px;">
				<span class="icon-download icon-white" aria-hidden="true"></span> {$export_params_lbl}
			</button>
HTML;
			$toolbar->appendButton('Custom', $export_params_btn_html); 

			// Javascript code
			$export_confirmation_msg = Text::_('PLG_SYSTEM_CPNB_MP_EXPORT_PARAMS_CONFIRMATION_MSG');
			$import_confirmation_msg = Text::_('PLG_SYSTEM_CPNB_MP_IMPORT_PARAMS_CONFIRMATION_MSG');
			
			$js = <<<JS
			jQuery(document).ready(function($)
			{
				// EXPORT
				$(".cpnb-mp-export-params-btn").click(function(e){

					e.preventDefault();

					// confirmation alert
					if(!confirm('{$export_confirmation_msg}')) 
					{
						return false;
					}

					// task
					var get_data_mp_task = $(this).data("mp-task");

					// redirection
					location = window.location.href + '&mp_task=' + get_data_mp_task;
				});

				// IMPORT

				// select the json file first
				$(".cpnb-mp-import-params-btn").click(function(e){

					e.preventDefault();

					$('#importParams').trigger('click');

				});

				// submit the file (json data)
				$( "#importParams" ).change(function() {

					if(!confirm('{$import_confirmation_msg}')) 
					{
						return false;
					}
					
					document.getElementById('adminFormWeb357').submit();
				});

			});
JS;
			$document->addScriptDeclaration($js);

			// Run the task
			switch ($app->input->getString('mp_task')) 
			{
				// EXPORT SETTINGS
				case 'exportParams':
					// get th params from db
					$db = Factory::getDBO();
					$query = $db->getQuery(true);
					$query->select($db->quoteName('params'));
					$query->from($db->quoteName('#__extensions'));
					$query->where($db->quoteName('element').' = '.$db->quote('cookiespolicynotificationbar'));
					$query->where($db->quoteName('folder').' = '.$db->quote('system'));
					$db->setQuery($query);
					$extension_params = $db->loadResult();

					if (empty($extension_params))
					{
						// Success Message
						Factory::getApplication()->enqueueMessage(Text::_('PLG_SYSTEM_CPNB_MP_NO_PARAMS_ERROR_MSG'), Text::_('PLG_SYSTEM_CPNB_MP_ERROR'));

						// Get Uri
						$uri = Uri::getInstance(Route::_(Uri::getInstance()->toString()));
						$uri->delVar('mp_task');

						// Redirect
						$app->redirect($uri);
					}

					// write to file
					$domain = $this->getFullDomain();
					$file_name = "plg_system_cookiespolicynotificationbar_params_".$domain."_".date('Y-m-d-H-i-s').".json";
					$file_fullpath = JPATH_SITE . "/tmp/".$file_name;
					File::write($file_fullpath, $extension_params);

					// download file
					ob_end_clean();
					header("Content-type:application/octet-stream");
					header('Content-Length: ' . filesize($file_fullpath));
					header('Content-Disposition: attachment; filename="'.$file_name.'"');
					header('Content-Transfer-Encoding: binary');
					header('Accept-Ranges: bytes');
					readfile($file_fullpath);

					// delete file
					File::delete($file_fullpath);

					// Get Uri
					$uri = Uri::getInstance(Route::_(Uri::getInstance()->toString()));
					$uri->delVar('mp_task');

					// exit
					exit();

				break;

				// IMPORT SETTINGS
				case "importParams" :

					// Get file
					$file = Factory::getApplication()->input->files->get('importParams');

					if (empty($file))
					{
						// Success Message
						Factory::getApplication()->enqueueMessage(Text::_('PLG_SYSTEM_CPNB_MP_NO_FILE_ERROR_MSG'), Text::_('PLG_SYSTEM_CPNB_MP_ERROR'));

						// Get Uri
						$uri = Uri::getInstance(Route::_(Uri::getInstance()->toString()));
						$uri->delVar('mp_task');

						// Redirect
						$app->redirect($uri);
					}

					// Clean up filename to get rid of strange characters like spaces etc
					$filename = File::makeSafe($file['name']);

					// Get file data
					$file_contents = file_get_contents($file['tmp_name']);

					// get the file's extension here
					$file_ext = pathinfo($filename, PATHINFO_EXTENSION); // e.g. json
					$file_size = $file['size'];
					$file_error = $file['error'];
					$file_type = $file['type']; // application/json

					// Check file format/size
					if(
						($file_type != 'application/json' && $file_type != 'application/octet-stream') || 
						$file_ext != 'json' || // ensure that is a json file
						empty($file_contents) || 
						$file_size == 0 || 
						$file_error > 0) 
					{
						// Error Message
						Factory::getApplication()->enqueueMessage(Text::_('PLG_SYSTEM_CPNB_MP_ERROR_UPLOADING_JSON_FILE_MSG'), Text::_('PLG_SYSTEM_CPNB_MP_ERROR'));

						// Get Uri
						$uri = Uri::getInstance(Route::_(Uri::getInstance()->toString()));
						$uri->delVar('mp_task');

						// Redirect
						$app->redirect($uri);

						// exit
						exit();
					}

					// Insert the parameters into db
					$db = Factory::getDBO();
					$query = $db->getQuery(true);
					$query->update($db->quoteName('#__extensions'));
					// $query->set($db->quoteName('params').' = '.$db->quote($file_data[0]));
					$query->set($db->quoteName('params').' = '.$db->quote($file_contents));
					$query->where($db->quoteName('element').' = '.$db->quote('cookiespolicynotificationbar'));
					$query->where($db->quoteName('folder').' = '.$db->quote('system'));
					$db->setQuery($query);
					$db->execute();

					// Success Message
					Factory::getApplication()->enqueueMessage(Text::_('PLG_SYSTEM_CPNB_MP_SUCCESS_UPLOADED_MSG'), Text::_('PLG_SYSTEM_CPNB_MP_SUCCESS'));

					// Get Uri
					$uri = Uri::getInstance(Route::_(Uri::getInstance()->toString()));
					$uri->delVar('mp_task');

					// Redirect
					$app->redirect($uri);

					// exit
					exit();
				
				break;
			}
			
		}

		return true;
	}
		
	/**
	 * Checks if the Page Cache plugin is enabled.
	 *
	 * This function queries the Joomla database to determine if the Page Cache plugin is enabled.
	 * It retrieves the value of the 'enabled' field from the 'extensions' table for the specified plugin.
	 * If the plugin is enabled, it returns true, otherwise it returns false.
	 *
	 * @return bool Returns true if the Page Cache plugin is enabled, false otherwise.
	 */
	function isPageCachePluginEnabled() {
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		
		// Select the `enabled` field from the `extensions` table
		$query->select($db->quoteName('enabled'))
			->from($db->quoteName('#__extensions'))
			->where($db->quoteName('type') . ' = ' . $db->quote('plugin'))
			->where($db->quoteName('element') . ' = ' . $db->quote('cache'))
			->where($db->quoteName('folder') . ' = ' . $db->quote('system'));
		
		// Set the query and load the result
		$db->setQuery($query);
		$result = $db->loadResult();
		
		// $result will be 1 if the plugin is enabled, 0 otherwise
		return $result == 1;
	}

}