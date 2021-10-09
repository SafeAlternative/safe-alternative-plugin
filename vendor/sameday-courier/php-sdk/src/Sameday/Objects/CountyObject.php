<?php

namespace SafeAlternative\Sameday\Objects;

use SafeAlternative\Sameday\Objects\Traits\SamedayObjectCodeTrait;
use SafeAlternative\Sameday\Objects\Traits\SamedayObjectIdTrait;
use SafeAlternative\Sameday\Objects\Traits\SamedayObjectNameTrait;
/**
 * County.
 *
 * @package Sameday
 */
class CountyObject
{
    use SamedayObjectIdTrait;
    use SamedayObjectNameTrait;
    use SamedayObjectCodeTrait;
    /**
     * CountyObject constructor.
     *
     * @param int $id
     * @param string $name
     * @param string $code
     */
    public function __construct($id, $name, $code)
    {
        $this->id = $id;
        $this->name = $name;
        $this->code = $code;
    }
}
