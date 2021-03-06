<?php

require_once("ViewHelper.php");
require_once("model/UserDB.php");
require_once("forms/UsersForm.php");
require_once("forms/LoginForm.php");
require_once("model/EmployeeDB.php");


class UserController {
    
    public static function index() {
        echo ViewHelper::render("view/vse-stranke.php", [
           "stranke" => UserDB::getAll() 
        ]);
        #ViewHelper::redirect(BASE_URL . "artikli");
    }
    
    public static function captcha() {
        echo ViewHelper::render("view/captcha.php", []);
    }

    public static function add() {             
        
        $form = new UsersInsertForm("add_form");

        if ($form->validate()) {
            $data = $form->getValue();

            if(!UserDB::exists($data)){
                UserDB::insert($data);
                if (isset($_SESSION["user_level"]) && $_SESSION["user_level"] == 0) {
                    EmployeeDB::dnevnik(["timestamp" => date('Y-m-d H:i:s', time()),
                        "dnevnik_id_aktivnosti" => 4,
                        "dnevnik_id_zaposlenca" => $_SESSION["user_id"]
                        ]);
                    echo ViewHelper::render("view/vse-stranke.php", ["stranke" => UserDB::getAll()]);
                } else{
                    echo ViewHelper::render("view/potrditevReg.php", [
                    "response" => "Registracija uspešna! Za aktivacijo sledite povezavi, ki ste jo prejeli na vpisani elektronski naslov.",
                    "title" => "Potrditev registracije"
                    ]);
                }               
            } else {
                echo ViewHelper::render("view/user-form.php", [
                "title" => "Elektronski naslov je že v uporabi!",
                "form" => $form
            ]);

            }

        } else {
            echo ViewHelper::render("view/user-form.php", [
                "title" => "Registracija stranke",
                "form" => $form
            ]);
        }
    }
    
    public static function get($id) {
        echo ViewHelper::render("view/stranka-podrobnosti.php", UserDB::get(["id_stranke" => $id]));
    }
    
    
    public static function edit() {
        
        $editForm = new UsersEditForm("edit_form");

        if ($editForm->isSubmitted()) {
            if ($editForm->validate()) {
                $data = $editForm->getValue();
                
                if((isset($_SESSION["user_id"]) && $_SESSION["user_id"] == $data["id_stranke"]) || (isset($_SESSION["user_level"]) && $_SESSION["user_level"] == 0 )){
                    UserDB::update($data);
                    if (isset($_SESSION["user_level"]) && $_SESSION["user_level"] == 0 ) {
                        EmployeeDB::dnevnik(["timestamp" => date('Y-m-d H:i:s', time()),
                        "dnevnik_id_aktivnosti" => 6,
                        "dnevnik_id_zaposlenca" => $_SESSION["user_id"]
                        ]);
                    }                    
                    
                    ViewHelper::redirect(BASE_URL . "stranke/" . $data["id_stranke"]);
                } else {
                    echo ViewHelper::render("view/user-form.php", [
                    "title" => "Urejanje profila stranke",
                    "form" => $editForm
                    ]);
                }
            } else {
                echo ViewHelper::render("view/user-form.php", [
                    "title" => "Urejanje profila stranke",
                    "form" => $editForm
                ]);
            }
        }
    }
    
    public static function editForm($params) {
        $stranka = UserDB::get(["id_stranke" => $params]);
        #izbriše vrednost hashanega gesla, da se ta ne doda v form
        unset($stranka["geslo_stranke"]);
        $editForm = new UsersEditForm("edit_form");
        
        $dataSource = new HTML_QuickForm2_DataSource_Array($stranka);
        
        $editForm->addDataSource($dataSource);
        
        echo ViewHelper::render("view/user-form.php", [
                    "title" => "Urejanje profila stranke",
                    "form" => $editForm
  
        ]);
    }
    
    public static function login() {
        $form = new LoginInsertForm("add_form");

        if ($form->validate()) {
            $data = $form->getValue();
            UserDB::login($data);
            if(isset($_SESSION['user_id'])){                
                ViewHelper::redirect(BASE_URL . "artikli");
            } else {
                echo ViewHelper::render("view/login-form.php", [
                "title" => "Prijava stranke",
                "form" => $form
                ]);            
            }   
                      
        } else {

            echo ViewHelper::render("view/login-form.php", [
                    "title" => "Prijava stranke",
                    "form" => $form
            ]);
        }

    }
    
    public static function activate(){
        
        echo ViewHelper::render("view/potrditevReg.php", [
            "response" => userDB::activate(),
            "title" => "Potrditev registracije"
            ]);
    }

    public static function logout(){
        
        if (isset($_SESSION["user_level"])) {
            EmployeeDB::dnevnik(["timestamp" => date('Y-m-d H:i:s', time()),
                        "dnevnik_id_aktivnosti" => 2,
                        "dnevnik_id_zaposlenca" => $_SESSION["user_id"]
                        ]);
        }
        
        session_start();
        setcookie(session_name(), '', 100);
        session_unset();
        session_destroy();
        $_SESSION = array();
        ViewHelper::redirect(BASE_URL . "artikli");
    }

    public static function izpisKosara() {
            echo ViewHelper::render("view/nakupovalna.php", ["title" => "Nakupovalna košarica"]);
    }

    
    
    //za izpis vseh naročil stranke
    public static function izpisNarocil() {
        echo ViewHelper::render("view/pregled-narocila-stranka.php" , ["title" => "Pregled naročil", "narocila" => NarociloDB::getAllIzpisNarocil() ]);
    }
    
    public static function izpisPodrobnostiNarocila($id) {
        echo ViewHelper::render("view/opis-narocila.php", ["title" => "Pregled naročila" , "narocilo" => NarociloDB::getNarocilo($id) ]);
    }
    
    //za dodajanje podatkov o stranki, ki se odloči za nakup izdelkov
    //podatki se prikažejo po kliku oddaj naročilo v košarici
    public static function getStrankaInfo() {
        echo ViewHelper::render("view/oddaja-narocila.php", ["stranka" => UserDB::getStranka($_SESSION["user_id"])]);
    }
    
    public static function urediNarocila() {
        echo ViewHelper::render("view/aktivacija-narocila.php", ["title" => "Uredi naročila", "narocila" => NarociloDB::gelAllIzpisNarocil()]);
    }




    /**
     * Returns an array of filtering rules for manipulation books
     * @return type
    
    public static function getRules() {
        return [
            'ime_artikla' => FILTER_SANITIZE_SPECIAL_CHARS,
            'cena' => FILTER_SANITIZE_SPECIAL_CHARS,
            'opis_artikla' => FILTER_SANITIZE_SPECIAL_CHARS,
            'artikel_aktiviran' => FILTER_SANITIZE_SPECIAL_CHARS
        ];
    }
    
    
     * Returns TRUE if given $input array contains no FALSE values
     * @param type $input
     * @return type
    
    public static function checkValues($input) {
        if (empty($input)) {
            return FALSE;
        }

        $result = TRUE;
        foreach ($input as $value) {
            $result = $result && $value != false;
        }

        return $result;
    }
    
     */
}
