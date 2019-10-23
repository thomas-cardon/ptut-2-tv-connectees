<?php


class StudyDirectorModel extends UserModel {
    public function insertDirector($login, $pwd, $email,$code){
        $role = "directeuretude";
        return $this->insertUser($login, $pwd, $role, $email, $code);
    }

    public function modifyStudyDirector($result, $code){
        return $this->modifyUser($result->ID, $result->user_login, $code);
    }
}