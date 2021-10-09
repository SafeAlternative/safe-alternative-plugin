<?php

namespace SafeAlternative\Sameday\Requests;

use SafeAlternative\Sameday\Http\SamedayRequest;
use SafeAlternative\Sameday\Requests\Traits\SamedayRequestPaginationTrait;
/**
 * Request to get pickup points list.
 *
 * @package Sameday
 */
class SamedayGetPickupPointsRequest implements SamedayPaginatedRequestInterface
{
    use SamedayRequestPaginationTrait;
    /**
     * @inheritdoc
     */
    public function buildRequest()
    {
        return new SamedayRequest(\true, 'GET', '/api/client/pickup-points', $this->buildPagination());
    }
}
