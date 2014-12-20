<?php
// This class will be for the pet battle system.
//
// TO DOO
// 1. Pet Information View (Self and Other Player)
// 2. Pet Battle Pick System (Show other players and their pets)
// 3. Pet Trading (Ability to trade pets with other users)
// 4. Pet Fusion (Combine pets for power boosts, +HP and +ATK)

class BattlePet
{
private $Connection;

public $User_ID;
public $Pet_ID;

public $Pet_Image;

public $Pet_Offense;
public $Pet_Defense;

public $Pet_Current_Health;
public $Pet_Max_Health;

public $Pet_Current_AP;
public $Pet_Max_AP;

public $Pet_Skill_1;
public $Pet_Skill_2;

public $Pet_Bonus_Offense;
public $Pet_Bonus_Defense;
public $Pet_Bonus_Health;
public $Pet_Bonus_EXP;

public $Pet_Exp;
public $Pet_Level;

public $Pet_Name;
public $Pet_Type;
public $Pet_Status;
public $Pet_Active;

public $Last_Pet_Aquired;

public $Active_Pet_ID;


// Main constructor used to initiate the class and setup objects for the current active pet.
// You can pass in an optional pet id to make it the active pet for this object.
function __construct($User_ID,$Pet_ID=0)
{
    $this->Connection = new Connection();

    $this->User_ID = $User_ID;

    if ($Pet_ID>0){
        $Active_Pet = $Pet_ID;
    } else {
        $Active_Pet = $this->Get_Active_Pet_ID();
    }

    // Here we check to make sure we revived a valid Active pet for the user. If not we create a new one for them if they have 0 pets.
    if ($Active_Pet != NULL && $Active_Pet != 0 && $Active_Pet != ""){
        // If the active pet id was successfully grabbed, go ahead and set the pet info.
        $this->Set_Pet_Info($Active_Pet);
    } else {
        // Otherwise we check and create a new pet as long as the user ID isn't a wild pet.
        if ($this->Get_Total_Pet_Count() < 1 && $this->User_ID != 0) {
            Toasts::addNewToast("It looks like you don't have any pets :( <br>Here is a free one to get started!", 'success');
            $this->Give_Random_Pet();
        }
    }
}

// Returns the current Active Pet ID
private function Get_Active_Pet_ID()
{
    $Pet_Array = array (':User_ID'=>$this->User_ID);
    $Pet_SQL = "SELECT * FROM pets WHERE User_ID = :User_ID AND Pet_Active = 1 LIMIT 1";
    $Pet_Result = $this->Connection->Custom_Query($Pet_SQL, $Pet_Array);

    return $Pet_Result["Pet_ID"];
}

// Returns an array of all pets owned by the user.
public function Get_All_Pets()
{
    $Pet_Array = array (':User_ID'=>$this->User_ID);
    $Pet_SQL = "SELECT * FROM pets WHERE User_ID = :User_ID";

    $Pet_Result = $this->Connection->Custom_Query($Pet_SQL, $Pet_Array, true);

    return $Pet_Result;
}

// Returns an array of all inactive pets owned by the user.
public function Get_All_Inactive_Pets()
{
    $Pet_Array = array (':User_ID'=>$this->User_ID);
    $Pet_SQL = "SELECT * FROM pets WHERE Pet_Active = 0 AND User_ID = :User_ID";

    $Pet_Result = $this->Connection->Custom_Query($Pet_SQL, $Pet_Array, true);

    return $Pet_Result;
}

public function Add_Battles_Won()
{
    $User_Array = array();
    $User_Array[':User_ID']=$this->User_ID;

    $User_SQL = "UPDATE users SET Pet_Battles_Won=Pet_Battles_Won+1 WHERE ID=:User_ID";
    $Results = $this->Connection->Custom_Execute($User_SQL, $User_Array);
}

public function Add_Battles_Lost()
{
    $User_Array = array();
    $User_Array[':User_ID']=$this->User_ID;

    $User_SQL = "UPDATE users SET Pet_Battles_Lost=Pet_Battles_Lost+1 WHERE ID=:User_ID";
    $Results = $this->Connection->Custom_Execute($User_SQL, $User_Array);
}

public function Add_Pets_Caught()
{
    $User_Array = array();
    $User_Array[':User_ID']=$this->User_ID;

    $User_SQL = "UPDATE users SET Pets_Caught=Pets_Caught+1 WHERE ID=:User_ID";
    $Results = $this->Connection->Custom_Execute($User_SQL, $User_Array);
}

// Set the current objects information for the active pet.
public function Set_Pet_Info($Pet_ID)
{
    $Pet_Array = array (':User_ID'=>$this->User_ID, ':Pet_ID'=>$Pet_ID);
    $Pet_SQL = "SELECT * FROM pets WHERE User_ID = :User_ID AND Pet_ID = :Pet_ID";
    $Pet_Result = $this->Connection->Custom_Query($Pet_SQL, $Pet_Array);

    $this->Active_Pet_ID            = (int) $Pet_ID;
    $this->Pet_ID                   = (int) $Pet_ID;
    $this->Pet_Image                = (string) $Pet_Result["Pet_Image"];
    $this->Pet_Offense              = (int) $Pet_Result["Pet_Offense"];
    $this->Pet_Defense              = (int) $Pet_Result["Pet_Defense"];
    $this->Pet_Current_Health       = (int) $Pet_Result["Pet_Current_Health"];
    $this->Pet_Max_Health           = (int) $Pet_Result["Pet_Max_Health"];
    $this->Pet_Current_AP           = (int) $Pet_Result["Pet_Current_AP"];
    $this->Pet_Max_AP               = (int) $Pet_Result["Pet_Max_AP"];
    $this->Pet_Skill_1              = (string) $Pet_Result["Pet_Skill_1"];
    $this->Pet_Skill_2              = (string) $Pet_Result["Pet_Skill_2"];
    $this->Pet_Skill_3              = (string) $Pet_Result["Pet_Skill_3"];
    $this->Pet_Bonus_Offense        = (int) $Pet_Result["Pet_Bonus_Offense"];
    $this->Pet_Bonus_Defense        = (int) $Pet_Result["Pet_Bonus_Defense"];
    $this->Pet_Bonus_Health         = (int) $Pet_Result["Pet_Bonus_Health"];
    $this->Pet_Bonus_EXP            = (int) $Pet_Result["Pet_Bonus_EXP"];
    $this->Pet_Exp                  = (int) $Pet_Result["Pet_Exp"];
    $this->Pet_Level                = (int) $Pet_Result["Pet_Level"];
    $this->Pet_Name                 = (string) $Pet_Result["Pet_Name"];
    $this->Pet_Type                 = (string) $Pet_Result["Pet_Type"];
    $this->Pet_Status               = (string) $Pet_Result["Pet_Status"];
    $this->Pet_Tier                 = (int) $Pet_Result["Pet_Tier"];
    $this->Pet_Active               = (int) $Pet_Result["Pet_Active"];
}

public function Give_Caught_Pet()
{
    $Pet_Image = explode("/", $_SESSION['PVE_AI_Pet_Image']);
    $Pet_Array = array();
    $Pet_Array[':User_ID']=$this->User_ID;
    $Pet_Array[':Pet_Name']=$_SESSION['PVE_AI_Pet_Name'];
    $Pet_Array[':Pet_Level']=$_SESSION['PVE_AI_Pet_Level'];
    $Pet_Array[':Pet_Image']=$Pet_Image[2];
    $Pet_Array[':Pet_Offense']=$_SESSION['PVE_AI_Pet_Offense'];
    $Pet_Array[':Pet_Defense']=$_SESSION['PVE_AI_Pet_Defense'];
    $Pet_Array[':Pet_Max_Health']=$_SESSION['PVE_AI_Pet_Max_Health'];
    $Pet_Array[':Pet_Current_Health']=$_SESSION['PVE_AI_Pet_Max_Health'];
    $Pet_Array[':Pet_Max_AP']=$_SESSION['PVE_AI_Pet_Max_AP'];
    $Pet_Array[':Pet_Current_AP']=$_SESSION['PVE_AI_Pet_Max_AP'];
    $Pet_Array[':Pet_Skill_1']=$_SESSION['PVE_AI_Pet_Skill_1'] ;
    $Pet_Array[':Pet_Skill_2']=$_SESSION['PVE_AI_Pet_Skill_2'] ;
    $Pet_Array[':Pet_Skill_3']=$_SESSION['PVE_AI_Pet_Skill_3'] ;
    $Pet_Array[':Pet_Type']=$_SESSION['PVE_AI_Pet_Type'];
    $Pet_Array[':Pet_Tier']=$_SESSION['PVE_AI_Pet_Tier'];

    $Pet_SQL = "INSERT INTO pets (User_ID, Pet_Level, Pet_Name, Pet_Image, Pet_Offense, Pet_Defense, Pet_Max_Health, Pet_Current_Health, Pet_Max_AP, Pet_Current_AP, Pet_Skill_1, Pet_Skill_2, Pet_Skill_3, Pet_Type, Pet_Tier) VALUES (:User_ID, :Pet_Level, :Pet_Name, :Pet_Image, :Pet_Offense, :Pet_Defense, :Pet_Max_Health, :Pet_Current_Health, :Pet_Max_AP, :Pet_Current_AP, :Pet_Skill_1, :Pet_Skill_2, :Pet_Skill_3, :Pet_Type, :Pet_Tier)";
    $Results = $this->Connection->Custom_Execute($Pet_SQL, $Pet_Array);
    $New_Pet_ID = $this->Connection->PDO_Connection->lastInsertId();
}

// Creates a new random pet for the user and adds them to the DB.
public function Give_Random_Pet($Tier=1)
{
    // Setup YAML stuff.
    $RandomPet = Spyc::YAMLLoad('/var/www/petbattles/data/base_pet_list.yml');
    $MaxPets = count($RandomPet['pets'][$Tier]) - 1;
    $Random = rand(0,$MaxPets);

    // Here I will load up a random pet from a YAML config file and then add it to the DB.
    $Pet_Name = $RandomPet['pets'][$Tier][$Random]['name'];
    $Pet_Image = $RandomPet['pets'][$Tier][$Random]['image'];
    $Pet_Offense = $RandomPet['pets'][$Tier][$Random]['offense'];
    $Pet_Defense = $RandomPet['pets'][$Tier][$Random]['defense'];
    $Pet_Max_Health = $RandomPet['pets'][$Tier][$Random]['max_health'];
    $Pet_Max_AP = $RandomPet['pets'][$Tier][$Random]['max_ap'];
    $Pet_Skill_1 = $RandomPet['pets'][$Tier][$Random]['skill_1'];
    $Pet_Skill_2 = $RandomPet['pets'][$Tier][$Random]['skill_2'];
    $Pet_Skill_3 = $RandomPet['pets'][$Tier][$Random]['skill_3'];
    $Pet_Type = $RandomPet['pets'][$Tier][$Random]['type'];
    $Pet_Tier = $Tier;


    $Pet_Array = array();
    $Pet_Array[':User_ID']=$this->User_ID;
    $Pet_Array[':Pet_Name']=$Pet_Name;
    $Pet_Array[':Pet_Image']=$Pet_Image;
    $Pet_Array[':Pet_Offense']=$Pet_Offense;
    $Pet_Array[':Pet_Defense']=$Pet_Defense;
    $Pet_Array[':Pet_Max_Health']=$Pet_Max_Health;
    $Pet_Array[':Pet_Current_Health']=$Pet_Max_Health;
    $Pet_Array[':Pet_Max_AP']=$Pet_Max_AP;
    $Pet_Array[':Pet_Current_AP']=$Pet_Max_AP;
    $Pet_Array[':Pet_Skill_1']=$Pet_Skill_1;
    $Pet_Array[':Pet_Skill_2']=$Pet_Skill_2;
    $Pet_Array[':Pet_Skill_3']=$Pet_Skill_3;
    $Pet_Array[':Pet_Type']=$Pet_Type;
    $Pet_Array[':Pet_Tier']=$Pet_Tier;

    $Pet_SQL = "INSERT INTO pets (User_ID, Pet_Name, Pet_Image, Pet_Offense, Pet_Defense, Pet_Max_Health, Pet_Current_Health, Pet_Max_AP, Pet_Current_AP, Pet_Skill_1, Pet_Skill_2, Pet_Skill_3, Pet_Type, Pet_Tier) VALUES (:User_ID, :Pet_Name, :Pet_Image, :Pet_Offense, :Pet_Defense, :Pet_Max_Health, :Pet_Current_Health, :Pet_Max_AP, :Pet_Current_AP, :Pet_Skill_1, :Pet_Skill_2, :Pet_Skill_3, :Pet_Type, :Pet_Tier)";
    $Results = $this->Connection->Custom_Execute($Pet_SQL, $Pet_Array);
    $New_Pet_ID = $this->Connection->PDO_Connection->lastInsertId();

    if ($Results){
        Toasts::addNewToast("You received a new Pet! <br>[{$Pet_Name}]", 'petbattle');
        Write_Log('pets', "User_ID [" . $this->User_ID . "] just received a new pet with the id [ " . $New_Pet_ID  . "]");
    }
}


public function Create_Wild_Pet($Tier=1)
{
    // Setup YAML stuff.
    $RandomPet = Spyc::YAMLLoad('/var/www/petbattles/data/base_pet_list.yml');
    $MaxPets = count($RandomPet['pets'][$Tier]) - 1;
    $Random = rand(0,$MaxPets);

    // Here I will load up a random pet from a YAML config file and then add it to the DB.
    $Pet_Name = $RandomPet['pets'][$Tier][$Random]['name'];
    $Pet_Image = $RandomPet['pets'][$Tier][$Random]['image'];
    $Pet_Offense = $RandomPet['pets'][$Tier][$Random]['offense'];
    $Pet_Defense = $RandomPet['pets'][$Tier][$Random]['defense'];
    $Pet_Max_Health = $RandomPet['pets'][$Tier][$Random]['max_health'];
    $Pet_Max_AP = $RandomPet['pets'][$Tier][$Random]['max_ap'];
    $Pet_Skill_1 = $RandomPet['pets'][$Tier][$Random]['skill_1'];
    $Pet_Skill_2 = $RandomPet['pets'][$Tier][$Random]['skill_2'];
    $Pet_Skill_3 = $RandomPet['pets'][$Tier][$Random]['skill_3'];
    $Pet_Type = $RandomPet['pets'][$Tier][$Random]['type'];
    $Pet_Tier = $Tier;


    $Pet_Array = array();
    $Pet_Array[':User_ID']=0;
    $Pet_Array[':Pet_Name']=$Pet_Name;
    $Pet_Array[':Pet_Image']=$Pet_Image;
    $Pet_Array[':Pet_Offense']=$Pet_Offense;
    $Pet_Array[':Pet_Defense']=$Pet_Defense;
    $Pet_Array[':Pet_Max_Health']=$Pet_Max_Health;
    $Pet_Array[':Pet_Current_Health']=$Pet_Max_Health;
    $Pet_Array[':Pet_Max_AP']=$Pet_Max_AP;
    $Pet_Array[':Pet_Current_AP']=$Pet_Max_AP;
    $Pet_Array[':Pet_Skill_1']=$Pet_Skill_1;
    $Pet_Array[':Pet_Skill_2']=$Pet_Skill_2;
    $Pet_Array[':Pet_Skill_3']=$Pet_Skill_3;
    $Pet_Array[':Pet_Type']=$Pet_Type;
    $Pet_Array[':Pet_Tier']=$Pet_Tier;

    $Pet_SQL = "INSERT INTO pets (User_ID, Pet_Name, Pet_Image, Pet_Offense, Pet_Defense, Pet_Max_Health, Pet_Current_Health, Pet_Max_AP, Pet_Current_AP, Pet_Skill_1, Pet_Skill_2, Pet_Skill_3, Pet_Type, Pet_Tier) VALUES (:User_ID, :Pet_Name, :Pet_Image, :Pet_Offense, :Pet_Defense, :Pet_Max_Health, :Pet_Current_Health, :Pet_Max_AP, :Pet_Current_AP, :Pet_Skill_1, :Pet_Skill_2, :Pet_Skill_3, :Pet_Type, :Pet_Tier)";
    $Results = $this->Connection->Custom_Execute($Pet_SQL, $Pet_Array);
    $New_Pet_ID = $this->Connection->PDO_Connection->lastInsertId();

    return $New_Pet_ID;
}

public function Clear_Battle_Room_PVE()
{
    unset($_SESSION['PVE_User_Pet_ID']);
    unset($_SESSION['PVE_User_Pet_Name']);
    unset($_SESSION['PVE_User_Pet_Image']);
    unset($_SESSION['PVE_User_Pet_Skill_1_Cooldown']);
    unset($_SESSION['PVE_User_Pet_Skill_2_Cooldown']);
    unset($_SESSION['PVE_User_Pet_Skill_3_Cooldown']);

    unset($_SESSION['PVE_AI_Pet_ID']);
    unset($_SESSION['PVE_AI_Pet_Name']);
    unset($_SESSION['PVE_AI_Pet_Image']);
}

public function Create_Battle_Room_PVE()
{
    $PET_IMAGE_PATH = 'petbattles/images/';
    $I = 1;


    $_SESSION['PVE_User_Pet_ID'] = $this->Pet_ID;
    $_SESSION['PVE_User_Pet_Name'] = $this->Pet_Name;
    $_SESSION['PVE_User_Pet_Image'] = $PET_IMAGE_PATH . $this->Pet_Image;
    $_SESSION['PVE_User_Pet_Offense'] = $this->Pet_Offense;
    $_SESSION['PVE_User_Pet_Defense'] = $this->Pet_Defense;
    $_SESSION['PVE_User_Pet_Current_Health'] = $this->Pet_Current_Health;
    $_SESSION['PVE_User_Pet_Max_Health'] = $this->Pet_Max_Health;
    $_SESSION['PVE_User_Pet_Current_AP'] = $this->Pet_Current_AP;
    $_SESSION['PVE_User_Pet_Max_AP'] = $this->Pet_Max_AP;

    $_SESSION['PVE_User_Pet_Skill_1'] = $this->Pet_Skill_1;
    $Pet_Skill = $this->Get_Pet_Skill_Array($this->Pet_Skill_1);
    $_SESSION['PVE_User_Pet_Skill_1_Type'] = $Pet_Skill['Ability_Damage_Type'];

    $_SESSION['PVE_User_Pet_Skill_2'] = $this->Pet_Skill_2;
    $Pet_Skill = $this->Get_Pet_Skill_Array($this->Pet_Skill_2);
    $_SESSION['PVE_User_Pet_Skill_2_Type'] = $Pet_Skill['Ability_Damage_Type'];

    $_SESSION['PVE_User_Pet_Skill_3'] = $this->Pet_Skill_3;
    $Pet_Skill = $this->Get_Pet_Skill_Array($this->Pet_Skill_3);
    $_SESSION['PVE_User_Pet_Skill_3_Type'] = $Pet_Skill['Ability_Damage_Type'];

    $_SESSION['PVE_User_Pet_Bonus_Offense'] = $this->Pet_Bonus_Offense;
    $_SESSION['PVE_User_Pet_Bonus_Defense'] = $this->Pet_Bonus_Defense;
    $_SESSION['PVE_User_Pet_Bonus_EXP'] = $this->Pet_Bonus_EXP;
    $_SESSION['PVE_User_Pet_Exp'] = $this->Pet_Exp;
    $_SESSION['PVE_User_Pet_Level'] = $this->Pet_Level;
    $_SESSION['PVE_User_Pet_Type'] = $this->Pet_Type;
    $_SESSION['PVE_User_Pet_Tier'] = $this->Pet_Tier;


    $AI_Pet_ID = $this->Create_Wild_Pet($this->Pet_Tier);
    $AI_Pet = new BattlePet(0, $AI_Pet_ID);

    $Level_Of_AI = rand($this->Pet_Level-1,$this->Pet_Level+1);

    while ($I < $Level_Of_AI) {
        $AI_Pet->LevelUp_Pet($AI_Pet_ID);
        $I++;
    }

    $AI_Pet = new BattlePet(0, $AI_Pet_ID);

    $_SESSION['PVE_AI_Pet_ID'] = $AI_Pet->Pet_ID;
    $_SESSION['PVE_AI_Pet_Name'] = $AI_Pet->Pet_Name;
    $_SESSION['PVE_AI_Pet_Image'] = $PET_IMAGE_PATH . $AI_Pet->Pet_Image;
    $_SESSION['PVE_AI_Pet_Offense'] = $AI_Pet->Pet_Offense;
    $_SESSION['PVE_AI_Pet_Defense'] = $AI_Pet->Pet_Defense;
    $_SESSION['PVE_AI_Pet_Current_Health'] = $AI_Pet->Pet_Current_Health;
    $_SESSION['PVE_AI_Pet_Max_Health'] = $AI_Pet->Pet_Max_Health;
    $_SESSION['PVE_AI_Pet_Current_AP'] = $AI_Pet->Pet_Current_AP;
    $_SESSION['PVE_AI_Pet_Max_AP'] = $AI_Pet->Pet_Max_AP;

    $_SESSION['PVE_AI_Pet_Skill_1'] = $this->Pet_Skill_1;
    $Pet_Skill = $this->Get_Pet_Skill_Array($this->Pet_Skill_1);
    $_SESSION['PVE_AI_Pet_Skill_1_Type'] = $Pet_Skill['Ability_Damage_Type'];

    $_SESSION['PVE_AI_Pet_Skill_2'] = $this->Pet_Skill_2;
    $Pet_Skill = $this->Get_Pet_Skill_Array($this->Pet_Skill_2);
    $_SESSION['PVE_AI_Pet_Skill_2_Type'] = $Pet_Skill['Ability_Damage_Type'];

    $_SESSION['PVE_AI_Pet_Skill_3'] = $this->Pet_Skill_3;
    $Pet_Skill = $this->Get_Pet_Skill_Array($this->Pet_Skill_3);
    $_SESSION['PVE_AI_Pet_Skill_3_Type'] = $Pet_Skill['Ability_Damage_Type'];

    $_SESSION['PVE_AI_Pet_Bonus_Offense'] = $AI_Pet->Pet_Bonus_Offense;
    $_SESSION['PVE_AI_Pet_Bonus_Defense'] = $AI_Pet->Pet_Bonus_Defense;
    $_SESSION['PVE_AI_Pet_Bonus_EXP'] = $AI_Pet->Pet_Bonus_EXP;
    $_SESSION['PVE_AI_Pet_Exp'] = $AI_Pet->Pet_Exp;
    $_SESSION['PVE_AI_Pet_Level'] = $AI_Pet->Pet_Level;
    $_SESSION['PVE_AI_Pet_Type'] = $AI_Pet->Pet_Type;
    $_SESSION['PVE_AI_Pet_Tier'] = $AI_Pet->Pet_Tier;

    $this->Remove_Wild_Pet($AI_Pet_ID);
}

public function Create_Battle_Room_PVP($Attacking_Pet_ID, $Defending_Pet_ID)
{

}

// This function gives a pet a set amount of exp, if it's more than the leveling threshold (100 exp) it will level the pet and apply the remaining exp.
public function Give_Exp($Pet_ID, $Exp)
{
    $i = 0;
    $Pet = new BattlePet($this->User_ID, $Pet_ID);
    $NewExp = $Pet->Pet_Exp + $Exp;

    if ($NewExp >= 100) {
        $LevelsGained = floor($NewExp / 100);

        while ($i < $LevelsGained) {
            $this->LevelUp_Pet($Pet_ID);
            $NewExp = $NewExp - 100;
            $i++;
        }
    }

    $Pet_Array = array();
    $Pet_Array[':Pet_ID']=$Pet_ID;
    $Pet_Array[':Pet_Exp']=$NewExp;

    $Pet_SQL = "UPDATE pets SET Pet_Exp=:Pet_Exp WHERE Pet_ID=:Pet_ID";
    $Results = $this->Connection->Custom_Execute($Pet_SQL, $Pet_Array);
}

// This functions takes a pet id and levels that pet up, increasing it's stats.
public function LevelUp_Pet($Pet_ID)
{
    $Pet = new BattlePet($this->User_ID, $Pet_ID);

    if ($Pet->Pet_Level < 30) {
        $RandomOffense = rand(1,3);
        $RandomMaxHealth = rand(3,8);
        $RandomDefense = rand(1,3);
        $RandomMaxAP = rand(1,2);

        $New_Level = $Pet->Pet_Level+1;
        $Pet_Array = array();
        $Pet_Array[':Pet_Offense']=$Pet->Pet_Offense + $RandomOffense;
        $Pet_Array[':Pet_Defense']=$Pet->Pet_Defense + $RandomDefense;
        $Pet_Array[':Pet_Max_Health']=$Pet->Pet_Max_Health + $RandomMaxHealth;
        $Pet_Array[':Pet_Current_Health']=$Pet->Pet_Max_Health + $RandomMaxHealth;
        $Pet_Array[':Pet_Max_AP']=$Pet->Pet_Max_AP + $RandomMaxAP;
        $Pet_Array[':Pet_Current_AP']=$Pet->Pet_Max_AP + $RandomMaxAP;
        $Pet_Array[':Pet_ID']=$Pet_ID;
        $Pet_Array[':Pet_Level']=$Pet->Pet_Level + 1;
        $Pet_Array[':Pet_Exp']=0;


        $Pet_SQL = "UPDATE pets SET Pet_Level=:Pet_Level, Pet_Exp=:Pet_Exp, Pet_Offense=:Pet_Offense, Pet_Defense=:Pet_Defense, Pet_Max_Health=:Pet_Max_Health, Pet_Current_Health=:Pet_Current_Health, Pet_Max_AP=:Pet_Max_AP, Pet_Current_AP=:Pet_Current_AP WHERE Pet_ID=:Pet_ID";
        $Results = $this->Connection->Custom_Execute($Pet_SQL, $Pet_Array);

        if ($Results){
            if ($this->User_ID != 0){
                Toasts::addNewToast("Your pet just leveled up!<br>[{$Pet->Pet_Name}]<br>{$Pet->Pet_Level} -> {$New_Level}<br>+ {$RandomOffense} Offense<br>+ {$RandomDefense} Defense<br>+ {$RandomMaxHealth} Health<br>+ {$RandomMaxAP} AP", 'petbattle');
            }
        }
    }
}



// Removed a Wild Pet from the DB.
public function Remove_Wild_Pet($Pet_ID)
{
    $Pet_Array[':Pet_ID'] = $Pet_ID;
    $Pet_SQL = "DELETE FROM pets WHERE Pet_ID=:Pet_ID";

    $Results = $this->Connection->Custom_Execute($Pet_SQL, $Pet_Array);
}

// Deletes a pet from the database.
public function Release_Pet($Pet_ID)
{
    $Pet_Array[':Pet_ID'] = $Pet_ID;
    $Pet_SQL = "DELETE FROM pets WHERE Pet_ID=:Pet_ID";

    $Results = $this->Connection->Custom_Execute($Pet_SQL, $Pet_Array);

    if ($Results) {
        Toasts::addNewToast("You just released a pet back into the wild.", 'petbattle');
    }
}

// Returns the current active pet information.
public function Get_Active_Pet()
{
    $Pet_Array = array(':User_ID' => $this->User_ID);
    $Pet_SQL = "SELECT * FROM pets WHERE Pet_Active = 1 AND User_ID = :User_ID";
    $Results = $this->Connection->Custom_Query($Pet_SQL, $Pet_Array);

    return $Results;
}
// This function will set a specific pet to Active and de-active any pet already active.
public function Set_Active_Pet($Pet_ID)
{
    $Old_Active_Pet = $this->Get_Active_Pet_ID();
    $this->Set_Inactive_Pet($Old_Active_Pet);

    $Pet_Array = array (':Pet_ID'=>$Pet_ID, ':User_ID'=>$this->User_ID);
    $Pet_SQL = "UPDATE pets SET Pet_Active = 1 WHERE Pet_ID = :Pet_ID AND User_ID = :User_ID";
    $Results = $this->Connection->Custom_Execute($Pet_SQL, $Pet_Array);

    if ($Results) {
        Toasts::addNewToast("You just set a new active pet!", 'petbattle');
        $this->Switch_Pet($Pet_ID);
    }
}

// Sets a specific pet as Inactive
private function Set_Inactive_Pet($Pet_ID)
{
    $Pet_Array = array (':Pet_ID'=>$Pet_ID);
    $Pet_SQL = "UPDATE pets SET Pet_Active = 0 WHERE Pet_ID = :Pet_ID";
    $Results = $this->Connection->Custom_Execute($Pet_SQL, $Pet_Array);
}

// Switches to a specific pet and re-sets up the object info.
private function Switch_Pet($Pet_ID)
{
    $this->Set_Pet_Info($Pet_ID);
}

// Returns the total amount of pets the current user has.
public function Get_Total_Pet_Count()
{
    $Pet_Array = array(':User_ID' => $this->User_ID);
    $Pet_SQL = "SELECT COUNT(*) FROM pets WHERE User_ID = :User_ID";
    $Results = $this->Connection->Custom_Count_Query($Pet_SQL, $Pet_Array);

    return $Results[0];
}

// Returns the total amount of alive pets the current user has.
public function Get_Total_Alive_Pet_Count()
{
    $Pet_Array = array(':User_ID' => $this->User_ID);
    $Pet_SQL = "SELECT COUNT(*) FROM pets WHERE Pet_Status='Alive' AND User_ID = :User_ID";
    $Results = $this->Connection->Custom_Count_Query($Pet_SQL, $Pet_Array);

    return $Results[0];
}

// Returns the total amount of dead pets the current user has.
public function Get_Total_Dead_Pet_Count()
{
    $Pet_Array = array(':User_ID' => $this->User_ID);
    $Pet_SQL = "SELECT COUNT(*) FROM pets WHERE Pet_Status='Dead' AND User_ID = :User_ID";
    $Results = $this->Connection->Custom_Count_Query($Pet_SQL, $Pet_Array);

    return $Results[0];
}


// =============== BATTLE FUNCTIONS
public function Get_Pet_Skill_Array($Skill_Name)
{
    $Pet_Array = array(':Ability_Name' => $Skill_Name);
    $Pet_SQL = "SELECT * FROM pet_abilitys WHERE Ability_Name = :Ability_Name";
    $Results = $this->Connection->Custom_Query($Pet_SQL, $Pet_Array);

    return $Results;
}


// This function will initiate a battle between the active pet and the PVE Wild Pet.
public function PVE_Attack($Skill_Name)
{
    // Here we make sure the user's pet is the right level.
    $Pet_Random_Ability = rand(0,1);
    if ($_SESSION['PVE_AI_Pet_Level'] >= 7) {
        $Pet_Random_Ability = rand(0,2);
    }
    if ($_SESSION['PVE_AI_Pet_Level'] >= 15) {
        $Pet_Random_Ability = rand(0,3);
    }
    switch ($Pet_Random_Ability) {
        case '0':
            $AI_Defend_Pet_Defense = $_SESSION['PVE_AI_Pet_Defense'];
        case '1':
            $AI_Pet_Ability = $this->Get_Pet_Skill_Array($_SESSION['PVE_AI_Pet_Skill_1']);
            break;
        case '2':
            $AI_Pet_Ability = $this->Get_Pet_Skill_Array($_SESSION['PVE_AI_Pet_Skill_2']);
            break;
        case '3':
            $AI_Pet_Ability = $this->Get_Pet_Skill_Array($_SESSION['PVE_AI_Pet_Skill_3']);
            break;
    }


    if ($Skill_Name!="Defend") {
        // This sets up the ability reference array for the User.
        $User_Pet_Ability = $this->Get_Pet_Skill_Array($Skill_Name);

        $User_Pet_Offense_Modifier = $this->Get_Elemental_Modifier($User_Pet_Ability['Ability_Damage_Type'], $_SESSION['PVE_AI_Pet_Type']);
        $User_Offense_Elemental = ceil(($_SESSION['PVE_User_Pet_Offense'] * $User_Pet_Offense_Modifier));

        $User_Offense = rand(1,$User_Pet_Ability['Ability_Damage']) + $_SESSION['PVE_User_Pet_Offense'] + $_SESSION['PVE_User_Pet_Bonus_Offense'] + $User_Offense_Elemental;
        $AI_Defense = ($_SESSION['PVE_AI_Pet_Defense'] + $_SESSION['PVE_AI_Pet_Bonus_Defense'] + $AI_Defend_Pet_Defense);
        $AI_Percent_Blocked = ($AI_Defense * .01);
        $AI_Damage_Blocked = ceil($User_Offense * $AI_Percent_Blocked);
        $User_Damage_Done = $User_Offense - $AI_Damage_Blocked;

        $_SESSION['PVE_AI_Pet_Current_Health'] = $_SESSION['PVE_AI_Pet_Current_Health'] - $User_Damage_Done;

        $Return_Array['UserAction'] = "YOU used ability " . $User_Pet_Ability['Ability_Name'] . " on enemy pet dealing [" . $User_Damage_Done . "] + [".$User_Offense_Elemental."] elemental damage. The enemy blocked [" . ceil($AI_Damage_Blocked) . "] damage with [" . $AI_Defense . "%] reduction.";
    }

    if ($Skill_Name=="Defend"){
        $User_Defend_Pet_Defense = $_SESSION['PVE_User_Pet_Defense'];
        $Return_Array['UserAction'] = "YOU used ability " . $Skill_Name . " and raised your defense to [" . $User_Defend_Pet_Defense * 2 . "]";
    }

    if (!isset($AI_Defend_Pet_Defense)) {
        // Here we check to make sure the enemy pet didn't die before having him attack back.
        if ($_SESSION['PVE_AI_Pet_Current_Health'] > 0) {

            $AI_Pet_Offense_Modifier = $this->Get_Elemental_Modifier($AI_Pet_Ability['Ability_Damage_Type'], $_SESSION['PVE_User_Pet_Type']);
            $AI_Offense_Elemental = ceil(($_SESSION['PVE_AI_Pet_Offense'] * $AI_Pet_Offense_Modifier));

            $AI_Offense = rand(1,$AI_Pet_Ability['Ability_Damage']) + $_SESSION['PVE_AI_Pet_Offense'] + $_SESSION['PVE_AI_Pet_Bonus_Offense'];
            $User_Defense = ($_SESSION['PVE_User_Pet_Defense'] + $_SESSION['PVE_User_Pet_Bonus_Defense']);
            $User_Percent_Blocked = (($User_Defense + $User_Defend_Pet_Defense) * .01);
            $User_Damage_Blocked = ceil($AI_Offense * $User_Percent_Blocked);
            $AI_Damage_Done = $AI_Offense - ceil($User_Damage_Blocked);

                $_SESSION['PVE_User_Pet_Current_Health'] = $_SESSION['PVE_User_Pet_Current_Health'] - $AI_Damage_Done;

               $Return_Array['AIAction'] = "ENEMY used ability " . $AI_Pet_Ability['Ability_Name'] . " on your pet dealing [" . $AI_Damage_Done ."] + [".$AI_Offense_Elemental."] elemental damage. You blocked [" . ceil($User_Damage_Blocked) . "] damage with [" . ($User_Defense + $User_Defend_Pet_Defense) . "%] reduction.";
        }
    }

    if (isset($AI_Defend_Pet_Defense)) {
        $Return_Array['AIAction'] = "ENEMY used ability Defend and raised it's defense to [" . ($AI_Defend_Pet_Defense * 2) . "]";
    }

    //Reduce the cooldowns on abilitys.
    if ($_SESSION['PVE_User_Pet_Skill_1_Cooldown'] > 0) { $_SESSION['PVE_User_Pet_Skill_1_Cooldown'] = $_SESSION['PVE_User_Pet_Skill_1_Cooldown'] - 1;}
    if ($_SESSION['PVE_User_Pet_Skill_2_Cooldown'] > 0) { $_SESSION['PVE_User_Pet_Skill_2_Cooldown'] = $_SESSION['PVE_User_Pet_Skill_2_Cooldown'] - 1;}
    if ($_SESSION['PVE_User_Pet_Skill_3_Cooldown'] > 0) { $_SESSION['PVE_User_Pet_Skill_3_Cooldown'] = $_SESSION['PVE_User_Pet_Skill_3_Cooldown'] - 1;}

    // Here we set cooldowns
    if ($Skill_Name == $_SESSION['PVE_User_Pet_Skill_1']) { $_SESSION['PVE_User_Pet_Skill_1_Cooldown'] = $User_Pet_Ability["Ability_Cooldown"];}
    if ($Skill_Name == $_SESSION['PVE_User_Pet_Skill_2']) { $_SESSION['PVE_User_Pet_Skill_2_Cooldown'] = $User_Pet_Ability["Ability_Cooldown"];}
    if ($Skill_Name == $_SESSION['PVE_User_Pet_Skill_3']) { $_SESSION['PVE_User_Pet_Skill_3_Cooldown'] = $User_Pet_Ability["Ability_Cooldown"];}


    // Here we check to see if any side has won the battle.
    if ($_SESSION['PVE_AI_Pet_Current_Health'] <= 0) {
        $this->PVE_Win_Battle();
    }

    if ($_SESSION['PVE_User_Pet_Current_Health'] <= 0) {
        $this->PVE_Lose_Battle();
    }

    return $Return_Array;

}

public function PVE_Win_Battle()
{
    $EXP_Earned = rand(1,30 - $this->Pet_Level);
    $this->Give_Exp($this->Pet_ID, $EXP_Earned);
    $this->Clear_Battle_Room_PVE();
    $this->Add_Battles_Won();
    Toasts::addNewToast("You just won a battle! +" . $EXP_Earned . " Exp", 'petbattle');
}

public function PVE_Lose_Battle()
{
    $this->Clear_Battle_Room_PVE();
    $this->Add_Battles_Lost();
    Toasts::addNewToast("You just lost a battle :(!", 'petbattle');
}

// This function will try to catch a Wild pet you're fighting.
public function PVE_Catch_Pet()
{
    $Chance_Range_Percent = ($_SESSION['PVE_AI_Pet_Current_Health'] / $_SESSION['PVE_AI_Pet_Max_Health']);
    $Chance = (100 * $Chance_Range_Percent);
    $Random = rand(1, $Chance);

    if ($Random <= 8) {
        // YOU CAUGHT IT!
        $this->Give_Caught_Pet();
        Toasts::addNewToast("You just caught [{$_SESSION['PVE_AI_Pet_Name']}]", 'petbattle');
        $this->Add_Pets_Caught();
        $this->Clear_Battle_Room_PVE();
    } else {
        // YOU MISSED! WTF!?
        Toasts::addNewToast("Pet [{$_SESSION['PVE_AI_Pet_Name']}] got away!", 'petbattle');
        $this->Clear_Battle_Room_PVE();
    }
}

public function PVE_Save_AP($NewAP)
{
    $Pet_Array = array (':Pet_ID'=>$this->Pet_ID, ':Pet_Current_AP'=>$NewAP);
    $Pet_SQL = "UPDATE pets SET Pet_Current_AP = :Pet_Current_AP WHERE Pet_ID = :Pet_ID";
    $Results = $this->Connection->Custom_Execute($Pet_SQL, $Pet_Array);

    $this->Pet_Current_AP = $NewAP;
}

public function Get_Elemental_Modifier($Attacking_Type, $Defending_Type)
{
    $Strong_Against_Modifier = .2;
    $Weak_Against_Modifier = -.2;
    $Normal_Modifier = 0;

    switch ($Attacking_Type) {
        case 'Normal':
            return $Normal_Modifier;
            break;
        case 'Water':
            switch ($Defending_Type) {
                case 'Fire':
                    return $Strong_Against_Modifier;
                    break;
                case 'Wind':
                    return $Normal_Modifier;
                    break;
                case 'Water':
                    return $Normal_Modifier;
                    break;
                case 'Earth':
                    return $Weak_Against_Modifier;
                    break;
                default:
                    return $Normal_Modifier;
                    break;
            }
            break;
        case 'Fire':
            switch ($Defending_Type) {
                case 'Fire':
                    return $Normal_Modifier;
                    break;
                case 'Wind':
                    return $Strong_Against_Modifier;
                    break;
                case 'Water':
                    return $Weak_Against_Modifier;
                    break;
                case 'Earth':
                    return $Normal_Modifier;
                    break;
                default:
                    return $Normal_Modifier;
                    break;
            }
            break;
        case 'Wind':
            switch ($Defending_Type) {
                case 'Fire':
                    return $Weak_Against_Modifier;
                    break;
                case 'Wind':
                    return $Normal_Modifier;
                    break;
                case 'Water':
                    return $Normal_Modifier;
                    break;
                case 'Earth':
                    return $Strong_Against_Modifier;
                    break;
                default:
                    return $Normal_Modifier;
                    break;
            }
            break;
        case 'Earth':
            switch ($Defending_Type) {
                case 'Fire':
                    return $Normal_Modifier;
                    break;
                case 'Wind':
                    return $Weak_Against_Modifier;
                    break;
                case 'Water':
                    return $Strong_Against_Modifier;
                    break;
                case 'Earth':
                    return $Normal_Modifier;
                    break;
                default:
                    return $Normal_Modifier;
                    break;
            }
            break;
        
        default:
            return $Normal_Modifier;
            break;
    }
}

} // END CLASS
