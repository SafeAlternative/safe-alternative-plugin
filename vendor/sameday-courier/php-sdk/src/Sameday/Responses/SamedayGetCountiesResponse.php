<?php

namespace SafeAlternative\Sameday\Responses;

use SafeAlternative\Sameday\Http\SamedayRawResponse;
use SafeAlternative\Sameday\Objects\CountyObject;
use SafeAlternative\Sameday\Requests\SamedayGetCountiesRequest;
use SafeAlternative\Sameday\Responses\Traits\SamedayResponsePaginationTrait;
use SafeAlternative\Sameday\Responses\Traits\SamedayResponseTrait;
/**
 * Response for get counties request.
 *
 * @package Sameday
 */
class SamedayGetCountiesResponse implements SamedayPaginatedResponseInterface
{
    use SamedayResponsePaginationTrait;
    use SamedayResponseTrait;
    /**
     * @var CountyObject[]
     */
    protected $counties = [];
    /**
     * SamedayGetCountiesResponse constructor.
     *
     * @param SamedayGetCountiesRequest $request
     * @param SamedayRawResponse $rawResponse
     */
    public function __construct(SamedayGetCountiesRequest $request, SamedayRawResponse $rawResponse)
    {
        $this->request = $request;
        $this->rawResponse = $rawResponse;
        $json = \json_decode($this->rawResponse->getBody(), \true);
        $this->parsePagination($this->request, $json);
        if (!$json) {
            // Empty response.
            return;
        }
        foreach ($json['data'] as $data) {
            $this->counties[] = new CountyObject($data['id'], $data['name'], $data['code']);
        }
    }
    /**
     * @return CountyObject[]
     */
    public function getCounties()
    {
        return $this->counties;
    }
}
