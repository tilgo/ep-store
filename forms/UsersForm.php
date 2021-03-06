<?php

require_once 'HTML/QuickForm2.php';
require_once 'HTML/QuickForm2/Container/Fieldset.php';
require_once 'HTML/QuickForm2/Element/InputSubmit.php';
require_once 'HTML/QuickForm2/Element/InputText.php';
require_once 'HTML/QuickForm2/Element/Textarea.php';
require_once 'HTML/QuickForm2/Element/Select.php';
require_once 'HTML/QuickForm2/Element/InputCheckbox.php';
require_once 'HTML/QuickForm2/Element/InputPassword.php';

require_once 'model/PostaDB.php';

abstract class UsersAbstractForm extends HTML_QuickForm2 {

    public $email_stranke;
    public $ime_stranke;
    public $priimek_stranke;
    public $geslo_stranke;
    public $geslo_stranke2;
    public $naslov_stevilka;
    public $id_poste;
    public $stranka_aktivirana;
    public $tel_st;
    public $button;

    public function __construct($id_stranke) {
        parent::__construct($id_stranke);
        
        $this->ime_stranke = new HTML_QuickForm2_Element_InputText('ime_stranke');
        $this->ime_stranke->setAttribute('size', 45);
        $this->ime_stranke->addRule('maxlength', 'Ime je predolgo (do 45 znakov).', 45);
        $this->ime_stranke->setLabel('Ime');
        $this->ime_stranke->addRule('required', 'Vpišite ime.');
        $this->ime_stranke->addRule('regex', 'Samo črke.', '/^[a-zA-ZščćžŠČĆŽ ]+$/');
        $this->addElement($this->ime_stranke);
        
        $this->priimek_stranke = new HTML_QuickForm2_Element_InputText('priimek_stranke');
        $this->priimek_stranke->setAttribute('size', 45);
        $this->priimek_stranke->addRule('maxlength', 'Priimek je predolg (do 45 znakov).', 45);
        $this->priimek_stranke->setLabel('Priimek');
        $this->priimek_stranke->addRule('required', 'Vpišite priimek.');
        $this->priimek_stranke->addRule('regex', 'Samo črke.', '/^[a-zA-ZščćžŠČĆŽ ]+$/');
        $this->addElement($this->priimek_stranke);
        
        $this->naslov_stevilka = new HTML_QuickForm2_Element_InputText('naslov_stevilka');
        $this->naslov_stevilka->setAttribute('size', 45);
        $this->naslov_stevilka->addRule('maxlength', 'Naslov je predolg (do 45 znakov).', 45);
        $this->naslov_stevilka->setLabel('Ulica in hišna številka:');
        $this->naslov_stevilka->addRule('required', 'Vpišite ulico in hišno številko.');
        $this->naslov_stevilka->addRule('regex', 'Uporabiti smete le črke, številke in presledek.', '/^[a-zA-ZščćžŠČĆŽ 0-9]+$/');
        $this->addElement($this->naslov_stevilka);
        
        $this->tel_st = new HTML_QuickForm2_Element_InputText('tel_st');
        $this->tel_st->setAttribute('size', 10);
        $this->tel_st->setLabel('Telefonska številka:');
        $this->tel_st->addRule('minlength', 'Popravite telefonsko številko (9 številk).', 9);
        $this->tel_st->addRule('maxlength', 'Popravite telefonsko številko (9 številk).', 9);
        $this->tel_st->addRule('required', 'Vpišite telefonsko številko.');
        $this->tel_st->addRule('regex', 'Uporabiti smete le številke (brez presledkov).', '/^[0-9]+$/');
        $this->addElement($this->tel_st);
        
        $this->id_poste = new HTML_QuickForm2_Element_Select('id_poste');
        #$this->id_poste->setAttribute('size', 4);
        $this->id_poste->setLabel('Izberite pošto:');
        $this->id_poste->loadOptions(PostaDB::sifrant());
        $this->id_poste->addRule('required', 'Izberite pošto.');
        $this->addElement($this->id_poste);
        
        $this->email_stranke = new HTML_QuickForm2_Element_InputText('email_stranke');
        $this->email_stranke->setAttribute('size', 45);
        $this->email_stranke->setLabel('Elektronski naslov:');
        $this->email_stranke->addRule('maxlength', 'Email je predolg (do 45 znakov).', 45);
        $this->email_stranke->addRule('required', 'Vpišite email.');
        $this->email_stranke->addRule('callback', 'Vnesite veljaven elektronski naslov.', array(
            'callback' => 'filter_var',
            'arguments' => [FILTER_VALIDATE_EMAIL])
        );
        $this->addElement($this->email_stranke);
        
        $this->geslo_stranke = new HTML_QuickForm2_Element_InputPassword('geslo_stranke');
        $this->geslo_stranke->setAttribute('size', 15);
        $this->geslo_stranke->setLabel('Vnesite geslo:');
        $this->geslo_stranke->addRule('maxlength', 'Geslo je predolgo (do 45 znakov).', 45);
        $this->geslo_stranke->addRule('required', 'Vnesite geslo.');
        $this->geslo_stranke->addRule('minlength', 'Geslo naj vsebuje vsaj 6 znakov.', 6);
        $this->geslo_stranke->addRule('regex', 'V geslu uporabite vsaj 1 številko.', '/[0-9]+/');
        $this->geslo_stranke->addRule('regex', 'V geslu uporabite vsaj 1 veliko črko.', '/[A-Z]+/');
        $this->geslo_stranke->addRule('regex', 'V geslu uporabite vsaj 1 malo črko.', '/[a-z]+/');
        $this->addElement($this->geslo_stranke);
        
        $this->geslo_stranke2 = new HTML_QuickForm2_Element_InputPassword('geslo_stranke2');
        $this->geslo_stranke2->setLabel('Ponovite geslo:');
        $this->geslo_stranke2->setAttribute('size', 15);
        $this->geslo_stranke2->addRule('required', 'Ponovno vpišite izbrano geslo.');
        $this->geslo_stranke2->addRule('eq', 'Gesli nista enaki.', $this->geslo_stranke);
        $this->addElement($this->geslo_stranke2);
              
        $this->stranka_aktivirana = new HTML_QuickForm2_Element_InputText('stranka_aktivirana');
        $this->stranka_aktivirana->setAttribute('size', 1);                
        $this->stranka_aktivirana->addRule('gte', 'Številka mora biti => 0.', 0);
        $this->stranka_aktivirana->addRule('lte', 'Številka mora biti <=1.', 1);       
            
//        Onemogoci stranki urejanje s statusom profila
        if(isset($_SESSION["user_level"]) && $_SESSION["user_level"] == 0){
            $this->stranka_aktivirana->setLabel('Status profila (1:aktiviran  0:neaktiviran)');
            $this->stranka_aktivirana->addRule('required', 'Vpišite 0 ali 1.');
            $this->stranka_aktivirana->addRule('regex', 'Samo 0 ali 1!', '/^(0|1)$/');
            
//        Ce lahko ureja profil se vpiše vrednost iz baze, ob registraciji je 0
        } else {                          
            $this->stranka_aktivirana->setAttribute('hidden');
            $this->stranka_aktivirana->setAttribute('value', "0");
            if(isset($_SESSION["user_id"])){
                $this->stranka_aktivirana->addRule('regex', 'Samo 0 ali 1!', '/^(0|1)$/');
            } else {
                $this->stranka_aktivirana->addRule('regex', 'Samo 0 je veljavna vrednost.', '/^0$/');
            }
                        
        }
        

        $this->addElement($this->stranka_aktivirana);
               
        $this->button = new HTML_QuickForm2_Element_InputSubmit(null);
        $this->addElement($this->button);

        $this->addRecursiveFilter('trim');
        $this->addRecursiveFilter('htmlspecialchars');
    }
}

class UsersInsertForm extends UsersAbstractForm {
    public function __construct($id_stranke) {
        parent::__construct($id_stranke);

        $this->button->setAttribute('value', 'Registriraj se');
    }

}

class UsersEditForm extends UsersAbstractForm {
    public $id_stranke;

    public function __construct($id_stranke) {
        parent::__construct($id_stranke);

        $this->button->setAttribute('value', 'Uredi profil');
        $this->id_stranke = new HTML_QuickForm2_Element_InputHidden("id_stranke");
        $this->addElement($this->id_stranke);
    }
}

