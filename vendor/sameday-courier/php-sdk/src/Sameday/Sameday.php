<?php

namespace SafeAlternative\Sameday;

use Exception;
use SafeAlternative\Sameday\Objects\AwbStatusHistory\ParcelObject;
use SafeAlternative\Sameday\Requests\SamedayDeleteAwbRequest;
use SafeAlternative\Sameday\Requests\SamedayGetAwbPdfRequest;
use SafeAlternative\Sameday\Requests\SamedayGetAwbStatusHistoryRequest;
use SafeAlternative\Sameday\Requests\SamedayGetCitiesRequest;
use SafeAlternative\Sameday\Requests\SamedayGetCountiesRequest;
use SafeAlternative\Sameday\Requests\SamedayGetLockersRequest;
use SafeAlternative\Sameday\Requests\SamedayGetParcelStatusHistoryRequest;
use SafeAlternative\Sameday\Requests\SamedayGetPickupPointsRequest;
use SafeAlternative\Sameday\Requests\SamedayGetStatusSyncRequest;
use SafeAlternative\Sameday\Requests\SamedayPostAwbRequest;
use SafeAlternative\Sameday\Requests\SamedayPostAwbEstimationRequest;
use SafeAlternative\Sameday\Requests\SamedayPostParcelRequest;
use SafeAlternative\Sameday\Requests\SamedayPutAwbCODAmountRequest;
use SafeAlternative\Sameday\Requests\SamedayPutParcelSizeRequest;
use SafeAlternative\Sameday\Requests\SamedayGetServicesRequest;
use SafeAlternative\Sameday\Responses\SamedayDeleteAwbResponse;
use SafeAlternative\Sameday\Responses\SamedayGetAwbPdfResponse;
use SafeAlternative\Sameday\Responses\SamedayGetAwbStatusHistoryResponse;
use SafeAlternative\Sameday\Responses\SamedayGetCitiesResponse;
use SafeAlternative\Sameday\Responses\SamedayGetCountiesResponse;
use SafeAlternative\Sameday\Responses\SamedayGetLockersResponse;
use SafeAlternative\Sameday\Responses\SamedayGetParcelStatusHistoryResponse;
use SafeAlternative\Sameday\Responses\SamedayGetPickupPointsResponse;
use SafeAlternative\Sameday\Responses\SamedayGetStatusSyncResponse;
use SafeAlternative\Sameday\Responses\SamedayPostAwbEstimationResponse;
use SafeAlternative\Sameday\Responses\SamedayPostAwbResponse;
use SafeAlternative\Sameday\Responses\SamedayPostParcelResponse;
use SafeAlternative\Sameday\Responses\SamedayPutAwbCODAmountResponse;
use SafeAlternative\Sameday\Responses\SamedayPutParcelSizeResponse;
use SafeAlternative\Sameday\Responses\SamedayGetServicesResponse;
/**
 * Class that encapsulates endpoints available in sameday api.
 *
 * @package Sameday
 */
class Sameday
{
    /**
     * @var SamedayClientInterface
     */
    protected $client;
    /**
     * Sameday constructor.
     *
     * @param SamedayClientInterface $client
     */
    public function __construct(SamedayClientInterface $client)
    {
        $this->client = $client;
    }
    /**
     * @return SamedayClientInterface
     */
    public function getClient()
    {
        return $this->client;
    }
    /**
     * @param SamedayGetServicesRequest $request
     *
     * @return SamedayGetServicesResponse
     *
     * @throws Exceptions\SamedaySDKException
     * @throws Exceptions\SamedayAuthenticationException
     * @throws Exceptions\SamedayAuthorizationException
     * @throws Exceptions\SamedayServerException
     */
    public function getServices(SamedayGetServicesRequest $request)
    {
        return new SamedayGetServicesResponse($request, $this->client->sendRequest($request->buildRequest()));
    }
    /**
     * @param SamedayGetPickupPointsRequest $request
     *
     * @return SamedayGetPickupPointsResponse
     *
     * @throws Exceptions\SamedaySDKException
     * @throws Exceptions\SamedayAuthenticationException
     * @throws Exceptions\SamedayAuthorizationException
     * @throws Exceptions\SamedayServerException
     */
    public function getPickupPoints(SamedayGetPickupPointsRequest $request)
    {
        return new SamedayGetPickupPointsResponse($request, $this->client->sendRequest($request->buildRequest()));
    }
    /**
     * @param SamedayPutParcelSizeRequest $request
     *
     * @return SamedayPutParcelSizeResponse
     *
     * @throws Exceptions\SamedaySDKException
     * @throws Exceptions\SamedayAuthenticationException
     * @throws Exceptions\SamedayAuthorizationException
     * @throws Exceptions\SamedayBadRequestException
     * @throws Exceptions\SamedayServerException
     */
    public function putParcelSize(SamedayPutParcelSizeRequest $request)
    {
        return new SamedayPutParcelSizeResponse($request, $this->client->sendRequest($request->buildRequest()));
    }
    /**
     * @param SamedayGetParcelStatusHistoryRequest $request
     *
     * @return SamedayGetParcelStatusHistoryResponse
     *
     * @throws Exceptions\SamedayAuthenticationException
     * @throws Exceptions\SamedayAuthorizationException
     * @throws Exceptions\SamedayNotFoundException
     * @throws Exceptions\SamedayOtherException
     * @throws Exceptions\SamedaySDKException
     * @throws Exceptions\SamedayServerException
     * @throws Exception
     */
    public function getParcelStatusHistory(SamedayGetParcelStatusHistoryRequest $request)
    {
        return new SamedayGetParcelStatusHistoryResponse($request, $this->client->sendRequest($request->buildRequest()));
    }
    /**
     * @param SamedayDeleteAwbRequest $request
     *
     * @return SamedayDeleteAwbResponse
     *
     * @throws Exceptions\SamedayAuthenticationException
     * @throws Exceptions\SamedayAuthorizationException
     * @throws Exceptions\SamedayNotFoundException
     * @throws Exceptions\SamedayOtherException
     * @throws Exceptions\SamedaySDKException
     * @throws Exceptions\SamedayServerException
     */
    public function deleteAwb(SamedayDeleteAwbRequest $request)
    {
        return new SamedayDeleteAwbResponse($request, $this->client->sendRequest($request->buildRequest()));
    }
    /**
     * @param SamedayPostAwbRequest $request
     *
     * @return SamedayPostAwbResponse
     *
     * @throws Exceptions\SamedayAuthenticationException
     * @throws Exceptions\SamedayAuthorizationException
     * @throws Exceptions\SamedayBadRequestException
     * @throws Exceptions\SamedayNotFoundException
     * @throws Exceptions\SamedayOtherException
     * @throws Exceptions\SamedaySDKException
     * @throws Exceptions\SamedayServerException
     */
    public function postAwb(SamedayPostAwbRequest $request)
    {
        return new SamedayPostAwbResponse($request, $this->client->sendRequest($request->buildRequest()));
    }
    /**
     * @param SamedayPostAwbEstimationRequest $request
     *
     * @return SamedayPostAwbEstimationResponse
     *
     * @throws Exceptions\SamedayAuthenticationException
     * @throws Exceptions\SamedayAuthorizationException
     * @throws Exceptions\SamedayBadRequestException
     * @throws Exceptions\SamedayNotFoundException
     * @throws Exceptions\SamedayOtherException
     * @throws Exceptions\SamedaySDKException
     * @throws Exceptions\SamedayServerException
     */
    public function postAwbEstimation(SamedayPostAwbEstimationRequest $request)
    {
        return new SamedayPostAwbEstimationResponse($request, $this->client->sendRequest($request->buildRequest()));
    }
    /**
     * @param SamedayGetCountiesRequest $request
     *
     * @return SamedayGetCountiesResponse
     *
     * @throws Exceptions\SamedayAuthenticationException
     * @throws Exceptions\SamedayAuthorizationException
     * @throws Exceptions\SamedayBadRequestException
     * @throws Exceptions\SamedaySDKException
     * @throws Exceptions\SamedayServerException
     */
    public function getCounties(SamedayGetCountiesRequest $request)
    {
        return new SamedayGetCountiesResponse($request, $this->client->sendRequest($request->buildRequest()));
    }
    /**
     * @param SamedayGetCitiesRequest $request
     *
     * @return SamedayGetCitiesResponse
     *
     * @throws Exceptions\SamedayAuthenticationException
     * @throws Exceptions\SamedayAuthorizationException
     * @throws Exceptions\SamedayBadRequestException
     * @throws Exceptions\SamedaySDKException
     * @throws Exceptions\SamedayServerException
     */
    public function getCities(SamedayGetCitiesRequest $request)
    {
        return new SamedayGetCitiesResponse($request, $this->client->sendRequest($request->buildRequest()));
    }
    /**
     * @param SamedayGetStatusSyncRequest $request
     *
     * @return SamedayGetStatusSyncResponse
     *
     * @throws Exceptions\SamedayAuthenticationException
     * @throws Exceptions\SamedayAuthorizationException
     * @throws Exceptions\SamedayBadRequestException
     * @throws Exceptions\SamedaySDKException
     * @throws Exceptions\SamedayServerException
     * @throws Exception
     */
    public function getStatusSync(SamedayGetStatusSyncRequest $request)
    {
        return new SamedayGetStatusSyncResponse($request, $this->client->sendRequest($request->buildRequest()));
    }
    /**
     * @param SamedayPostParcelRequest $request
     *
     * @return SamedayPostParcelResponse
     *
     * @throws Exceptions\SamedayAuthenticationException
     * @throws Exceptions\SamedayAuthorizationException
     * @throws Exceptions\SamedayBadRequestException
     * @throws Exceptions\SamedayNotFoundException
     * @throws Exceptions\SamedayOtherException
     * @throws Exceptions\SamedaySDKException
     * @throws Exceptions\SamedayServerException
     * @throws Exception
     */
    public function postParcel(SamedayPostParcelRequest $request)
    {
        $parcelsRequest = new SamedayGetAwbStatusHistoryRequest($request->getAwbNumber());
        // Get old parcels.
        $parcelsResponse = $this->getAwbStatusHistory($parcelsRequest);
        $oldParcels = \array_map(function (ParcelObject $parcel) {
            return $parcel->getParcelAwbNumber();
        }, $parcelsResponse->getParcels());
        // Create new parcel.
        $response = $this->client->sendRequest($request->buildRequest());
        // Get new parcels.
        $parcelsResponse = $this->getAwbStatusHistory($parcelsRequest);
        $newParcels = \array_map(function (ParcelObject $parcel) {
            return $parcel->getParcelAwbNumber();
        }, $parcelsResponse->getParcels());
        $newParcel = \array_values(\array_diff($newParcels, $oldParcels));
        return new SamedayPostParcelResponse($request, $response, $newParcel[0]);
    }
    /**
     * @param SamedayGetAwbPdfRequest $request
     *
     * @return SamedayGetAwbPdfResponse
     *
     * @throws Exceptions\SamedayAuthenticationException
     * @throws Exceptions\SamedayAuthorizationException
     * @throws Exceptions\SamedayBadRequestException
     * @throws Exceptions\SamedayNotFoundException
     * @throws Exceptions\SamedaySDKException
     * @throws Exceptions\SamedayServerException
     */
    public function getAwbPdf(SamedayGetAwbPdfRequest $request)
    {
        return new SamedayGetAwbPdfResponse($request, $this->client->sendRequest($request->buildRequest()));
    }
    /**
     * @param SamedayGetAwbStatusHistoryRequest $request
     *
     * @return SamedayGetAwbStatusHistoryResponse
     *
     * @throws Exceptions\SamedayAuthenticationException
     * @throws Exceptions\SamedayAuthorizationException
     * @throws Exceptions\SamedayBadRequestException
     * @throws Exceptions\SamedayNotFoundException
     * @throws Exceptions\SamedayOtherException
     * @throws Exceptions\SamedaySDKException
     * @throws Exceptions\SamedayServerException
     * @throws Exception
     */
    public function getAwbStatusHistory(SamedayGetAwbStatusHistoryRequest $request)
    {
        return new SamedayGetAwbStatusHistoryResponse($request, $this->client->sendRequest($request->buildRequest()));
    }
    /**
     * @param SamedayGetLockersRequest $request
     *
     * @return SamedayGetLockersResponse
     *
     * @throws Exceptions\SamedaySDKException
     * @throws Exceptions\SamedayAuthenticationException
     * @throws Exceptions\SamedayAuthorizationException
     * @throws Exceptions\SamedayServerException
     * @throws Exceptions\SamedayBadRequestException
     */
    public function getLockers(SamedayGetLockersRequest $request)
    {
        return new SamedayGetLockersResponse($request, $this->client->sendRequest($request->buildRequest()));
    }
    /**
     * @param SamedayPutAwbCODAmountRequest $request
     *
     * @return SamedayPutAwbCODAmountResponse
     *
     * @throws Exceptions\SamedaySDKException
     * @throws Exceptions\SamedayAuthenticationException
     * @throws Exceptions\SamedayAuthorizationException
     * @throws Exceptions\SamedayServerException
     * @throws Exceptions\SamedayBadRequestException
     */
    public function putAwbCODAmount(SamedayPutAwbCODAmountRequest $request)
    {
        return new SamedayPutAwbCODAmountResponse($request, $this->client->sendRequest($request->buildRequest()));
    }
}
