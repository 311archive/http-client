<?php

namespace Balsama;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ServerException;
use Psr\Http\Message\ResponseInterface;

class Fetch
{

    private string $protocol = 'https://';
    private string $domain = '311.report/jsonapi/';
    private string $resource = 'node/report';

    private ClientInterface $client;

    private string $url;
    private int $requestLimit;
    private int $requestsPerformed = 0;

    private array $fetchedData = [];

    public function __construct($params, $requestLimit = 0)
    {
        $this->url = $this->constructUrl($params);
        $this->requestLimit = $requestLimit;
        $this->client = new Client();
    }

    public function fetchAll()
    {
        $response = $this->fetchSinglePage();
        $this->requestsPerformed++;
        if ($this->requestLimit) {
            if ($this->requestsPerformed >= $this->requestLimit) {
                return $this->fetchedData;
            }
        }
        if (isset($response['links']['next'])) {
            $this->url = $response['links']['next']['href'];
            $this->fetchAll();
        }

        return $this->fetchedData;
    }

    private function fetchSinglePage($retryOnError = 5)
    {
        try {
            /* @var $response ResponseInterface $response */
            $response = $this->client->get($this->url);
            $body = json_decode($response->getBody(), true);
            $this->fetchedData = array_merge($body['data'], $this->fetchedData);
            return $body;
        } catch (ServerException $e) {
            if ($retryOnError) {
                $retryOnError--;
                usleep(250000);
                return $this->fetchSinglePage($retryOnError);
            }
            echo 'Caught response: ' . $e->getResponse()->getStatusCode();
        }
    }

    private function constructUrl($params)
    {
        return implode('?', [$this->protocol . $this->domain . $this->resource, http_build_query($params)]);
    }
}
