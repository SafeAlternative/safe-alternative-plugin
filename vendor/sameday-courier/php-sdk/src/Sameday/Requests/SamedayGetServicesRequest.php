<?php

namespace SafeAlternative\Sameday\Requests;

use SafeAlternative\Sameday\Http\SamedayRequest;
use SafeAlternative\Sameday\Requests\Traits\SamedayRequestPaginationTrait;
/**
 * Request to get services list.
 *
 * @package Sameday
 */
class SamedayGetServicesRequest implements SamedayPaginatedRequestInterface
{
    use SamedayRequestPaginationTrait;
    /**
     * @inheritdoc
     */
    public function buildRequest()
    {
        return new SamedayRequest(\true, 'GET', '/api/client/services', $this->buildPagination());
    }
}
