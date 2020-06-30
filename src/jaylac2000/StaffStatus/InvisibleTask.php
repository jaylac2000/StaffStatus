<?php

namespace jaylac2000\StaffStatus;

use pocketmine\network\mcpe\protocol\PlayerListPacket;
use pocketmine\network\mcpe\protocol\types\PlayerListEntry;
use pocketmine\network\mcpe\protocol\types\SkinData;
use pocketmine\network\mcpe\protocol\types\SkinAdapterSingleton;
use pocketmine\scheduler\Task;
use pocketmine\Server;

use function in_array;

class InvisibleTask extends Task {
    public $pk;

    public function onRun(int $currentTick){
        foreach(Server::getInstance()->getOnlinePlayers() as $p){
            if($p->spawned){
                if(in_array($p->getName(), Invisible::$invisible)){
                    foreach(Server::getInstance()->getOnlinePlayers() as $player){
                        $p->sendPopup("§bYou are currently invisible");
			            if($player->hasPermission("invisible.see")){
			                $player->showPlayer($p);
		                }else{
			                $player->hidePlayer($p);
			                $entry = new PlayerListEntry();
			                $entry->uuid = $p->getUniqueId();

			                $pk = new PlayerListPacket();
			                $pk->entries[] = $entry;
			                $pk->type = PlayerListPacket::TYPE_REMOVE;
			                $player->sendDataPacket($pk);
		                }
                    }
                }
            }
        }
    }
}
