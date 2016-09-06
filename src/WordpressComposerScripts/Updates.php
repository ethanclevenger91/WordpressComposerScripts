<?php

namespace WordpressComposerScripts;

use Composer\Script\Event;
use Composer\Installer\PackageEvent;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;

class Updates
{
    private $plugins = '';
    private $oldPlugins = '';
    private $updateStatuses = [];

    private $headers = ['Plugin Name', 'Old Version', 'New Version'];

    public function __construct()
    {
        $this->loadPlugins();
        return $this;
    }

    public static function preUpdateCommand(Event $event)
    {
        $composer = $event->getComposer();
        $updates = new self();
        $updates->writePluginsToFile();
    }

    public static function postUpdateCommand(Event $event)
    {
        $composer = $event->getComposer();
        $updates = self::withOldFile();
        $updates->updatePluginDiff();
        $updates->printTable();
        $updates->deleteFile();
    }

    public function loadPlugins()
    {
        $pluginData = json_decode(`wp plugin list --format=json`);
        $this->plugins = $pluginData;
    }

    public function writePluginsToFile($path = 'plugins.tmp')
    {
        file_put_contents($path, json_encode($this->plugins));
    }

    public function deleteFile($path = 'plugins.tmp')
    {
        unlink($path);
    }

    public static function withOldFile($path = 'plugins.tmp')
    {
        $updates = new Updates();
        $oldPluginData = json_decode(file_get_contents($path));
        $updates->oldPlugins = $oldPluginData;
        return $updates;
    }

    public function printTable()
    {
        $output = new ConsoleOutput();
        $table = new Table($output);
        $table->setHeaders($this->headers);
        $table->setRows($this->updateStatuses);
        $table->render();
    }

    public function updatePluginDiff()
    {
        $updateInfo = [];
        $installedPlugins = [];
        foreach ($this->plugins as $newPlugin) {
            $installedPlugins[$newPlugin->name] = $newPlugin->version;
            $found = false;
            foreach ($this->oldPlugins as $oldPlugin) {
                if ($oldPlugin->name == $newPlugin->name) {
                    $found = true;
                    if ($oldPlugin->version == $newPlugin->version) {
                        break;
                    }
                    $updateInfo[] = [
                      $oldPlugin->name,
                      $oldPlugin->version,
                      $newPlugin->version
                    ];
                    break;
                }
            }
            if (!$found) {
                $updateInfo[] = [
              $newPlugin->name,
              'installed',
              $newPlugin->version
            ];
            }
        }
        foreach ($this->oldPlugins as $oldPlugin) {
            if (!array_key_exists($oldPlugin->name, $installedPlugins)) {
                $updateInfo[] = [
            $oldPlugin->name,
            'removed',
            'N/A'
          ];
            }
        }
        $this->updateStatuses = $updateInfo;
    }
}
