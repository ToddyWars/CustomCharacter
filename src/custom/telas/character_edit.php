<?php

namespace custom\telas;

use custom\utils\SkinUtils;
use JsonException;
use pocketmine\player\Player;
use ref\libNpcDialogue\form\NpcDialogueButtonData;
use ref\libNpcDialogue\NpcDialogue;

class character_edit
{
    private Player $player;
    private array $session = [];

    public function __construct(Player $player)
    {
        $this->player = $player;
    }

    private function getNext($array, $key) {
        $next = null;
        $index = array_search($key, $array);
        if($index !== false && $index < count($array)-1) $next = $array[$index+1];
        if(is_null($next)) $next = reset($array);
        return $next;
    }

    /**
     * @throws JsonException
     */
    public function openForm():void
    {
        $player = $this->getPlayer();
        $form = new NpcDialogue();

        $cabelos = [
            'hair1',
            'hair2',
            'hair3',
            'hair4',
            'hair5',
            'hair6',
            ];

        $corpos = [
            'saiyan',
            'arcosian',
            'namekian'
            ];


        if(empty($this->session)) $this->session = ['corpo' => current($corpos), 'cabelo' => current($cabelos)];

        $corpo = $this->session["corpo"];
        $cabelo = $this->session["cabelo"];
        SkinUtils::changeSkin($player, $cabelo, $corpo);

        $form->addButton(NpcDialogueButtonData::create()
            ->setName("§0$corpo")
            ->setClickHandler(function(Player $player) use($corpos) : void{
                $corpo  =  $this->getNext($corpos, $this->session['corpo']);
                $cabelo = $this->session['cabelo'];
                $this->session['corpo'] = $corpo;
                SkinUtils::changeSkin($player, $cabelo, $corpo);
                $this->openForm();
            })
            ->setForceCloseOnClick(false)
        );

        $form->addButton(NpcDialogueButtonData::create()
            ->setName("§0$cabelo")
            ->setClickHandler(function(Player $player) use ($cabelos) : void{
                $cabelo = $this->getNext($cabelos, $this->session['cabelo']);
                $corpo  = $this->session['corpo'];
                $this->session['cabelo'] = $cabelo;
                SkinUtils::changeSkin($player, $cabelo, $corpo);
                $this->openForm();
            })
            ->setForceCloseOnClick(false)
        );

        $form->setNpcName("Aparencia");
        $form->setDialogueBody("§0Escolha sua aparencia otaro");
        $form->setSceneName("aparencia");
        $form->sendTo($player, $player);
    }

    public function debug():void
    {
        $player = $this->getPlayer();
        $form = new NpcDialogue();

        $form->setNpcName("Aparencia");
        $form->setDialogueBody("§0Escolha sua aparencia otaro");
        $form->setSceneName("aparencia");
        $form->sendTo($player, $player);
    }

    /**
     * @return Player
     */
    private function getPlayer(): Player
    {
        return $this->player;
    }
}