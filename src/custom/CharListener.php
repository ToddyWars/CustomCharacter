<?php

namespace custom;

use custom\telas\character_edit;
use JsonException;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;

class CharListener implements Listener
{
    private CharLoader $plugin;
    public function __construct(CharLoader $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * @throws JsonException
     */
    public function join(PlayerJoinEvent $event): void
    {
        (new character_edit($event->getPlayer()))->debug();
    }

    /**
     * @return CharLoader
     */
    public function getPlugin(): CharLoader
    {
        return $this->plugin;
    }
}