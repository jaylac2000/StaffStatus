<?php

namespace jaylac2000\StaffStatus;

use pocketmine\event\Listener;
use pocketmine\Server;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\inventory\InventoryPickupItemEvent;

use function array_search;
use function in_array;

class EventListener implements Listener {

    public function onQuit(PlayerQuitEvent $event) {
        $player = $event->getPlayer();
        $name = $player->getName();
        if (in_array($name, Invisible::$invisible)) {
            unset(Invisible::$invisible[array_search($name, Invisible::$invisible)]);
        }
    }

    public function PickUp(InventoryPickupItemEvent $event) {
        $inv = $event->getInventory();
        $player = $inv->getHolder();
        $name = $player->getName();
        if (in_array($name, Invisible::$invisible)) {
            $event->setCancelled();
        }
    }
}