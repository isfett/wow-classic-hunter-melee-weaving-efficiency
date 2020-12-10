<?php
declare(strict_types = 1);

namespace Isfett\WowClassicHunterMeleeWeavingEfficiency\Console;

use Symfony\Component\Console\Command\Command;

/**
 * Class AbstractCommand
 */
abstract class AbstractCommand extends Command
{
    /**
     * AbstractCommand constructor.
     *
     * @param string                            $commandName
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        string $commandName
    ) {
        parent::__construct($commandName);
    }

    /**
     * @param string $defaultVisitors
     * @param bool   $withJsonExport
     *
     * @return void
     */
    protected function configureDefaultFields(string $defaultVisitors, bool $withJsonExport = false): void
    {
    }

    /**
     * @param object $classObject
     *
     * @return string
     */
    protected function getClassnameWithoutNamespace($classObject): string
    {
        $classWithNamespaces = explode('\\', get_class($classObject));

        return end($classWithNamespaces);
    }
}
