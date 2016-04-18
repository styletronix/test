<?
class Alarmzone extends IPSModule {
        public function __construct($InstanceID) {
            // Diese Zeile nicht löschen
            parent::__construct($InstanceID);
 
            // Selbsterstellter Code
        }
		
        public function Create() {
            parent::Create();

            $this->ConnectParent("{92FD9EAD-2EFC-4D14-BE82-482ED81E1104}");


            $this->RegisterPropertyInteger("AlertMode", 0);
            $this->RegisterPropertyInteger("AlertTarget", 0);
            $this->RegisterPropertyInteger("AlertDelay", 0);
            $this->RegisterPropertyInteger("ActiveDelay", 0);
            $this->RegisterPropertyString("GroupName", "Alarmzone");


			@$CategoryID = IPS_GetCategoryIDByName("Alarmzonen", $this->InstanceID);
			if ($CategoryID == false){
				$CategoryID = IPS_CreateCategory();
				IPS_SetName($CategoryID, "Alarmzonen");
				IPS_SetParent($CategoryID, $this->InstanceID);
			}

            $this->UpdateEvents();

			//$this->EnableAction("Ergebnis_Boolean");
        }
        public function ApplyChanges() {
            parent::ApplyChanges();

			$this->UpdateEvents();
			$this->SetStatus(102);
        }

		public function UpdateEvents(){
            $dataObject = array();
            $dataObject["AlarmAktiv"] = true;
            $dataObject["ZoneType"] = 0;
            $dataObject["Test1"] = 1234;


            $data = json_encode($dataObject);
            $resultat = $this->SendDataToParent(json_encode(Array("DataID" => "{486EAF6F-466F-4E07-9E89-5CC0FCB4161F}", "Buffer" => $data)));



			$AlarmzonenID = IPS_GetCategoryIDByName("Alarmzonen", $this->InstanceID);
		
			foreach(IPS_GetChildrenIDs($AlarmzonenID) as $AlarmzoneID) {
				$foundIDs = array();
				$ScriptID = $this->RegisterScript("Update", "Update", "<?\n\nSXGRP_RefreshZone(".$this->InstanceID.", ".$AlarmzoneID."); \n\n ?>");

				foreach(IPS_GetChildrenIDs($AlarmzoneID) as $key2) {
					$itemObject = IPS_GetObject($key2);
					$TargetID = 0;
					$TargetName = IPS_GetName($key2);

					if ($itemObject["ObjectType"] == 6){
					$TargetID = IPS_GetLink($key2)["TargetID"];
					}

					if ($TargetID > 0){
						$EventName = "TargetID ".$TargetID;
						$foundIDs[] = $EventName;

						@$EventID = IPS_GetEventIDByName($EventName, $ScriptID);
						if ($EventID === false){
							$EventID = IPS_CreateEvent(0);
							IPS_SetEventTrigger($EventID, 1, $TargetID);
							IPS_SetName($EventID, $EventName);
							IPS_SetParent($EventID, $ScriptID);
							IPS_SetEventActive($EventID, true);
						}
					}
				}

				foreach(IPS_GetChildrenIDs($ScriptID) as $key2) {
					$EventName = IPS_GetName($key2);
					if (!in_array ($EventName, $foundIDs)){
						IPS_DeleteEvent($key2);
					}
				}
			}
		}

        public function RefreshStatus() {
			$result = false;
			$CategoryID = IPS_GetCategoryIDByName("Devices", $this->InstanceID);

			foreach(IPS_GetChildrenIDs($CategoryID) as $key2) {
	         $itemObject = IPS_GetObject($key2);
	         $TargetID = 0;
	         $TargetName = IPS_GetName($key2);


				if ($itemObject["ObjectType"] == 6){
				   $TargetID = IPS_GetLink($key2)["TargetID"];
				}

			if ($TargetID > 0){
				$var = IPS_GetVariable ($TargetID);
				$t = $var["VariableType"];
				if ($t == 0){
				   $Meldung = GetValueBoolean($TargetID);
					if ($Meldung == true){
				      $result = true;
				   }
				}
				if ($t == 1){
				   $Meldung = GetValueInteger($TargetID);
					if ($Meldung > 0){
				      $result = true;
				   }
				}
				if ($t == 2){
				   $Meldung = GetValueFloat($TargetID);
					if ($Meldung > 0){
				      $result = true;
				   }
				}
			}
	   }

	   SetValue($this->GetIDForIdent("Ergebnis_Boolean"),	$result);
       }

		public function GetCurrentStateString(){
			$arr =array();
			$CategoryID = IPS_GetCategoryIDByName("Devices", $this->InstanceID);

			foreach(IPS_GetChildrenIDs($CategoryID) as $key2) {
				$itemObject = IPS_GetObject($key2);
				$TargetID = 0;
				$TargetName = IPS_GetName($key2);
			

				if ($itemObject["ObjectType"] == 6){
					$TargetID = IPS_GetLink($key2)["TargetID"];
				}

				if ($TargetID > 0){
					$var = IPS_GetVariable ($TargetID);
					$t = $var["VariableType"];
					if ($t == 0){
						$arr[$TargetID] = GetValueBoolean($TargetID);
					}
					if ($t == 1){
						$arr[$TargetID] = GetValueInteger($TargetID);
					}
					if ($t == 2){
						$arr[$TargetID] = GetValueFloat($TargetID);
					}
				}
			}

			return json_encode($arr);
		}
		

		public function SetCurrentStateString(string $State){
			$arr = json_decode($State, true);
			$CategoryID = IPS_GetCategoryIDByName("Devices", $this->InstanceID);

			foreach(IPS_GetChildrenIDs($CategoryID) as $key2) {
				$itemObject = IPS_GetObject($key2);
				$TargetID = 0;
				$TargetName = IPS_GetName($key2);


				if ($itemObject["ObjectType"] == 6){
					$TargetID = IPS_GetLink($key2)["TargetID"];
				}

				if ($TargetID > 0){
					$pID = IPS_GetParent($TargetID);
					$inst = IPS_GetInstance($pID);
					$istHM = ($inst["ModuleInfo"]["ModuleID"] == "{EE4A81C6-5C90-4DB7-AD2F-F6BBD521412E}");

					$value = $arr[$TargetID];
					$var = IPS_GetVariable ($TargetID);
					$t = $var["VariableType"];
					if ($t == 0){
						if ($istHM){
							HM_WriteValueBoolean($pID, IPS_GetName($TargetID), $value);
						}else{
							SetValueBoolean($TargetID, $value);
						}
					}
					if ($t == 1){
						if ($istHM){
							if ($value){
								HM_WriteValueInteger($pID, IPS_GetName($TargetID), 100);
							}else{
								HM_WriteValueInteger($pID, IPS_GetName($TargetID), 0);
							}

						}else{
							if ($value){
								SetValueInteger($TargetID, 100);
							}else{
								SetValueInteger($TargetID, 0);
							}
						}
					}
					if ($t == 2){
						if ($istHM){
							if ($value){
								HM_WriteValueFloat($pID, IPS_GetName($TargetID), 1.0);
							}else{
								HM_WriteValueFloat($pID, IPS_GetName($TargetID), 0.0);
							}

						}else{
							if ($value){
								SetValueFloat($TargetID, 1.0);
							}else{
								SetValueFloat($TargetID, 0.0);
							}
						}

					}
				}
			}
		}

		public function RequestAction($Ident, $Value) {
    		switch($Ident) {

        	default:
	            throw new Exception("Invalid Ident");

    		}
 		}

    }
?>