<?php
/**
 * Created by PhpStorm.
 * UserView: Rohrb
 * Date: 25/04/2019
 * Time: 10:02
 */

class StudentManager extends Model{

    public function insertStudent($login, $pwd, $email){
        $role = "etudiant";
        return $this->insertUser($login, $pwd, $role, $email);
    }

    public function modifyStudent($id, $code){
        $result = $this->getById($id);
        return $this->modifyUser($id, $result[0]['user_login'], $code);
    }
}