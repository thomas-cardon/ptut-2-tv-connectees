<?php


class StudyDirectorModel extends UserModel
{
    /**
     * Ajoute un directeur d'études
     * @param $login    string login
     * @param $pwd      string mot de passe
     * @param $email    string email
     * @param $code     int code ADE
     * @return bool
     */
    public function insertDirector($login, $pwd, $email, $code)
    {
        $role = "directeuretude";
        return $this->insertUser($login, $pwd, $role, $email, $code);
    }

    /**
     * Modifie le code du directeur d'études
     * @param $result   WP_User
     * @param $code     int code ADE
     * @return bool
     */
    public function modifyStudyDirector($result, $code)
    {
        return $this->modifyUser($result->ID, $result->user_login, $code);
    }
}