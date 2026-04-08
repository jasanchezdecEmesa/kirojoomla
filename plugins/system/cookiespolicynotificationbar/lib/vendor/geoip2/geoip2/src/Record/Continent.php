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
 * Contains data for the continent record associated with an IP address.
 *
 * This record is returned by all location services and databases.
 *
 * @property-read string|null $code A two character continent code like "NA" (North
 * America) or "OC" (Oceania). This attribute is returned by all location
 * services and databases.
 * @property-read int|null $geonameId The GeoName ID for the continent. This
 * attribute is returned by all location services and databases.
 * @property-read string|null $name Returns the name of the continent based on the
 * locales list passed to the constructor. This attribute is returned by all location
 * services and databases.
 * @property-read array|null $names An array map where the keys are locale codes
 * and the values are names. This attribute is returned by all location
 * services and databases.
 */
class Continent extends AbstractPlaceRecord
{
    /**
     * @ignore
     */
    protected $validAttributes = [
        'code',
        'geonameId',
        'names',
    ];
}
