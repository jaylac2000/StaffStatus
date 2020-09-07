<?php

namespace jaylac2000\StaffStatus;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\server\CommandEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use function in_array;
use function str_replace;
use function strtolower;

class Main extends PluginBase implements Listener
{

    private $list;

    public function onEnable()
    {
        $this->saveDefaultConfig();
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->list = $this->getConfig()->get("staff");
    }

    public function onCommand(CommandSender $sender, Command $command, String $label, array $args): bool
    {
        if (strtolower($command->getName()) === "staff") {
            if ($sender instanceof Player) {
                $sender->sendMessage($this->getConfig()->getNested("messages.title"));
                foreach ($this->list as $smem) {
                    if (($player = Server::getInstance()->getPlayerExact($smem)) === null) {
                        $status = $this->getConfig()->getNested("messages.offline");
                    } elseif ($player->hasPermission("stafflist.hidden")) {
                        $status = $this->getConfig()->getNested("messages.offline");
                    } else {
                        $status = $this->getConfig()->getNested("messages.online");
                    }
                    $msg = str_replace(["{staff}", "{status}"], [$smem, $status], $this->getConfig()->getNested("messages.status"));
                    $sender->sendMessage($msg);
                }
            }
            return false;
        }
        return false;
    }

    public function onExecuteCommand(CommandEvent $event): void
    {
        $commandLine = $event->getCommand();
        $commandName = explode(' ', $commandLine, 2)[0];
        $selectedCommand = Server::getInstance()->getCommandMap()->getCommand($commandName);
        if (!$selectedCommand instanceof Command) return;
        if (in_array(strtolower($commandName), $this->getConfig()->get("vanish-commands"))) {
            $player = $event->getSender();
            if ($player instanceof Player) {
                if ($player->hasPermission("stafflist.hidden")) {
                    $player->addAttachment($this, "stafflist.hidden", false);
                } else {
                    $player->addAttachment($this, "stafflist.hidden", true);
                }
            }
        }
    }
}