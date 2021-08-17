<?php

#Name: AutoSellGUI
#Plugin make by: QuocThien908
#Plugin Created: 7/8/2021

#VUI LÒNG TÔN TRỌNG CHỦ PLUGIN. KHÔNG ĐƯỢC ĐỔI TÊN AUTHOR!!!

namespace QuocThien908\AutoSellGUI;

use pocketmine\{Player, Server};

use pocketmine\plugin\PluginBase;

use pocketmine\event\Listener;

use pocketmine\command\{Command,CommandSender, CommandExecutor, ConsoleCommandSender};

use pocketmine\inventory\BaseInventory;

use pocketmine\event\player\{PlayerQuitEvent, PlayerJoinEvent};
use pocketmine\event\block\BlockBreakEvent;

use pocketmine\item\Item;

use libs\muqsit\invmenu\InvMenu;
use libs\muqsit\invmenu\InvMenuHandler;

class Main extends PluginBase implements Listener {
    
    public $prefix = "§l§8[§eAuto§6Sell§8]";
    
    private $mode = [];
#=========ENABLE===========#
    public function onEnable(){
        $this->getLogger()->info("$this->prefix \n\n§aPlugin Đã Bật!\n§8[§bPlugin by §eQuocThien908§8]§d•^•\n");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->menu = InvMenu::create(InvMenu::TYPE_CHEST);
        
        if(!InvMenuHandler::isRegistered()){
            InvMenuHandler::register($this);
        }
    }
#=========DISNABLE===========#
    public function onDisable (){
        $this->getLogger()->info("$this->prefix \n§cPlugin Đã Tắt UwU!");
    }
#=========ON JOIN===========#
    public function onJoin (PlayerJoinEvent $join){
        $playerjoin = $join->getPlayer()->getName();
        $this->mode[$playerjoin] = "off";
    }
#=========ON COMMAND===========#
    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool {
       switch($cmd->getName()){
               case "autosell":
                   $this->openAutoSellGUI($sender);
                   break;
       }
       return true;
   }
#=========OPEN GUI===========#
   public function openAutoSellGUI($sender){
        $this->menu->readonly();
        $this->menu->setListener([$this, "AutoSellCMD"]);
        $this->menu->setName("§f§l• §eAuto§aSell§f •");
        $inventory = $this->menu->getInventory();
       
        $inventory->setContents(array_fill(0, 27, Item::get(160, 15, 1)));
        $inventory->setItem(11, Item::get(341, 0, 1)->setCustomName("§l§6AutoSell §aOn")); //AutoSell On
        $inventory->setItem(15, Item::get(331, 0, 1)->setCustomName("§l§6AutoSell §cOff")); //AutoSell Off
        $inventory->setItem(10, Item::get(0, 8, 1) );
        $inventory->setItem(12, Item::get(0, 9, 1) );
        $inventory->setItem(13, Item::get(0, 9, 1) );
        $inventory->setItem(14, Item::get(0, 9, 1) );
        $inventory->setItem(16, Item::get(0, 8, 1) );
        $this->menu->send($sender);
    }
#=========AUTOSELL COMMAND===========#
    public function AutoSellCMD(Player $sender, Item $item){
        $hand = $sender->getInventory()->getItemInHand()->getCustomName();
        $inventory = $this->menu->getInventory();
    #=========AUTOSELL ON===========#
        if($item->getId() == 341 ){
             $this->mode[$sender->getName()] = "on";
             $sender->sendMessage("$this->prefix §aĐã bật!");
             $sender->removeWindow($inventory);
        }
     #=========AUTOSELL OFF===========#
        if($item->getId() == 331 ){
             $this->mode[$sender->getName()] = "off";
             $sender->sendMessage("$this->prefix §cĐã tắt!");
             $sender->removeWindow($inventory);
        }
    }
#=========ON BREAK===========#
    public function onBreak(BlockBreakEvent $event) : void {
        $player = $event->getPlayer();
        foreach($event->getDrops() as $drop) {
            if(!$player->getInventory()->canAddItem($drop)) 
            {
                if ($this->mode[$player->getName()] == "on") 
                {
                $this->getServer()->dispatchCommand($player, "sell all");
                $player->sendMessage("$this->prefix §aĐã tự động bán tất cả item!");
                }
                break;
            }
        }
    }
#=========ON QUIT===========#
    public function onQuit(PlayerQuitEvent $quit){
        $playerquit = $quit->getPlayer()->getName();
        $this->mode[$playerquit] == "off";
    }
    
}
#==============END CODE================#
