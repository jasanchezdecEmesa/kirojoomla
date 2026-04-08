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

namespace GeoIp2\Model;

/**
 * This class provides the GeoIP2 Domain model.
 *
 * @property-read string|null $domain The second level domain associated with the
 *     IP address. This will be something like "example.com" or
 *     "example.co.uk", not "foo.example.com".
 * @property-read string $ipAddress The IP address that the data in the model is
 *     for.
 */
class Domain extends AbstractModel
{
    protected $domain;
    protected $ipAddress;

    /**
     * @ignore
     *
     * @param mixed $raw
     */
    public function __construct($raw)
    {
        parent::__construct($raw);

        $this->domain = $this->get('domain');
        $this->ipAddress = $this->get('ip_address');
    }
}
