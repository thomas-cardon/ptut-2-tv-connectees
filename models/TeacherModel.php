<?php
/**
 * Created by PhpStorm.
 * UserView: Rohrb
 * Date: 25/04/2019
 * Time: 10:13
 */

class TeacherModel extends UserModel {
    public function insertTeacher($login, $pwd, $email,$code){
        $role = "enseignant";
        return $this->insertUser($login, $pwd, $role, $email, $code);
    }

    public function modifyTeacher($result, $code){
        return $this->modifyUser($result->ID, $result->user_login, $code);
    }
}