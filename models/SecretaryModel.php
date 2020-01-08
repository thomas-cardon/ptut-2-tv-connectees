<?php
/**
 * Created by PhpStorm.
 * UserView: Rohrb
 * Date: 25/04/2019
 * Time: 10:26
 */

class SecretaryModel extends UserModel
{

    /**
     * Ajoute un secrétaire dans la base de données
     * @param $login    string login
     * @param $pwd      string mot de passe
     * @param $email    string email
     * @return bool
     */
    public function insertMySecretary($login, $pwd, $email)
    {
        $role = "secretaire";
        return $this->insertUser($login, $pwd, $role, $email);
    }

}