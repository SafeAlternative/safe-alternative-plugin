<?php

namespace SafeAlternative\Sameday\Responses;

use DateTime;
use SafeAlternative\Sameday\Http\SamedayRawResponse;
use SafeAlternative\Sameday\Objects\StatusSync\StatusObject;
use SafeAlternative\Sameday\Requests\SamedayGetStatusSyncRequest;
use SafeAlternative\Sameday\Responses\Traits\SamedayResponsePaginationTrait;
use SafeAlternative\Sameday\Responses\Traits\SamedayResponseTrait;
/**
 * Response for get status sync request.
 *
 * @package Sameday
 */
class SamedayGetStatusSyncResponse implements SamedayPaginatedResponseInterface
{
    use SamedayResponsePaginationTrait;
    use SamedayResponseTrait;
    /**
     * @var StatusObject[]
     */
    protected $statuses = [];
    /**
     * SamedayGetStatusSyncResponse constructor.
     *
     * @param SamedayGetStatusSyncRequest $request
     * @param SamedayRawResponse $rawResponse
     *
     * @throws \Exception
     */
    public function __construct(SamedayGetStatusSyncRequest $request, SamedayRawResponse $rawResponse)
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
            $this->statuses[] = new StatusObject($data['statusId'], $data['status'], $data['parcelAwbNumber'], $data['statusLabel'], $data['statusState'], new DateTime($data['statusDate']), $data['reasonId'] === '' ? null : (int) $data['reasonId'], $data['reason'], $data['parcelDetails']);
        }
    }
    /**
     * @return StatusObject[]
     */
    public function getStatuses()
    {
        return $this->statuses;
    }
}
