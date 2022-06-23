<?php

declare(strict_types=1);

namespace zodiax\game\behavior\fishing;

use pocketmine\entity\Human;
use pocketmine\entity\Location;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\inventory\InventoryHolder;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;
use pocketmine\world\sound\ThrowSound;
use PracticeCore\Entity\Projectile\FishingHook;
use PracticeCore\Utils\PluginUtils;

class FishingBehavior{

	private ?FishingHook $fishing = null;
	private ?IFishingBehaviorEntity $parent;

	public function __construct(IFishingBehaviorEntity $human){
		$this->parent = $human;
	}

	public function getParentEntity() : ?Human{
		return $this->parent?->getFishingEntity();
	}

	public function startFishing() : void{
		$parent = $this->parent?->getFishingEntity();
		if($parent === null || !$parent->isAlive() || $this->isFishing() || ($parent instanceof InventoryHolder && $parent->getInventory()->getItemInHand()->getId() !== ItemIds::FISHING_ROD)){
			return;
		}
		$players = [];
		if($parent instanceof Player){
			$players = PluginUtil::getViewersForPosition($parent);
		}
		$location = $parent->getLocation();
		$world = $location->getWorld();
		($ev = new ProjectileLaunchEvent(new FishingHook(Location::fromObject($parent->getEyePos(), $world, $location->getYaw(), $location->getPitch()), $parent, null, $players)))->call();
		if($ev->isCancelled()){
			$hook->flagForDespawn();
			return;
		}
		$world->addSound($location, new ThrowSound(), $players);
		$this->fishing = $hook;
	}

	public function stopFishing() : void{
		if($this->isFishing()){
			$this->fishing?->reelLine();
			$this->fishing = null;
		}
	}

	public function isFishing() : bool{
		return $this->fishing !== null;
	}
}