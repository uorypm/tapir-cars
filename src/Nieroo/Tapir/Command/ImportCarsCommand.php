<?php

namespace Nieroo\Tapir\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use StdClass;
use Nieroo\Tapir\Car\Car;
use Nieroo\DB\DAO\CarTable;
use Nieroo\DB\DB;
use Exception;

/**
 * Class CreateDBCommand
 * @package Nieroo\Tapir\Command
 */
class ImportCarsCommand extends Command
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('cars:import')
            ->setDescription('Import data from remote source')
            ->setHelp(
                'The command allows you to import data from remote source'
            )
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        global $application;

        $db     = $application->getDB();
        $config = $application->getConfig();

        if (!isset($config->getConfigSource()['url'])) {
            $output->writeln('<error>URL not defined</error>');

            return 1;
        }

        $url = $config->getConfigSource()['url'];

        if (\filter_var($url, FILTER_VALIDATE_URL) === false) {
            $output->writeln('<error>Wrong URL</error>');

            return 2;
        }

        $contentRaw = file_get_contents($url);

        if (!$contentRaw) {
            $output->writeln('<error>Wrong Content</error>');

            return 3;
        }

        $contentJSON = json_decode(str_replace(
            [
                '<pre>',
                '</pre>',
            ],
            '',
            html_entity_decode($contentRaw)
        ));

        if (!$contentJSON || count($contentJSON) === 0) {
            $output->writeln('<error>Wrong Content JSON</error>');

            return 4;
        }

        $this->createCars($contentJSON, $output, $db);

        return 0;
    }

    /**
     * @param array $cars
     * @param OutputInterface $output
     * @param DB $db
     */
    private function createCars(
        array $cars,
        OutputInterface $output,
        DB $db
    ) {
        $output->writeln('<info>Start creating cars...</info>');

        try {
            $output->writeln('<info>Start transaction</info>');
            $db->getPDO()->beginTransaction();

            foreach ($cars as $car) {
                if (!($car instanceof StdClass)) {
                    continue;
                }

                if (!$this->createCar($car, $output, $db)) {
                    throw new \RuntimeException('Car import error');
                }
            }

            $output->writeln('<info>Commit transaction</info>');
            $db->getPDO()->commit();
        } catch (Exception $e) {
            $output->writeln("<error>{$e->getMessage()}</error>");

            $output->writeln('<info>RollBack transaction</info>');
            $db->getPDO()->rollBack();
        }

        $output->writeln('<info>Finish creating cars</info>');
    }

    /**
     * @param StdClass $stdCar
     * @param OutputInterface $output
     * @param DB $db
     *
     * @return bool
     */
    private function createCar(
        StdClass $stdCar,
        OutputInterface $output,
        DB $db
    ) {
        if (isset($stdCar->vin)) {
            $output->writeln(
                "<info>Start creating car#{$stdCar->vin}...</info>"
            );
        } else {
            $output->writeln(
                '<info>Start creating unknown car...</info>'
            );
        }

        $car = new Car(
            $stdCar,
            new CarTable($db->getPDO())
        );

        $result = $car->add();

        if (!$result
            && $car->getLastError()
        ) {
            $output->writeln(
                "<error>{$car->getLastError()}</error>"
            );
        }

        return $car->add();
    }
}
