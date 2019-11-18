<?php
/**
 * Created by PhpStorm.
 * UserView: Rohrb
 * Date: 25/04/2019
 * Time: 10:13
 */

class TeacherModel extends UserModel
{
    /**
     * Ajoute un enseignant à la base de données
     * @param $login    string login
     * @param $pwd      string mot de passe
     * @param $email    string email
     * @param $code     int code
     * @return bool
     */
    public function insertTeacher($login, $pwd, $email, $code)
    {
        $role = "enseignant";
        return $this->insertUser($login, $pwd, $role, $email, $code);
    }

    /**
     * Modifie le code d'un enseignant
     * @param $result   WP_user l'enseignant
     * @param $code     int code
     * @return bool
     */
    public function modifyTeacher($result, $code)
    {
        return $this->modifyUser($result->ID, $result->user_login, $code);
    }
}