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
// No direct access.
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

jimport('joomla.application.component.controlleradmin');

class CookiespolicynotificationbarControllerLogs extends \Joomla\CMS\MVC\Controller\AdminController
{
	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    Optional. Model name
	 * @param   string  $prefix  Optional. Class prefix
	 * @param   array   $config  Optional. Configuration array for model
	 *
	 * @return  object	The Model
	 *
	 * @since    1.6
	 */
	public function getModel($name = 'log', $prefix = 'CookiespolicynotificationbarModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}

	public function deleteAllLogs()
	{	
		$view = Factory::getApplication()->input->get('view', 'logs', 'STRING');

		// Delete all logs
		$db = Factory::getDBO();
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__plg_system_cookiespolicynotificationbar_logs'));
		$db->setQuery($query);
		$db->execute();
 
		// Redirect to the list screen.
		$this->setRedirect(Route::_('index.php?option=com_cookiespolicynotificationbar&view='.$view, false), Text::_('COM_COOKIESPOLICYNOTIFICATIONBAR_ALL_LOGS_DELETED_SUCCESSFULLY'), 'message');
	}

	public function exportLogsInCsvFile()
	{	
		$view = Factory::getApplication()->input->get('view', 'logs', 'STRING');

		$db = Factory::getDBO();
		$query = $db->getQuery(true);
		$query
			->select($db->quoteName('logs.id'))
			->select($db->quoteName('logs.ip_address'))
			->select($db->quoteName('logs.user_id'))
			->select($db->quoteName('logs.status'))
			->select($db->quoteName('logs.datetime'))
			->select($db->quoteName('logs.cookiesinfo'))
			->select($db->quoteName('users.name'))
			->select($db->quoteName('users.username'))
			->from($db->quoteName('#__plg_system_cookiespolicynotificationbar_logs', 'logs'))
			->join('LEFT', $db->quoteName('#__users', 'users') . ' ON ' . $db->quoteName('users.id') . ' = ' . $db->quoteName('logs.user_id'))
			->order('logs.datetime DESC');
		$db->setQuery($query);
		$logs = $db->loadObjectList();

		// file name (e.g. cookies_consent_logs_20220107135054.csv)
		$datetime_now = preg_replace('/[:\s-]+/i', '', Factory::getDate()->toSql());
		$csv_file_name = 'cookies_consent_logs_'.$datetime_now;

		// build the array of data
		$data_arr = [];

		// heading
		$data_arr[0] = 'ID,IP Address,User ID,Status,Cookies Info,Date Time';

		$num = 1;
		foreach ($logs as $log)
		{
			$cookiesinfo = str_replace(',', " | ", $log->cookiesinfo);

			$log_array = [
				$log->id,
				$log->ip_address,
				$log->user_id,
				$log->status,
				$cookiesinfo,
				$log->datetime
			];

			$data_arr[$num] = implode(',', $log_array);
			$num++;
		}

		// Export in a .csv file
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment; filename="'.$csv_file_name.'.csv"');
		$fp = fopen('php://output', 'wb');
		foreach ( $data_arr as $line ) {
			$val = explode(",", $line);
			fputcsv($fp, $val);
		}
		fclose($fp);

		exit('');

		// Redirect to the list screen.
		$this->setRedirect(Route::_('index.php?option=com_cookiespolicynotificationbar&view='.$view, false), Text::_('COM_COOKIESPOLICYNOTIFICATIONBAR_ALL_LOGS_EXPORTED_SUCCESSFULLY'), 'message');
	}
}