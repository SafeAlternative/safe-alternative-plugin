<?php

final class SafealternativeSamedayShippingClass
{
    private $sameday, $sameday_client;

    public function __construct()
    {
        $this->sameday_client = new SafeAlternative\Sameday\SamedayClient(
            get_option('sameday_username', ''),
            get_option('sameday_password', '')
        );
        $this->sameday = new SafeAlternative\Sameday\Sameday($this->sameday_client);
    }

    public function calculate(array $parameters)
    {
        $tarif = $this->sameday->postAwbEstimation(new SafeAlternative\Sameday\Requests\SamedayPostAwbEstimationRequest(
            get_option('sameday_pickup_point'),
            null,
            new SafeAlternative\Sameday\Objects\Types\PackageType(get_option('sameday_package_type', 0)),
            [
                new SafeAlternative\Sameday\Objects\ParcelDimensionsObject(
                    $parameters['weight']
                ),
            ],
            get_option('sameday_service_id'),
            new SafeAlternative\Sameday\Objects\Types\AwbPaymentType(
                SafeAlternative\Sameday\Objects\Types\AwbPaymentType::CLIENT
            ),
            new SafeAlternative\Sameday\Objects\PostAwb\Request\AwbRecipientEntityObject(
                ucwords(strtolower($parameters['city'])) !== 'Bucuresti' ? $parameters['city'] : 'Sector 1',
                $parameters['state'],
                ltrim($parameters['address']) !== '' ? ltrim($parameters['address']) : '123',
                null,
                null,
                null,
                null
            ),
            $parameters['declared_value'],
            $parameters['cod_value']
        ));

        return $tarif->getCost();
    }
}
