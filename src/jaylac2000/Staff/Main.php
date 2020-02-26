<?php

namespace jaylac2000\Staff;

use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\plugin\PluginBase;
use pocketmine\command\CommandSender;

class Main extends PluginBase implements Listener
{

    private $list;
    public function onEnable()
    {
        if(!file_exists($this->getDataFolder() . "config.yml")) $this->saveResource("config.yml");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        $this->list = $config->get("staff");
    }

    public function onCommand(CommandSender $sender, Command $command, String $label, array $args): bool
    {
        if (strtolower($command->getName()) == "staff") {

            if ($sender instanceof Player) {

                $sender->sendMessage("§l§5(-§9Staff §6Status§5-)");
                foreach($this->list as $smem) {
                    if ($this->getServer()->getPlayerExact($smem) != null)
                        $sender->sendMessage("§b" . $smem . " : §aOnline");
                    else
                        $sender->sendMessage("§b" . $smem . " : §cOffline");

                }
            }
            return false;
        }
        return false;
    }
}
