<?
class Alarmanlage extends IPSModule {
        public function __construct($InstanceID) {
            // Diese Zeile nicht löschen
            parent::__construct($InstanceID);
 
            // Selbsterstellter Code
        }
		
        public function Create() {
            parent::Create();

			$this->RegisterVariableBoolean("AlertActive", "Alarm", "~Switch");
			$this->RegisterVariableBoolean("SilentAlertActive", "Stiller Alarm", "~Switch");
			$this->RegisterVariableBoolean("TechnicalAlertActive", "Technik Alarm", "~Switch");




			//$this->EnableAction("Ergebnis_Boolean");
        }
        public function ApplyChanges() {
            parent::ApplyChanges();

			$this->SetStatus(102);
        }

        public function UpdateEvents(){
                
        }


		public function RequestAction($Ident, $Value) {
    		switch($Ident) {

        	default:
	            throw new Exception("Invalid Ident");

    		}
 		}


        public function ForwardData($JSONString) {
            SetValue($this->GetIDForIdent("AlertActive"),	true);


            // Empfangene Daten von der Device Instanz
            $data = json_decode($JSONString);
            if ($data->DataID == "{486EAF6F-466F-4E07-9E89-5CC0FCB4161F}"){
                //Nachricht von Alarmzone
                IPS_LogMessage("ForwardData", utf8_decode($data->Buffer));
            }else{
                IPS_LogMessage("ForwardData", "unbekannte Nachricht");
            }


        

            // Hier würde man den Buffer im Normalfall verarbeiten
            // z.B. CRC prüfen, in Einzelteile zerlegen

            // Weiterleiten zur I/O Instanz
            // $resultat = $this->SendDataToParent(json_encode(Array("DataID" => "{79827379-F36E-4ADA-8A95-5F8D1DC92FA9}", "Buffer" => $data->Buffer)));

            // Weiterverarbeiten und durchreichen
            // return $resultat;


            return true;
        }

}
?>