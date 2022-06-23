<?php

/*
 *  _____      _     _      __  __  _____ 
 * |  __ \    (_)   | |    |  \/  |/ ____|
 * | |__) | __ _  __| | ___| \  / | |     
 * |  ___/ '__| |/ _` |/ _ \ |\/| | |     
 * | |   | |  | | (_| |  __/ |  | | |____ 
 * |_|   |_|  |_|\__,_|\___|_|  |_|\_____|
 *
 * A minecraft bedrock server.
 *
*/

declare(strict_types=1);

namespace PracticeCore;

use pocketmine\plugin\PluginBase;

/*
 * TODO:
 * - Rainbow Splash Potion (possible using random color)
 * - FFA
 * - Duels
 * - Events
 * - Replay (like hive)
 * - Kit Editor
 * - Database (Mysql or Sqlite)
 * - Store all player data
 * - Custom NPC
*/
class Main extends PluginBase {
	
	private static Main $instance;
	
	public function onLoad() :void {
		self::$instance = $this;
	}
	
	public function onEnable() :void {
		$this->loadWorlds();
		$this->clearEntities();
		$this->registerEntities();
	}
	
	public function onDisable() :void {
		
	}
	
	private function loadWorlds() :void {
		foreach(array_diff(scandir($this->getServer()->getDataPath() . "worlds"), ["..", "."]) as $WorldName){
            $this->getServer()->getWorldManager()->loadWorld($WorldName);
        }
        foreach($this->getServer()->getWorldManager()->getWorlds() as $world) {
            $world->setTime(0);
            $world->stopTime();
        }
	}
	
	private function clearEntities(): void {
        foreach ($this->getServer()->getWorldManager()->getWorlds() as $world){
            foreach ($world->getEntities() as $entity) {
                $entity->flagForDespawn();
            }
        }
    }
	
	private function registerEntities() :void {
		EntityFactory::getInstance()->register(FishingHook::class, function(World $world, CompoundTag $nbt) : FishingHook{
			return new FishingHook(EntityDataHelper::parseLocation($nbt, $world), null, $nbt);
		}, ["FishingHook", "minecraft:fishing_hook"], EntityLegacyIds::FISHING_HOOK);
	}
	
	public static function getInstance() : Main {
		return self::$instance;
	}
}