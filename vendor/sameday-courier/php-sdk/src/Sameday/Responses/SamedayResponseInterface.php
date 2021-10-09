<?php

namespace SafeAlternative\Sameday\Responses;

use SafeAlternative\Sameday\Http\SamedayRawResponse;
use SafeAlternative\Sameday\Requests\SamedayRequestInterface;
/**
 * Interface that encapsulates a request+raw response pair.
 *
 * @package Sameday
 */
interface SamedayResponseInterface
{
    /**
     * @return SamedayRequestInterface
     */
    public function getRequest();
    /**
     * @return SamedayRawResponse
     */
    public function getRawResponse();
}
