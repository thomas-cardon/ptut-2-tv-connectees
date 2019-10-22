<?php
/**
 * Created by PhpStorm.
 * UserView: Rohrb
 * Date: 25/04/2019
 * Time: 10:29
 */

class TelevisionManager extends Model
{
    public function insertMyTelevision($login, $pwd, $code){
        $role = "television";
        return $this->insertUser($login, $pwd, $role, $login, $code);

    }

    public function modifyTv($result, $codes){
        return $this->modifyUser($result->ID, $result->user_login, $codes);
    }
}