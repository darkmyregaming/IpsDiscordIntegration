<?php

namespace IPS\discord\Api;

class _Channel extends \IPS\Patterns\Singleton
{
    use ResponseTransformer;

    /** @var \IPS\discord\Api\Client */
    protected $httpClient;

    public function __construct()
    {
        $this->httpClient = \IPS\discord\Api\Client::i();
    }

    public function createMessage(\IPS\discord\Api\Request $request)
    {
        $channelId = $request->getQueryParameter('channel_id');

        $response = $this->httpClient->post(
            "/channels/{$channelId}/messages",
            $request->getPayload()
        );

        return $this->transformResponse($response);
    }
}
