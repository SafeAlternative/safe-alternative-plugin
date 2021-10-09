<?php

namespace SafeAlternative\Sameday\Requests;

use SafeAlternative\Sameday\Http\SamedayRequest;
use SafeAlternative\Sameday\Requests\Traits\SamedayRequestPaginationTrait;
/**
 * Request to get counties list.
 *
 * @package Sameday
 */
class SamedayGetCountiesRequest implements SamedayPaginatedRequestInterface
{
    use SamedayRequestPaginationTrait;
    /**
     * @var string|null
     */
    protected $name;
    /**
     * SamedayGetCountiesRequest constructor.
     *
     * @param string|null $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }
    /**
     * @inheritdoc
     */
    public function buildRequest()
    {
        return new SamedayRequest(\true, 'GET', '/api/geolocation/county', \array_merge(['name' => $this->name], $this->buildPagination()));
    }
    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * @param string|null $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
}
