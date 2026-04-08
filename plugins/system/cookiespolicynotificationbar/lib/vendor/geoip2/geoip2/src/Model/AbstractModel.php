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
 * @ignore
 */
abstract class AbstractModel implements \JsonSerializable
{
    protected $raw;

    /**
     * @ignore
     *
     * @param mixed $raw
     */
    public function __construct($raw)
    {
        $this->raw = $raw;
    }

    /**
     * @ignore
     *
     * @param mixed $field
     */
    protected function get($field)
    {
        if (isset($this->raw[$field])) {
            return $this->raw[$field];
        }
        if (preg_match('/^is_/', $field)) {
            return false;
        }

        return null;
    }

    /**
     * @ignore
     *
     * @param mixed $attr
     */
    public function __get($attr)
    {
        if ($attr !== 'instance' && property_exists($this, $attr)) {
            return $this->$attr;
        }

        throw new \RuntimeException("Unknown attribute: $attr");
    }

    /**
     * @ignore
     *
     * @param mixed $attr
     */
    public function __isset($attr)
    {
        return $attr !== 'instance' && isset($this->$attr);
    }

    public function jsonSerialize():mixed
    {
        return $this->raw;
    }
}
