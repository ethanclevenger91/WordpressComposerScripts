<?php

namespace Ethanclevenger91\WordpressComposerScripts;

use Composer\Script\Event;
use Composer\Installer\PackageEvent;

class WordpressComposerScripts
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
    }
}
