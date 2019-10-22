<?php
/**
 * Created by PhpStorm.
 * UserView: Rohrb
 * Date: 25/03/2019
 * Time: 18:10
 */

abstract class Model
{
    private static $db;

    /**
     * Set the db with PDO
     */
    private static function setDb(){
        self::$db = new PDO('mysql:host='.DB_HOST.'; dbname='.DB_NAME, DB_USER, DB_PASSWORD);
        self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    }

    /**
     * Return the db
     * @return mixed
     */
    protected function getDb(){
        if (self:: $db == null)
            self::setDb();
        return self::$db;
    }

    protected function getAll($table){
        $var = [];
        $req = $this->getDb()->prepare('SELECT * FROM ' . $table . ' ORDER BY ID desc');
        $req->execute();
        while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
            $var[] = $data;
        }
        return $var;
        $req->closeCursor();
    }

    protected function verifyTuple($login){
        $var = 0;
        $req = $this->getDb()->prepare('SELECT * FROM wp_users WHERE user_login =:login');
        $req->bindValue(':login', $login);
        $req->execute();
        while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
            $var = $var + 1;
        }
        if ($var > 0) {
            return true;
        } else {
            return false;
        }
        $req->closeCursor();
    }

    public function verifyMail($mail) {
        $var = 0;
        $req = $this->getDb()->prepare('SELECT * FROM wp_users WHERE user_email =:mail');
        $req->bindValue(':mail', $mail);
        $req->execute();
        while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
            $var = $var + 1;
        }
        if ($var > 0) {
            return false;
        } else {
            return true;
        }
        $req->closeCursor();
    }

    public function verifyLogin($login) {
        $var = 0;
        $req = $this->getDb()->prepare('SELECT * FROM wp_users WHERE user_login =:login');
        $req->bindValue(':login', $login);
        $req->execute();
        while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
            $var = $var + 1;
        }
        if ($var > 0) {
            return false;
        } else {
            return true;
        }
        $req->closeCursor();
    }

    protected function verifyNoDouble($email, $login){
        $var = 0;
        $req = $this->getDb()->prepare('SELECT * FROM wp_users WHERE user_email =:mail OR user_login =:login');
        $req->bindValue(':mail', $email);
        $req->bindValue(':login', $login);
        $req->execute();
        while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
            $var = $var + 1;
        }
        if ($var > 0) {
            return false;
        } else {
            return true;
        }
        $req->closeCursor();
    }

    protected function insertUser($login, $pwd, $role, $email, $code = array()){
        if ($this->verifyNoDouble($email, $login)){
            $req = $this->getDb()->prepare('INSERT INTO wp_users (user_login, user_pass, code,
                                      user_nicename, user_email, user_url, user_registered, user_activation_key,
                                      user_status, display_name) 
                                         VALUES (:login, :pwd, :code, :name, :email, :url, NOW(), :key, :status, :displayname)');

            $nul = " ";
            $zero = "0";

            $serCode = serialize($code);
            $req->bindParam(':login', $login);
            $req->bindParam(':pwd', $pwd);
            $req->bindParam(':code', $serCode);
            $req->bindParam(':name', $login);
            $req->bindParam(':email', $email);
            $req->bindParam(':url', $nul);
            $req->bindParam(':key', $nul);
            $req->bindParam(':status', $zero);
            $req->bindParam(':displayname', $login);

            $req->execute();

            $capa = 'wp_capabilities';
            $size = strlen($role);
            $role = 'a:1:{s:'.$size.':"'.$role.'";b:1;}';

            $id = $this->getDb()->lastInsertId();

            $req = $this->getDb()->prepare('INSERT INTO wp_usermeta(user_id, meta_key, meta_value) VALUES (:id, :capabilities, :role)');

            $req->bindParam(':id', $id);
            $req->bindParam(':capabilities', $capa);
            $req->bindParam(':role', $role);

            $req->execute();

            $level = "wp_user_level";

            $req = $this->getDb()->prepare('INSERT INTO wp_usermeta(user_id, meta_key, meta_value) VALUES (:id, :level, :value)');

            $req->bindParam(':id', $id);
            $req->bindParam(':level', $level);
            $req->bindParam(':value', $zero);

            $req->execute();

            return true;
        }
        else{
            return false;
        }
    }

    protected function modifyUser($id, $login, $codes){
        if ($this->verifyTuple($login)) {
            $req = $this->getDb()->prepare('UPDATE wp_users SET code = :codes
                                            WHERE ID=:id');
            $serCode = serialize($codes);
            $req->bindParam(':id', $id);
            $req->bindParam(':codes', $serCode);

            $req->execute();

            return true;
        }
        else {
            return false;
        }
    }

    public function getUsersByRole($role){
        $req = $this->getDb()->prepare('SELECT * FROM wp_users user, wp_usermeta meta WHERE user.ID = meta.user_id AND meta.meta_value =:role 
                                        ORDER BY user.code, user.user_login');
        $size = strlen($role);
        $role = 'a:1:{s:'.$size.':"'.$role.'";b:1;}';
        $req->bindParam(':role', $role);
        $req->execute();
        while ($data = $req->fetch()) {
            $var[] = $data;
        }
        return $var;
        $req->closeCursor();
    }

    public function getUserByLogin($login){
        $req = $this->getDb()->prepare('SELECT * FROM wp_users WHERE user_login = :login');
        $req->bindParam(':login', $login);
        $req->execute();
        while ($data = $req->fetch()) {
            $var[] = $data;
        }
        return $var;
        $req->closeCursor();
    }

    public function getTitleOfCode($code){
        $req = $this->getDb()->prepare('SELECT title FROM code_ade WHERE code = :code');
        $req->bindParam(':code', $code);
        $req->execute();
        while ($data = $req->fetch()) {
            $var[] = $data;
        }
        return $var;
        $req->closeCursor();
    }

    public function getCodeYear(){
        $req = $this->getDb()->prepare('SELECT * FROM code_ade WHERE type = "Annee" ORDER BY title');
        $req->execute();
        while ($data = $req->fetch()) {
            $var[] = $data;
        }
        return $var;
        $req->closeCursor();
    }

    public function getCodeGroup(){
        $req = $this->getDb()->prepare('SELECT * FROM code_ade WHERE type = "Groupe" ORDER BY title');
        $req->execute();
        while ($data = $req->fetch()) {
            $var[] = $data;
        }
        return $var;
        $req->closeCursor();
    }

    /**
     * Renvoie tous les codes de demi-groupe
     * @return array
     */
    public function getCodeHalfgroup(){
        $req = $this->getDb()->prepare('SELECT * FROM code_ade WHERE type = "Demi-Groupe" ORDER BY title');
        $req->execute();
        while ($data = $req->fetch()) {
            $var[] = $data;
        }
        return $var;
        $req->closeCursor();
    }

    /**
     * Envoie le titre du code ADE
     * @param $code     Code ADE
     * @return mixed    Renvoie le titre si le code est enregistré, sinon on renvoie le code
     */
    public function getTitle($code){
        $var = $this->getTitleOfCode($code);
        if(! isset($var[0]['title']))  $var[0]['title'] = $code;
        return $var[0]['title'];
    }

    /**
     * Supprime une ligne d'une table de données
     * @param $table    Table de données
     * @param $id       ID de la ligne à supprimer
     */
    protected function deleteTuple($table, $id){

        $req = $this->getDb()->prepare('DELETE FROM '.$table.' WHERE ID = :id');
        $req->bindValue(':id', $id);

        $req->execute();
    }

    /**
     * Supprime un utilisateur
     * @param $id   ID de l'utilisateur
     */
    public function deleteUser($id){
        $this->deleteTuple('wp_users', $id);
        $req = $this->getDb()->prepare('DELETE FROM wp_usermeta WHERE user_id = :id');
        $req->bindValue(':id', $id);

        $req->execute();
    }

    /**
     * Renvoie un utilisateur grâce à son ID
     * @param $id       ID de l'utilisateur souhaité
     * @return mixed    Renvoie les données concernant l'utilisateur
     */
    public function getById($id){
        $req = $this->getDb()->prepare('SELECT * FROM wp_users user, wp_usermeta meta WHERE user.ID = meta.user_id AND user.ID =:id 
                                        ORDER BY user.code, user.user_login');

        $req->bindParam(':id', $id);
        $req->execute();
        while ($data = $req->fetch()) {
            $var[] = $data;
        }
        return $var;
        $req->closeCursor();
        global $wpdb;
        $result = $wpdb->get_row('SELECT * FROM `wp_users` WHERE `ID` ="' . $id . '"', ARRAY_A);
        return $result;
    }

    /**
     * Renvoie tout les code ADE qui n'ont pas été enregistré dans la bd code_ade mais enregistré dans les étudiants
     * @return array
     */
    public function codeNotBound($type = null){
        $users = $this->getUsersByRole('etudiant');
        $allCode = array();
        $usersCodes = array();
        if(is_array($users)) {
            foreach ($users as $user){
                $codes = unserialize($user['code']);
                foreach ($codes as $code) {
                    if($code != '') {
                        $usersCodes[] = $codes[$type];
                    }
                }
            }
        } else {
            $codes = unserialize($users['code']);
            $usersCodes[] = $codes[$type];
        }

        $codesAde = $this->getAll('code_ade');
        $notRegisterCode = array();

        if(isset($codesAde)){
            foreach ($codesAde as $codeAde){
                $allCode[] = $codeAde['code'];
            }
        }

        if(isset($usersCodes)){
            if(is_array($usersCodes)){
                foreach ($usersCodes as $userCode){
                    if(! in_array($userCode, $allCode)) {
                        $notRegisterCode[] = $userCode;
                    }
                }
            } else {
                if(! in_array($usersCodes, $allCode)) {
                    $notRegisterCode[] = $usersCodes;
                }
            }
        }
        if(empty($notRegisterCode)) {
            return null;
        } else {
            return $notRegisterCode;
        }
    }
}