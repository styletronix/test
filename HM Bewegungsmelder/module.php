<?
    // Klassendefinition
    class HM_Taster_mit_Bewegungsmelder extends IPSModule {
 
        // Der Konstruktor des Moduls
        // �berschreibt den Standard Kontruktor von IPS
        public function __construct($InstanceID) {
            // Diese Zeile nicht l�schen
            parent::__construct($InstanceID);
 
            // Selbsterstellter Code
        }
 
        // �berschreibt die interne IPS_Create($id) Funktion
        public function Create() {
            // Diese Zeile nicht l�schen.
            parent::Create();

			$this->RegisterPropertyInteger("ON_TIME", 60);
			$this->RegisterVariableInteger("Test", "Test");
 
        }
 
        // �berschreibt die intere IPS_ApplyChanges($id) Funktion
        public function ApplyChanges() {
            // Diese Zeile nicht l�schen
            parent::ApplyChanges();
        }
 
        /**
        * Die folgenden Funktionen stehen automatisch zur Verf�gung, wenn das Modul �ber die "Module Control" eingef�gt wurden.
        * Die Funktionen werden, mit dem selbst eingerichteten Prefix, in PHP und JSON-RPC wiefolgt zur Verf�gung gestellt:
        *
        * SX_MeineErsteEigeneFunktion($id);
        *
        */
        public function MeineErsteEigeneFunktion() {
            // Selbsterstellter Code
        }
    }
?>