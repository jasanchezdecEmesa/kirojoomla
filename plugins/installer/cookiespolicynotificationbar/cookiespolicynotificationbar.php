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
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
class plgInstallerCookiespolicynotificationbar extends CMSPlugin
{
    public function onInstallerBeforePackageDownload(&$url, &$headers)
    {
        if (parse_url($url, PHP_URL_HOST) == 'www.web357.com' || parse_url($url, PHP_URL_HOST) == 'downloads.web357.com') {

            $apikey_from_plugin_parameters = Web357Framework\Functions::getWeb357ApiKey();
            $current_url = Uri::getInstance()->toString();
            $parse = parse_url($current_url);
            $domain = isset($parse['host']) ? $parse['host'] : 'domain.com';
            $url = str_replace('?cms=j', '&cms=j', $url);
            $uri = Uri::getInstance($url);

            $item = $uri->getVar('item'); 
            if ($item !== 'cookiespolicynotificationbar')
            {
                return;
            }

            if (!empty($apikey_from_plugin_parameters))
            {
                $uri->setVar('liveupdate', 'true');
                $uri->setVar('domain', $domain);
                $uri->setVar('dlid', $apikey_from_plugin_parameters);
                $url = $uri->toString();
                $url = str_replace('?cms=', '&cms=', $url);
                $url = str_replace(' ', '+', $url);
            }
            // Watchful.net support
            elseif (isset($parse['query']) && strpos($parse['query'], 'com_watchfulli') !== false)
            {
                $apikey = $uri->getVar('key'); // get apikey from watchful settings

                if (isset($apikey) && !empty($apikey))
                {
                    $apikey = str_replace(' ', '+', $apikey);
                    $uri->setVar('liveupdate', 'com_watchfulli');
                    $uri->setVar('domain', $domain);
                    $uri->setVar('dlid', $apikey);
                    $uri->setVar('key', $apikey);
                    $url = $uri->toString();
                    $url = str_replace('?cms=', '&cms=', $url);
                }
                else
                {
                    Factory::getApplication()->enqueueMessage(Text::_('W357FRM_APIKEY_WARNING'), 'notice');
                }
            } 
            else 
            {
                // load default and current language
                $jlang = Factory::getLanguage();
                $jlang->load('plg_system_web357framework', JPATH_ADMINISTRATOR, 'en-GB', true);

                // warn about missing api key
                Factory::getApplication()->enqueueMessage(Text::_('W357FRM_APIKEY_WARNING'), 'notice');
            }
        }
        return true;
    }
}