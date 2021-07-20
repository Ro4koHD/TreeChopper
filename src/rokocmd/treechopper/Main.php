<?php

namespace rokocmd\treechopper;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\block\Block;
use pocketmine\Player;
use pocketmine\item\Item;

class Main extends PluginBase implements Listener {
    /** @var bool[] $isInUse */
    private $isInUse;

    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onBreak(BlockBreakEvent $event) {
        if(isset($this->isInUse[$event->getPlayer()->getName()]))
            return;
        $player = $event->getPlayer();
        $block = $event->getBlock();
        if($event->getBlock()->getId() === Item::LOG) {
            $this->isInUse[$player->getName()] = true;
            $this->breakTree($block, $event->getItem(), $player);
            unset($this->isInUse[$player->getName()]);
        }
    }

    public function breakTree(Block $block, Item $item, Player $player, array &$dont = []) {
        if($block->isValid()) {
            $dont[] = $block->asVector3()->__toString();
            foreach($block->getAllSides() as $side)
                if($side->getName() === $block->getName() || $side->getName() === "Leaves" and !in_array($side->asVector3()->__toString(), $dont))
                    $this->breakTree($side, $item, $player, $dont);
            $block->getLevel()->useBreakOn($block, $item, $player, true);
        }
    }
}
