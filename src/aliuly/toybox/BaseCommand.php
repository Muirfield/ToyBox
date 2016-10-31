<?php
namespace aliuly\toybox;

use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\command\PluginCommand;
use pocketmine\Player;
use aliuly\toybox\common\mc;

abstract class BaseCommand implements CommandExecutor {
	protected $owner;

	public function __construct($owner) {
		$this->owner = $owner;
	}

	//public function onCommand(CommandSender $sender,Command $command,$label, array $args);

	public function enableCmd($cmd,$yaml) {
		$newCmd = new PluginCommand($cmd,$this->owner);
		if (isset($yaml["description"]))
			$newCmd->setDescription($yaml["description"]);
		if (isset($yaml["usage"]))
			$newCmd->setUsage($yaml["usage"]);
		if(isset($yaml["aliases"]) and is_array($yaml["aliases"])) {
			$aliasList = [];
			foreach($yaml["aliases"] as $alias) {
				if(strpos($alias,":")!== false) {
					$this->owner->getLogger()->warning(mc::_("Unable to load alias %1%",$alias));
					continue;
				}
				$aliasList[] = $alias;
			}
			$newCmd->setAliases($aliasList);
		}
		if(isset($yaml["permission"]))
			$newCmd->setPermission($yaml["permission"]);
		if(isset($yaml["permission-message"]))
			$newCmd->setPermissionMessage($yaml["permission-message"]);
		$newCmd->setExecutor($this);
		$cmdMap = $this->owner->getServer()->getCommandMap();
		$cmdMap->register($this->owner->getDescription()->getName(),$newCmd);
	}

	public function inGame(CommandSender $sender,$msg = true) {
		if (!($sender instanceof Player)) {
			if ($msg) $sender->sendMessage(mc::_("You can only do this in-game"));
			return false;
		}
		return true;
	}

	public function getState(CommandSender $player,$default) {
		return $this->owner->getState(get_class($this),$player,$default);
	}
	public function setState(CommandSender $player,$val) {
		$this->owner->setState(get_class($this),$player,$val);
	}
	public function unsetState(CommandSender $player) {
		$this->owner->unsetState(get_class($this),$player);
	}
}
