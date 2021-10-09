<?php

namespace SafeAlternative\Sameday\Responses;

use SafeAlternative\Sameday\Http\SamedayRawResponse;
use SafeAlternative\Sameday\Requests\SamedayPutParcelSizeRequest;
use SafeAlternative\Sameday\Responses\Traits\SamedayResponseTrait;
/**
 * Response for updating a parcel size request.
 *
 * @package Sameday
 */
class SamedayPutParcelSizeResponse implements SamedayResponseInterface
{
    use SamedayResponseTrait;
    /**
     * SamedayPutParcelSizeResponse constructor.
     *
     * @param SamedayPutParcelSizeRequest $request
     * @param SamedayRawResponse $rawResponse
     */
    public function __construct(SamedayPutParcelSizeRequest $request, SamedayRawResponse $rawResponse)
    {
        $this->request = $request;
        $this->rawResponse = $rawResponse;
    }
}
