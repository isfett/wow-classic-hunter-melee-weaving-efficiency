<?php
declare(strict_types = 1);

namespace Isfett\WowClassicHunterMeleeWeavingEfficiency\Service;

use http\Client;
use Symfony\Component\HttpClient\HttpClient;

/**
 * Class WebClientService
 */
class WebClientService
{
    private $baseUrl = 'https://classic.warcraftlogs.com/reports/';

    /** @var HttpClient */
    private $webClient;

    /**
     * WebClientService constructor.
     *
     * @param HttpClient|null $webClient
     */
    public function __construct(?HttpClient $webClient = null)
    {
        $this->webClient = $webClient;
        if (null === $this->webClient) {
            $this->webClient = HttpClient::createForBaseUri($this->baseUrl);
        }
    }

    /**
     * @param string $url
     *
     * @return string
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function get(string $url): string
    {
        return $this->webClient->request('GET', $url)->getContent();
    }
}
