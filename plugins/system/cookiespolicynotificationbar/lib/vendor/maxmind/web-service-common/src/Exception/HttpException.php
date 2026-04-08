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

namespace MaxMind\Exception;

/**
 *  This class represents an HTTP transport error.
 */
class HttpException extends WebServiceException
{
    /**
     * The URI queried.
     */
    private $uri;

    /**
     * @param string     $message    a message describing the error
     * @param int        $httpStatus the HTTP status code of the response
     * @param string     $uri        the URI used in the request
     * @param \Exception $previous   the previous exception, if any
     */
    public function __construct(
        $message,
        $httpStatus,
        $uri,
        \Exception $previous = null
    ) {
        $this->uri = $uri;
        parent::__construct($message, $httpStatus, $previous);
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function getStatusCode()
    {
        return $this->getCode();
    }
}
