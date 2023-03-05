<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\Gandi\Address;
use App\Entity\Alias;
use App\Exception\GandiException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GandiService
{
    public function __construct(
        protected readonly HttpClientInterface $gandiNet,
        protected readonly NormalizerInterface $normalizer,
        protected readonly DenormalizerInterface $denormalizer,
        protected readonly LoggerInterface $logger,
    ) {
    }

    /** @return Address[] */
    public function getAddresses(string $domain): array
    {
        $response = $this->gandiNet->request(
            Request::METHOD_GET,
            "/v5/email/forwards/{$domain}"
        );

        if (Response::HTTP_OK !== $response->getStatusCode()) {
            $this->logger->error(
                "Unable to retrieve forwarding addresses.",
                [
                    'body' => [
                        'domain' => $domain,
                    ],
                    'headers' => $response->getHeaders(false),
                    'content' => $response->getContent(false),
                    'statusCode' => $response->getStatusCode(),
                    'response' => $response->toArray(false),
                ]
            );

            throw new GandiException(
                "Unable to retrieve forwarding addresses.",
                $response->getStatusCode(),
            );
        }

        return $this->denormalizer->denormalize(
            $response->toArray(),
            Address::class . '[]',
            'any',
        );
    }

    public function addAddress(Alias $alias): void
    {
        $json = $this->normalizer->normalize(
            $alias,
            'json',
            [
                'groups' => ['gandi'],
            ],
        );

        $response = $this->gandiNet->request(
            Request::METHOD_POST,
            "/v5/email/forwards/{$alias->getDomain()}",
            [
                'json' => $json,
            ]
        );

        if (Response::HTTP_CREATED !== $response->getStatusCode()) {
            $this->logger->error(
                "Unable to create forwarding address.",
                [
                    'body' => $json,
                    'headers' => $response->getHeaders(false),
                    'content' => $response->getContent(false),
                    'statusCode' => $response->getStatusCode(),
                    'response' => $response->toArray(false),
                ]
            );

            throw new GandiException(
                "Unable to create forwarding address.",
                $response->getStatusCode(),
            );
        }
    }

    public function updateAddress(Alias $alias): void
    {
        $json = $this->normalizer->normalize(
            $alias,
            'json',
            [
                'groups' => ['gandi'],
            ],
        );

        $response = $this->gandiNet->request(
            Request::METHOD_PUT,
            "/v5/email/forwards/{$alias->getDomain()}/{$alias->getSource()}",
            [
                'json' => $json,
            ]
        );

        if (Response::HTTP_ACCEPTED !== $response->getStatusCode()) {
            $this->logger->error(
                "Unable to update forwarding address.",
                [
                    'body' => $json,
                    'headers' => $response->getHeaders(false),
                    'content' => $response->getContent(false),
                    'statusCode' => $response->getStatusCode(),
                    'response' => $response->toArray(false),
                ]
            );

            throw new GandiException(
                "Unable to update forwarding address.",
                $response->getStatusCode(),
            );
        }
    }

    public function removeAddress(string $domain, string $source): void
    {
        $response = $this->gandiNet->request(
            Request::METHOD_DELETE,
            "/v5/email/forwards/{$domain}/{$source}",
        );

        if (Response::HTTP_ACCEPTED !== $response->getStatusCode()
            && Response::HTTP_NOT_FOUND !== $response->getStatusCode()
        ) {
            $this->logger->error(
                "Unable to remove forwarding address.",
                [
                    'body' => [
                        'domain' => $domain,
                        'source' => $source,
                    ],
                    'headers' => $response->getHeaders(false),
                    'content' => $response->getContent(false),
                    'statusCode' => $response->getStatusCode(),
                    'response' => $response->toArray(false),
                ]
            );

            throw new GandiException(
                "Unable to remove forwarding address.",
                $response->getStatusCode(),
            );
        }
    }
}
