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
// No direct access
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\HTML\Helpers\Sidebar;
use Joomla\CMS\Toolbar\ToolbarHelper;

jimport('joomla.application.component.view');

class CookiespolicynotificationbarViewLogs extends \Joomla\CMS\MVC\View\HtmlView
{
	protected $items;

	protected $pagination;

	protected $state;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
        $this->filterForm = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');
		$this->link_to_pro = '<a href="https://www.web357.com/product/cookies-policy-notification-bar-joomla-plugin?utm_source=CLIENT&amp;utm_medium=CLIENT-CPNB-ProLink-web357&amp;utm_content=CLIENT-CPNB-ProLink&amp;utm_campaign=CPNB_radiofelement_free_vs_pro" target="_blank">PRO</a>';

		// get plugin's parameters
		$plugin = PluginHelper::getPlugin('system', 'cookiespolicynotificationbar');
		$this->plugin_params = $plugin ? new Registry($plugin->params) : [];

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		CookiespolicynotificationbarHelper::addSubmenu('cookiespolicynotificationbar');

		$this->addToolbar();

		$this->sidebar = '';
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 *
	 * @since    1.6
	 */
	protected function addToolbar()
	{
		$state = $this->get('State');
		$canDo = CookiespolicynotificationbarHelper::getActions();

		ToolbarHelper::title(Text::_('COM_COOKIESPOLICYNOTIFICATIONBAR_TITLE_COOKIESPOLICYNOTIFICATIONBAR'), 'lock.png');

		if ($canDo->get('core.delete'))
		{
			if (isset($this->items[0]))
			{
				// If this component does not use state then show a direct delete button as we can not trash
				ToolbarHelper::deleteList('', 'logs.delete', 'JTOOLBAR_DELETE');

				// If this component does not use state then show a direct delete button as we can not trash
				ToolbarHelper::custom('logs.deleteAllLogs', 'purge.png', 'purge_f2.png', 'COM_COOKIESPOLICYNOTIFICATIONBAR_DELETE_ALL', false);
			}
		}

        if ($canDo->get('core.admin')) 
		{
			ToolbarHelper::custom('logs.exportLogsInCsvFile', 'download.png', 'download_f2.png', 'COM_COOKIESPOLICYNOTIFICATIONBAR_EXPORT_LOGS_IN_CSV_FORMAT', false);
		}

        if ($canDo->get('core.admin')) 
		{
            //  get the ID of plugin
            $db = Factory::getDBO();
            $query = "SELECT extension_id "
            ."FROM #__extensions "
            ."WHERE type='plugin' AND element='cookiespolicynotificationbar' AND folder='system' "
            ;
            $db->setQuery($query);
            $db->execute();
            $extension_id = (int)$db->loadResult();
            ToolbarHelper::link(Route::_('index.php?option=com_plugins&task=plugin.edit&extension_id='.$extension_id), 'COM_COOKIESPOLICYNOTIFICATIONBAR_PLUGIN_SETTINGS', 'options');
        }

		// Set sidebar action - New in 3.0
		if (version_compare(JVERSION, '5.0', '>=')) {
			Sidebar::setAction('index.php?option=com_cookiespolicynotificationbar&view=logs');
		}
		else
		{
			JHtmlSidebar::setAction('index.php?option=com_cookiespolicynotificationbar&view=logs');
		}
	}

	/**
	 * Method to order fields 
	 *
	 * @return void 
	 */
	protected function getSortFields()
	{
		return array(
			'a.`id`' => Text::_('JGRID_HEADING_ID'),
			'a.`status`' => Text::_('status'),
			'a.`datetime`' => Text::_('datetime'),
		);
	}

    /**
     * Check if state is set
     *
     * @param   mixed  $state  State
     *
     * @return bool
     */
    public function getState($state)
    {
        return isset($this->state->{$state}) ? $this->state->{$state} : false;
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
	public function getDataFromGeoIP($ip = '')
	{
		// require geoip2 library
		require_once(JPATH_PLUGINS . '/system/cookiespolicynotificationbar/lib/geoip2/geoip2.php');
		require_once(JPATH_PLUGINS . '/system/cookiespolicynotificationbar/lib/vendor/autoload.php');
	
		$data = [
			'country_name' => '',
			'country_code' => '',
			'continent' => ''
		];

		if (empty($ip))
		{
			return $data;
		}

		// If the GeoIP provider is not loaded return "XX" (no country detected)
		if (!class_exists('Web357GeoIp2'))
		{
			return $data;
		}

		// Use GeoIP to detect the country
		$geoip = new \Web357GeoIp2();

		$data = [
			'country_name' => $geoip->getCountryName($ip),
			'country_code' => $geoip->getCountryCode($ip),
			'continent' => $geoip->getContinent($ip)
		];

		return $data;
	}
}
