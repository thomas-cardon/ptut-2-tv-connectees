<?php


class TechnicianModel extends UserModel
{

    /**
     * Ajoute un technicien
     * @param $login    string login
     * @param $pwd      string mot de passe
     * @param $email    string email
     * @return bool
     */
    public function insertMyTechnician($login, $pwd, $email)
    {
        $role = 'technicien';
        return $this->insertUser($login, $pwd, $role, $email);
    }
}