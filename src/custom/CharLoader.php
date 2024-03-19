<?php

namespace custom;

use custom\comandos\customize_command;
use pocketmine\plugin\PluginBase;
use ref\libNpcDialogue\libNpcDialogue;

class CharLoader extends PluginBase
{
    protected static CharLoader $plugin;

    protected function onEnable(): void
    {
        if(!libNpcDialogue::isRegistered()) libNpcDialogue::register($this);
        self::$plugin = $this;
        $this->getServer()->getPluginManager()->registerEvents(new CharListener($this), $this);

        $this->getServer()->getCommandMap()->registerAll("CharLoader",
        [
            new customize_command()
        ]);
    }
    /**
     * @return CharLoader
     */
    public static function getPlugin(): CharLoader
    {
        return self::$plugin;
    }
}