<?php

namespace SafeAlternative\Sameday\Responses;

use SafeAlternative\Sameday\Http\SamedayRawResponse;
use SafeAlternative\Sameday\Requests\SamedayDeleteAwbRequest;
use SafeAlternative\Sameday\Responses\Traits\SamedayResponseTrait;
/**
 * Response for delete AWB request.
 *
 * @package Sameday
 */
class SamedayDeleteAwbResponse implements SamedayResponseInterface
{
    use SamedayResponseTrait;
    /**
     * SamedayDeleteAwbResponse constructor.
     *
     * @param SamedayDeleteAwbRequest $request
     * @param SamedayRawResponse $rawResponse
     */
    public function __construct(SamedayDeleteAwbRequest $request, SamedayRawResponse $rawResponse)
    {
        $this->request = $request;
        $this->rawResponse = $rawResponse;
    }
}
