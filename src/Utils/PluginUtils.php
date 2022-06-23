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

namespace PracticeCore\Utils;

use PracticeCore\Player\Player;

class PluginUtils {
	public const spaceChar = " ";
	public const PADDING_LINE = 0;
	public const PADDING_CENTER = 1;
	public const lineLength = 30;
	public const charWidth = 6;
	/////////////////////////////////////////WORLD////////////////////////////////////////
	
	public static function copyDirectory(string $from, string $to) : void{
		@mkdir($to, 0777, true);
		$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($from, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST);
		foreach($files as $fileInfo){
			$target = str_replace($from, $to, $fileInfo->getPathname());
			if($fileInfo->isDir()){
				@mkdir($target, 0777, true);
			}else{
				$contents = file_get_contents($fileInfo->getPathname());
				file_put_contents($target, $contents);
			}
		}
	}

	public static function removeDirectory(string $dir) : void{
		$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
		foreach($files as $fileInfo){
			if($fileInfo->isDir()){
				rmdir($fileInfo->getPathname());
			}else{
				unlink($fileInfo->getPathname());
			}
		}
		rmdir($dir);
	}
	
	public static function getViewersForPosition(Player $player) : array{
		$players = [];
		$position = $player->getPosition();
		$world = $position->getWorld();
		foreach($world->getViewersForPosition($position) as $p){
			if($p->canSee($player)){
				$players[] = $p;
			}
		}
		return $players;
	}
	
	//////////////////////////////////////////////////////////////////////////////////////
	
	/////////////////////////////////////TIME////////////////////////////////////////////
	
	public static function secondsToTicks(int $secs) : int{
		return $secs * 20;
	}

	public static function minutesToTicks(int $mins) : int{
		return $mins * 1200;
	}

	public static function hoursToTicks(int $hours) : int{
		return $hours * 72000;
	}

	public static function ticksToSeconds(int $ticks) : int{
		return (int) ($ticks / 20);
	}

	public static function ticksToMinutes(int $ticks) : int{
		return (int) ($ticks / 1200);
	}

	public static function ticksToHours(int $ticks) : int{
		return (int) ($ticks / 72000);
	}
	
	//////////////////////////////////////////////////////////////////////////////////////
	
	///////////////////////////////////////TEXT///////////////////////////////////////////
	
	public static function centerText(string $input, int $maxLength = 0, bool $addRightPadding = false) : string{
		$lines = explode("\n", trim($input));
		$sortedLines = $lines;
		usort($sortedLines, static function(string $a, string $b){
			return self::getPixelLength($b) <=> self::getPixelLength($a);
		});
		$longest = $sortedLines[0];
		if($maxLength === 0){
			$maxLength = self::getPixelLength($longest);
		}
		$result = "";
		$spaceWidth = self::getCharWidth(self::spaceChar);
		foreach($lines as $sortedLine){
			$len = max($maxLength - self::getPixelLength($sortedLine), 0);
			$padding = (int) round($len / (2 * $spaceWidth));
			$paddingRight = (int) floor($len / (2 * $spaceWidth));
			$result .= str_pad(self::spaceChar, $padding) . $sortedLine . ($addRightPadding ? str_pad(self::spaceChar, $paddingRight) : "") . "\n";
		}
		return rtrim($result, "\n");
	}
	
	public static function centerLine(string $input) : string{
		return self::centerText($input, self::lineLength * self::charWidth);
	}
	
	public static function getPixelLength(string $line) : int{
		$length = 0;
		foreach(str_split(TextFormat::clean($line)) as $c){
			$length += self::getCharWidth($c);
		}
		$length += substr_count($line, TextFormat::BOLD);
		return $length;
	}

	private static function getCharWidth(string $c) : int{
		return self::charWidths[$c] ?? self::charWidth;
	}
	
	/////////////////////////////////////////////////////////////////////////////////////
}