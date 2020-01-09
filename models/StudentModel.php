<?php
/**
 * Created by PhpStorm.
 * UserView: Rohrb
 * Date: 25/04/2019
 * Time: 10:02
 */

class StudentModel extends UserModel
{

    /**
     * Ajoute un étudiant dans la base de données
     * @param $login    string login
     * @param $pwd      string mot de passe
     * @param $email    string email
     * @return bool
     */
    public function insertStudent($login, $pwd, $email) {
        $role = "etudiant";
        return $this->insertUser($login, $pwd, $role, $email);
    }

    /**
     * Modifie le code de l'étudiant
     * @param $id       int id
     * @param $code     int code
     * @return bool
     */
    public function modifyStudent($id, $code)
    {
        $result = $this->getById($id);
        return $this->modifyUser($id, $result[0]['user_login'], $code);
    }
}