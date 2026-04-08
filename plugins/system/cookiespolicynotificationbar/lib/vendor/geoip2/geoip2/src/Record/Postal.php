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

namespace GeoIp2\Record;

/**
 * Contains data for the postal record associated with an IP address.
 *
 * This record is returned by all location databases and services besides
 * Country.
 *
 * @property-read string|null $code The postal code of the location. Postal codes
 * are not available for all countries. In some countries, this will only
 * contain part of the postal code. This attribute is returned by all location
 * databases and services besides Country.
 * @property-read int|null $confidence A value from 0-100 indicating MaxMind's
 * confidence that the postal code is correct. This attribute is only
 * available from the Insights service and the GeoIP2 Enterprise
 * database.
 */
class Postal extends AbstractRecord
{
    /**
     * @ignore
     */
    protected $validAttributes = ['code', 'confidence'];
}
