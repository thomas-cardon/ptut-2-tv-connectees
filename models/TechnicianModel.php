<?php


class TechnicianModel extends UserModel {
    public function insertMyTechnician($login, $pwd, $email){
        $role = 'technicien';
        return $this->insertUser($login, $pwd, $role, $email);
    }
}