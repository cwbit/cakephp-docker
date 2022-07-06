<?php
declare(strict_types=1);

namespace AssetMix;

use AssetMix\Command\AssetMixCommand;
use Cake\Console\CommandCollection;
use Cake\Core\BasePlugin;
use Cake\Core\PluginApplicationInterface;

/**
 * Plugin for AssetMix
 */
class Plugin extends BasePlugin
{
    /**
     * Add console commands for the plugin.
     *
     * @param \Cake\Console\CommandCollection<mixed> $commands The command collection to update
     * @return \Cake\Console\CommandCollection<mixed>
     */
    public function console($commands): CommandCollection
    {
        parent::console($commands);

        $commands->add('asset_mix generate', AssetMixCommand::class);

        return $commands;
    }

    /**
     * @inheritDoc
     */
    public function bootstrap(PluginApplicationInterface $app): void
    {
        parent::bootstrap($app);
    }
}
