<?php

namespace Drupal\animal_api\Drush\Commands;

use Consolidation\OutputFormatters\FormatterManager;
use Consolidation\OutputFormatters\StructuredData\RowsOfFields;
use Drupal\Core\Utility\Token;
use Drush\Attributes as CLI;
use Drush\Commands\AutowireTrait;
use Drush\Formatters\FormatterTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(
  name: self::NAME,
  description: 'Retrieve animal from API',
  aliases: ['retrieve'],
)]
#[CLI\Formatter(returnType: RowsOfFields::class, defaultFormatter: 'table')]
#[CLI\FieldLabels(labels: [
  'name' => 'Name',
  'scientific_name' => 'Scientific name',
  'class' => 'Class',
  'lifespan' => 'Lifespan',
  'size' => 'Size',
  'weight' => 'Weight',
  'slogan' => 'Slogan',
])]
#[CLI\DefaultTableFields(fields: [
  'name',
  'scientific_name',
  'class',
  'lifespan',
  'size',
  'weight',
  'slogan',
])]
#[CLI\FilterDefaultField(field: 'scientific_name')]
final class RetrieveCommand extends Command {

  use AutowireTrait;
  use FormatterTrait;

  const NAME = 'animal_api:retrieve';

  /**
   * Constructs a RetrieveCommand object.
   */
  public function __construct(
    private readonly LoggerInterface $logger,
    #[Autowire(service: 'animal_api.service')]
    private readonly \Drupal\animal_api\Service\AnimalApiService $animalApiService,
  ) {
    parent::__construct();


  }

  protected function configure(): void {
    $this
      ->addArgument(name: 'group', mode: InputOption::VALUE_REQUIRED, description: 'Show tokens from this group only.')
      ->addOption(name: 'example', mode: InputOption::VALUE_OPTIONAL, description: 'Example description')
      ->addUsage(usage: 'token --group=aggregator');
  }

  public function execute(InputInterface $input, OutputInterface $output): int {
    $result = $this->animalApiService->getAnimalData('dog');
    foreach ($result as $animal) {
      $characteristics = $animal['characteristics'] ?? [];
      $taxonomy = $animal['taxonomy'] ?? [];

      $filteredResult = [
        'name' => $animal['name'] ?? null,
        'scientific_name' => $taxonomy['scientific_name'] ?? null,
        'class' => $taxonomy['class'] ?? null,
        'lifespan' => $characteristics['lifespan'] ?? $characteristics['average_lifespan'] ?? null,
        'size' => $characteristics['length'] ?? $characteristics['height'] ?? null,
        'weight' => $characteristics['weight'] ?? null,
        'slogan' => $characteristics['slogan'] ?? null,
      ];

      $this->logger->warning(json_encode($filteredResult));
    }

    return Command::SUCCESS;
  
  }


}
