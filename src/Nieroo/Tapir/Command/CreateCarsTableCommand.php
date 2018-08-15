<?php

namespace Nieroo\Tapir\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CreateCarsTableCommand
 * @package Nieroo\Tapir\Command
 */
class CreateCarsTableCommand extends Command
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('cars:create-table')
            ->setDescription('Create table `cars`')
            ->setHelp(
                'The command allows you to create table `cars`'
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

        $pdo = $application->getDB()->getPDO();

        $result = $pdo->query("
            CREATE TABLE IF NOT EXISTS `cars` (
              `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
              `vin` CHAR(17) NOT NULL,
              `model` VARCHAR(45) NOT NULL,
              `km` DECIMAL(10,2) UNSIGNED NOT NULL,
              `color` VARCHAR(45) NOT NULL,
              `owners` INT(7) UNSIGNED NOT NULL,
              `power` INT(5) UNSIGNED NOT NULL,
              `engine_capacity` DECIMAL(4,2) UNSIGNED NOT NULL,
              `transmission` VARCHAR(45) NOT NULL,
              `price` DECIMAL(11,2) UNSIGNED NOT NULL,
              PRIMARY KEY (`id`),
              UNIQUE INDEX `vin_UNIQUE` (`vin` ASC)
            )
            ENGINE = MyISAM
            DEFAULT CHARACTER SET = utf8;
        ");

        if ($result === false) {
            $output->writeln(
                '<error>Cannot create table `cars`</error>'
            );

            return 1;
        }

        $output->writeln(
            '<info>Table `cars` successfully created</info>'
        );

        return 0;
    }
}
