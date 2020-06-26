<?php

namespace jaylac2000\StaffStatus;

use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\plugin\PluginBase;
use pocketmine\command\CommandSender;
use pocketmine\scheduler\PluginTask;
use pocketmine\utils\TextFormat as C;
use pocketmine\network\mcpe\protocol\PlayerListPacket;
use pocketmine\network\mcpe\protocol\types\PlayerListEntry;
use pocketmine\network\mcpe\protocol\types\SkinData;
use pocketmine\network\mcpe\protocol\types\SkinAdapterSingleton;

use function array_search;
use function in_array;
use function strtolower;

class Main extends PluginBase {
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
    public const PREFIX = C::GOLD . "Invisible " . C::DARK_BLUE . "> ". C::RESET;

    public static $invisible = [];

    public static $nametagg = [];

    public $pk;

    protected static $main;

    public function onEnable(){
        self::$main = $this;
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
        $this->getScheduler()->scheduleRepeatingTask(new InvisibleTask(), 20);
        @mkdir($this->getDataFolder());
        $this->saveDefaultConfig();
    }

    public static function getMain(): self{
        return self::$main;
    }

    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool{
        $name = $sender->getName();
        switch(strtolower($cmd->getName())){
		case "invisible":
		case "invis":
	        if(!$sender instanceof Player){
                $sender->sendMessage(self::PREFIX . C::DARK_RED . "Please use this command inside the game...");
                return false;
	        }

	        if(!$sender->hasPermission("invis.use")){
		        $sender->sendMessage(self::PREFIX . C::DARK_RED . "You do not have permission to use this command");
                return false;
	        }

            if(!in_array($name, self::$invisible)){
                self::$invisible[] = $name;
		        $sender->sendMessage(self::PREFIX . C::GREEN . "You are now invisible.");
		        $nameTag = $sender->getNameTag();
		        self::$nametagg[$name] = $nameTag;
		        $sender->setNameTag("§b[Invisible]§r $nameTag");
                if($this->getConfig()->get("enable-leave") === true){
                    $msg = $this->getConfig()->get("FakeLeave");
                    $msg = str_replace("%name", "$name", $msg);
                    $this->getServer()->broadcastMessage($msg);
             	}
            }else{
                unset(self::$invisible[array_search($name, self::$invisible)]);
                foreach($this->getServer()->getOnlinePlayers() as $players){
                    $players->showPlayer($sender);
                    $nameTag = self::$nametagg[$name];
                    $sender->setNameTag("$nameTag");
                }
             $pk = new PlayerListPacket();
             $pk->type = PlayerListPacket::TYPE_ADD;
                 $pk->entries[] = PlayerListEntry::createAdditionEntry($sender->getUniqueId(), $sender->getId(), $sender->getDisplayName(), SkinAdapterSingleton::get()->toSkinData($sender->getSkin()), $sender->getXuid());
             foreach($this->getServer()->getOnlinePlayers() as $p)
             $p->sendDataPacket($pk);
		        if($this->getConfig()->get("enable-join") === true){
                        $msg = $this->getConfig()->get("FakeJoin");
                        $msg = str_replace("%name", "$name", $msg);
                        $this->getServer()->broadcastMessage($msg);
		        }
               	    $sender->sendMessage(self::PREFIX . C::DARK_RED . "You are no longer invisible!");
            }
        }
        return true;
    }
}
