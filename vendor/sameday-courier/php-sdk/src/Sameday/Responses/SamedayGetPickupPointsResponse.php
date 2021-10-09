<?php

namespace SafeAlternative\Sameday\Responses;

use SafeAlternative\Sameday\Http\SamedayRawResponse;
use SafeAlternative\Sameday\Objects\CountyObject;
use SafeAlternative\Sameday\Objects\PickupPoint\CityObject;
use SafeAlternative\Sameday\Objects\PickupPoint\ContactPersonObject;
use SafeAlternative\Sameday\Objects\PickupPoint\PickupPointObject;
use SafeAlternative\Sameday\Requests\SamedayGetPickupPointsRequest;
use SafeAlternative\Sameday\Responses\Traits\SamedayResponsePaginationTrait;
use SafeAlternative\Sameday\Responses\Traits\SamedayResponseTrait;
/**
 * Response for get pickup points request.
 *
 * @package Sameday
 */
class SamedayGetPickupPointsResponse implements SamedayPaginatedResponseInterface
{
    use SamedayResponsePaginationTrait;
    use SamedayResponseTrait;
    /**
     * @var PickupPointObject[]
     */
    protected $pickupPoints = [];
    /**
     * SamedayGetPickupPointsResponse constructor.
     *
     * @param SamedayGetPickupPointsRequest $request
     * @param SamedayRawResponse $rawResponse
     */
    public function __construct(SamedayGetPickupPointsRequest $request, SamedayRawResponse $rawResponse)
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
            $this->pickupPoints[] = new PickupPointObject($data['id'], new CountyObject($data['county']['id'], $data['county']['name'], $data['county']['code']), new CityObject($data['city']['id'], $data['city']['name'], $data['city']['samedayDeliveryAgency'], $data['city']['samedayPickupAgency'], $data['city']['extraKM']), $data['address'], $data['defaultPickupPoint'], \array_map(function ($entry) {
                return new ContactPersonObject($entry['id'], $entry['name'], $entry['phoneNumber'], $entry['defaultContactPerson']);
            }, $data['pickupPointContactPerson']), $data['alias']);
        }
    }
    /**
     * @return PickupPointObject[]
     */
    public function getPickupPoints()
    {
        return $this->pickupPoints;
    }
}
