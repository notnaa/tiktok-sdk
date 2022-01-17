<?php

declare(strict_types=1);

namespace TikTok\Contract;

use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use TikTok\Exception\NotAllowHttpMethod;

interface ClientInterface
{
    public const BASE_URI = 'https://open-api.tiktok.com';
    public const API_OAUTH_PATH = 'platform/oauth/connect';

    public const GET_HTTP = 'GET';
    public const POST_HTTP = 'POST';
    public const ALLOW_HTTP_METHODS = [
        self::GET_HTTP,
        self::POST_HTTP,
    ];

    public const DEFAULT_SCOPE = [
        'user.info.basic',
    ];

    /**
     * @param string $path
     * @param string $method
     * @param array  $parameters
     *
     * @return ResponseInterface
     *
     * @throws NotAllowHttpMethod
     * @throws GuzzleException
     */
    public function sendRequest(string $path, string $method, array $parameters): ResponseInterface;

    /**
     * @param array  $scope
     * @param string $state
     *
     * @return string
     */
    public function getOAuthUrl(array $scope = self::DEFAULT_SCOPE, string $state = ''): string;
}
