<?php

namespace App\Service;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MixRepository
{
    public function __construct(
        private readonly CacheInterface $cache,
        private readonly HttpClientInterface $githubContentClient,
        private readonly bool $isDebug
    ) {
    }
    /**
     * @throws \Exception
     */
    public function findAll()
    {
        try {
            $mixes = $this->cache->get('mixes_data', function (CacheItemInterface $cacheItem) {
                $cacheItem->expiresAfter($this->isDebug ? 5 : 60);
                $response = $this->githubContentClient->request(
                    'GET',
                    '/SymfonyCasts/vinyl-mixes/main/mixes.json',
                );
                return $response->toArray();
            });
        } catch (
            ClientExceptionInterface |
            RedirectionExceptionInterface |
            ServerExceptionInterface |
            TransportExceptionInterface |
            InvalidArgumentException $e
        ) {
            return throw new \Exception("Error: " . $e->getMessage());
        }
        return $mixes;
    }
}
