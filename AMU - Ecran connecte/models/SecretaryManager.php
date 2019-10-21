<?php
/**
 * Created by PhpStorm.
 * User: Rohrb
 * Date: 25/04/2019
 * Time: 10:26
 */

class SecretaryManager extends Model{
    public function insertMySecretary($login, $pwd, $email){
        $role = "secretaire";
        return $this->insertUser($login, $pwd, $role, $email);
    }
}