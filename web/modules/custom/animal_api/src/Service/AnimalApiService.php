<?php

declare(strict_types=1);

namespace Drupal\animal_api\Service;


use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use GuzzleHttp\ClientInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;



final class AnimalApiService implements ContainerInjectionInterface {

  /**
   * Constructs a new AnimalApiService object.
   */
  public function __construct(    private readonly ClientInterface $client,
    private readonly LoggerChannelFactoryInterface $loggerChannelFactory,) {
  }
  
  public static function create(\Symfony\Component\DependencyInjection\ContainerInterface $container): self {
    return new self(
      $container->get('http_client'),
      $container->get('logger.factory'),
    );
  }


  public function getAnimalData(string $animalName): array {
    try {
      $response = $this->client->request('GET', 'https://api.api-ninjas.com/v1/animals', [
        'query' => ['name' => $animalName],
        'headers' => [
          'X-Api-Key' => $_ENV['ANIMAL_API_KEY'] ?? '',
        ],
      ]);

      if ($response->getStatusCode() !== 200) {
        throw new \RuntimeException("HTTP {$response->getStatusCode()}: {$response->getBody()}");
      }

      return json_decode($response->getBody()->getContents(), true);
    } catch (\Exception $e) {
      $this->loggerChannelFactory->get('animal_api')->error('Error retrieving animal data: @message', ['@message' => $e->getMessage()]);
      return [];
    }
  }

}