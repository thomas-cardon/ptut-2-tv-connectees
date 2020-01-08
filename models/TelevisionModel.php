<?php
/**
 * Created by PhpStorm.
 * UserView: Rohrb
 * Date: 25/04/2019
 * Time: 10:29
 */

class TelevisionModel extends UserModel
{

    /**
     * Ajoute un utilisateur "television" dans la base de données
     * @param $login    string login
     * @param $pwd      string mot de passe
     * @param $code     array Codes ADE
     * @return bool
     */
    public function insertMyTelevision($login, $pwd, $code)
    {
        $role = "television";
        return $this->insertUser($login, $pwd, $role, $login, $code);

    }

    /**
     * Modifie les codes ADE et le mot de passe de la télévision
     * @param $result       WP_User
     * @param $codes        array Codes ADE
     * @return bool
     */
    public function modifyTv($result, $codes)
    {
        return $this->modifyUser($result->ID, $result->user_login, $codes);
    }
}