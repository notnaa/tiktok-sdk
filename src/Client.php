<?php

declare(strict_types=1);

namespace TikTok;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface as GuzzleClientInterface;
use Psr\Http\Message\ResponseInterface;
use TikTok\Contract\ClientInterface;
use TikTok\Exception\NotAllowHttpMethod;

class Client implements ClientInterface
{
    /**
     * @var string
     */
    private string $clientKey;

    /**
     * @var string
     */
    private string $redirectUri;

    /**
     * @var GuzzleClientInterface
     */
    private GuzzleClientInterface $httpClient;

    /**
     * @param string        $clientKey
     * @param string        $redirectUri
     * @param string|null   $proxy
     */
    public function __construct(string $clientKey, string $redirectUri, ?string $proxy = null)
    {
        $this->clientKey = $clientKey;
        $this->redirectUri = $redirectUri;
        $this->httpClient = $this->configureHttpClient($proxy);
    }

    /**
     * @inheritDoc
     */
    public function sendRequest(string $path, string $method, array $parameters): ResponseInterface
    {
        if (!in_array($method, self::ALLOW_HTTP_METHODS)) {
            throw new NotAllowHttpMethod();
        }

        if (self::GET_HTTP) {
            $field = 'query';
        } else {
            $field = 'json';
        }

        $requestParameters = [];
        foreach ($parameters as $name => $value) {
            $placeholder = sprintf('{%s}', $name);
            if (strpos($path, $placeholder) !== false) {
                $path = str_replace($placeholder, $value, $path);
            } else {
                $requestParameters[$name] = $value;
            }
        }

        return $this->httpClient->request($method, $path, [$field => $requestParameters]);
    }

    /**
     * @inheritDoc
     */
    public function getOAuthUrl(array $scope = self::DEFAULT_SCOPE, string $state = ''): string
    {
        $params = [
            'client_key'        => $this->clientKey,
            'redirect_uri'      => urlencode($this->redirectUri),
            'scope'             => implode(',', $scope),
            'response_type'     => 'code',
            'state'             => $state,
        ];
        $query = http_build_query($params);

        return sprintf('%s/%s?%s', self::BASE_URI, self::API_OAUTH_PATH, $query);
    }

    /**
     * @param string|null $proxy
     *
     * @return GuzzleClientInterface
     */
    private function configureHttpClient(?string $proxy): GuzzleClientInterface
    {
        $clientConfig = ['base_uri' => self::BASE_URI];
        if (null !== $proxy) {
            $clientConfig['proxy'] = $proxy;
            $clientConfig['allow_redirects'] = true;
        }

        return new GuzzleClient($clientConfig);
    }
}
