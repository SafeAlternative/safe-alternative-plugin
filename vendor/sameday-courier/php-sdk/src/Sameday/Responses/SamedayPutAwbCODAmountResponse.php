<?php

namespace SafeAlternative\Sameday\Responses;

use SafeAlternative\Sameday\Http\SamedayRawResponse;
use SafeAlternative\Sameday\Requests\SamedayPutAwbCODAmountRequest;
use SafeAlternative\Sameday\Responses\Traits\SamedayResponseTrait;
/**
 * Response for updating an AWB's COD amount.
 *
 * @package Sameday
 */
class SamedayPutAwbCODAmountResponse implements SamedayResponseInterface
{
    use SamedayResponseTrait;
    /**
     * SamedayPutAwbCODAmountResponse constructor.
     *
     * @param SamedayPutAwbCODAmountRequest $request
     * @param SamedayRawResponse $rawResponse
     */
    public function __construct(SamedayPutAwbCODAmountRequest $request, SamedayRawResponse $rawResponse)
    {
        $this->request = $request;
        $this->rawResponse = $rawResponse;
    }
}
