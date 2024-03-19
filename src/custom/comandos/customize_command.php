<?php

namespace custom\comandos;

use custom\telas\character_edit;
use JsonException;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;

class customize_command extends Command
{

    public function __construct()
    {
        $this->setPermission(DefaultPermissions::ROOT_OPERATOR);
        parent::__construct("customizar", "customizar personagem", "/cc", ['cc']);
    }
    /**
     * @throws JsonException
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if(!$sender instanceof Player) return;
        (new character_edit($sender))->openForm();
    }
}