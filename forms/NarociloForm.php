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

abstract class NarociloAbstractForm extends HTML_QuickForm2 {

    public $status;

    public function __construct($status) {
        parent::__construct($status);
        
              
        $this->status = new HTML_QuickForm2_Element_InputText('stranka_aktivirana');
        $this->status->setAttribute('size', 1);                
       // $this->status->addRule('gte', 'Številka mora biti => 0.', 0);
       // $this->status->addRule('lte', 'Številka mora biti <=1.', 1);       
            
//        Onemogoci stranki urejanje s statusom profila
        if(isset($_SESSION["user_level"]) && $_SESSION["user_level"] == 0){
            $this->status->setLabel('Status naročila (0:potrjeno, 1:preklicano, 2:stornirano)');
            $this->status->addRule('required', 'Vpišite 0 ali 1 ali 2.');
            $this->status->addRule('regex', 'Samo 0 ali 1 ali 2!', '/^(0|1|2)$/');
            
//        Ce lahko ureja profil se vpiše vrednost iz baze, ob registraciji je 0
        }
        

        $this->addElement($this->status);
               
        $this->button = new HTML_QuickForm2_Element_InputSubmit(null);
        $this->addElement($this->button);

        $this->addRecursiveFilter('trim');
        $this->addRecursiveFilter('htmlspecialchars');
    }
}

class NarociloInsertForm extends NarociloAbstractForm {
    public function __construct($status) {
        parent::__construct($status);

        $this->button->setAttribute('value', 'Potrdi');
    }

}

class NarociloEditForm extends NarociloAbstractForm {
    public $status;

    public function __construct($status) {
        parent::__construct($status);

        $this->button->setAttribute('value', 'Uredi artikel');
        $this->status = new HTML_QuickForm2_Element_InputHidden("status");
        $this->addElement($this->status);
    }

