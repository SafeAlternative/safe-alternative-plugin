<?php

namespace SafeAlternative\Sameday\Responses;

use SafeAlternative\Sameday\Http\SamedayRawResponse;
use SafeAlternative\Sameday\Requests\SamedayGetAwbPdfRequest;
use SafeAlternative\Sameday\Responses\Traits\SamedayResponseTrait;
/**
 * Response for downloading an PDF for an existing AWB request.
 *
 * @package Sameday
 */
class SamedayGetAwbPdfResponse implements SamedayResponseInterface
{
    use SamedayResponseTrait;
    /**
     * SamedayGetAwbPdfResponse constructor.
     *
     * @param SamedayGetAwbPdfRequest $request
     * @param SamedayRawResponse $rawResponse
     */
    public function __construct(SamedayGetAwbPdfRequest $request, SamedayRawResponse $rawResponse)
    {
        $this->request = $request;
        $this->rawResponse = $rawResponse;
    }
    /**
     * @return string
     */
    public function getPdf()
    {
        return $this->rawResponse->getBody();
    }
}
