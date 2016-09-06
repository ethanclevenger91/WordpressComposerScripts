<?php

namespace WordpressComposerScripts;

use Composer\Script\Event;
use Composer\Installer\PackageEvent;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\StreamOutput;

class Updates
{
    public static function preUpdateCommand(Event $event)
    {
        $composer = $event->getComposer();
        $pluginData = json_decode(`wp plugin list --format=json`);
        file_put_contents('plugins.tmp', json_encode($pluginData));
    }

    public static function postUpdateCommand(Event $event)
    {
        $composer = $event->getComposer();
        $pluginData = json_decode(`wp plugin list --format=json`);
        $oldPluginData = json_decode(file_get_contents('plugins.tmp'));
        $tableData = self::getPluginDiff($oldPluginData, $pluginData);
    }

    public static function printTable($data)
    {
        $output = new StreamOutput(fopen('php://stdout', 'w'));
        $table = new Table($output);
        $table->setHeaders(['Plugin', 'Old Version', 'New Version']);
        $table->setRows($data);
        $table->render();
    }

    public static function getPluginDiff($oldPluginData, $newPluginData)
    {
        $updateInfo = [];
        $installedPlugins = [];
        foreach ($newPluginData as $newPlugin) {
            $installedPlugins[$newPlugin->name] = $newPlugin->version;
            foreach ($oldPluginData as $oldPlugin) {
                $found = false;
                if ($oldPlugin->name == $newPlugin->name) {
                    if ($oldPlugin->version == $newPlugin->version) {
                        break;
                    }
                    $updateInfo[] = [
              $oldPlugin->name,
              $oldPlugin->version,
              $newPlugin->version
            ];
                }
                if (!$found) {
                    $updateInfo[] = [
              $newPlugin->name,
              'installed',
              $newPlugin->version
            ];
                }
            }
        }
        foreach ($oldPluginData as $oldPlugin) {
            if (!array_key_exists($oldPlugin->name, $installedPlugins)) {
                $updateInfo[] = [
            $oldPlugin->name,
            'removed',
            'N/A'
          ];
            }
        }
        return $updateInfo;
    }
}
