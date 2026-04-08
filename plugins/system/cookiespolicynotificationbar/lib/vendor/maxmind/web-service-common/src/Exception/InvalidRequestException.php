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
 * Thrown when a MaxMind web service returns an error relating to the request.
 */
class InvalidRequestException extends HttpException
{
    /**
     * The code returned by the MaxMind web service.
     */
    private $error;

    /**
     * @param string     $message    the exception message
     * @param int        $error      the error code returned by the MaxMind web service
     * @param int        $httpStatus the HTTP status code of the response
     * @param string     $uri        the URI queries
     * @param \Exception $previous   the previous exception, if any
     */
    public function __construct(
        $message,
        $error,
        $httpStatus,
        $uri,
        \Exception $previous = null
    ) {
        $this->error = $error;
        parent::__construct($message, $httpStatus, $uri, $previous);
    }

    public function getErrorCode()
    {
        return $this->error;
    }
}
