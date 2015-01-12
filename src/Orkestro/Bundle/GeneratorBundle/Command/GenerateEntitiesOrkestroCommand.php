<?php

namespace Orkestro\Bundle\GeneratorBundle\Command;

use Doctrine\Bundle\DoctrineBundle\Command\GenerateEntitiesDoctrineCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class GenerateEntitiesOrkestroCommand extends GenerateEntitiesDoctrineCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('orkestro:generate:entities')
            ->setAliases(array('generate:orkestro:entities'))
            ->setDescription('Generates entity classes and method stubs from your mapping information')
            ->addArgument('name', InputArgument::REQUIRED, 'A bundle name, a namespace, or a class name')
            ->addOption('path', null, InputOption::VALUE_REQUIRED, 'The path where to generate entities when it cannot be guessed')
            ->addOption('no-backup', null, InputOption::VALUE_NONE, 'Do not backup existing entities files.')
            ->setHelp(<<<EOT
The <info>orkestro:generate:entities</info> command generates entity classes
and method stubs from your mapping information:

You have to limit generation of entities:

* To a bundle:

  <info>php app/console orkestro:generate:entities MyCustomBundle</info>

* To a single entity:

  <info>php app/console orkestro:generate:entities MyCustomBundle:User</info>
  <info>php app/console orkestro:generate:entities MyCustomBundle/Entity/User</info>

* To a namespace

  <info>php app/console orkestro:generate:entities MyCustomBundle/Entity</info>

If the entities are not stored in a bundle, and if the classes do not exist,
the command has no way to guess where they should be generated. In this case,
you must provide the <comment>--path</comment> option:

  <info>php app/console orkestro:generate:entities Blog/Entity --path=src/</info>

By default, the unmodified version of each entity is backed up and saved
(e.g. Product.php~). To prevent this task from creating the backup file,
pass the <comment>--no-backup</comment> option:

  <info>php app/console orkestro:generate:entities Blog/Entity --no-backup</info>

<error>Important:</error> Even if you specified Inheritance options in your
XML or YAML Mapping files the generator cannot generate the base and
child classes for you correctly, because it doesn't know which
class is supposed to extend which. You have to adjust the entity
code manually for inheritance to work!

EOT
            );
    }
}