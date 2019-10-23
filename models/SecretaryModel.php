<?php
/**
 * Created by PhpStorm.
 * UserView: Rohrb
 * Date: 25/04/2019
 * Time: 10:26
 */

class SecretaryModel extends UserModel {

    public function insertMySecretary($login, $pwd, $email){
        $role = "secretaire";
        return $this->insertUser($login, $pwd, $role, $email);
    }

}