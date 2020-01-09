<?php
/**
 * Created by PhpStorm.
 * UserView: Rohrb
 * Date: 25/03/2019
 * Time: 18:10
 */

abstract class Model {


    private static $dbh;

    /**
     * Set the db with PDO
     */
    private static function setDb() {
        self::$dbh = new PDO( 'mysql:host=' . DB_HOST . '; dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
        self::$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    }

    /**
     * Return the db
     * @return mixed
     */
    protected function getDbh() {
        if ( self:: $dbh == null)
            self::setDb();
        return self::$dbh;
    }

    /**
     * Renvoi tous le contenue de la table
     * @param $table    string Nome de la table
     * @return array
     */
    protected function getAll($table) {
        $var = [];
        $req = $this->getDbh()->prepare( 'SELECT * FROM ' . $table . ' ORDER BY ID desc');
        $req->execute();
        while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
            $var[] = $data;
        }
        return $var;
        $req->closeCursor();
    }

    /**
     * Renvoie tous les utilisateurs ayant le role sélectionné
     * @param $role     string role des utilisateurs recherchés
     * @return array
     */
    public function getUsersByRole($role) {
        $req = $this->getDbh()->prepare('SELECT * FROM wp_users user, wp_usermeta meta WHERE user.ID = meta.user_id AND meta.meta_value =:role 
                                        ORDER BY user.code, user.user_login');
        $size = strlen($role);
        $role = 'a:1:{s:' . $size . ':"' . $role . '";b:1;}';
        $req->bindParam(':role', $role);
        $req->execute();
        while ($data = $req->fetch()) {
            $var[] = $data;
        }
        return $var;
        $req->closeCursor();
    }

    /**
     * Renvoie le titre du code ADE
     * @param $code     int code ADE
     * @return array
     */
    public function getTitleOfCode($code) {
        $req = $this->getDbh()->prepare('SELECT title FROM code_ade WHERE code = :code');
        $req->bindParam(':code', $code);
        $req->execute();
        while ($data = $req->fetch()) {
            $var[] = $data;
        }
        return $var;
        $req->closeCursor();
    }

    /**
     * Renvoie tous les codes ADE qui sont des années
     * @return array
     */
    public function getCodeYear() {
        $req = $this->getDbh()->prepare('SELECT * FROM code_ade WHERE type = "Annee" ORDER BY title');
        $req->execute();
		$data = $req->fetchAll();
        return $data;
    }

    /**
     * Renvoie tous les codes ADE qui sont des groupes
     * @return array
     */
    public function getCodeGroup() {
        $req = $this->getDbh()->prepare('SELECT * FROM code_ade WHERE type = "Groupe" ORDER BY title');
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
    public function getCodeHalfgroup() {
        $req = $this->getDbh()->prepare('SELECT * FROM code_ade WHERE type = "Demi-Groupe" ORDER BY title');
        $req->execute();
        while ($data = $req->fetch()) {
            $var[] = $data;
        }
        return $var;
        $req->closeCursor();
    }

    /**
     * Envoie le titre du code ADE
     * @param $code     int Code ADE
     * @return mixed    Renvoie le titre si le code est enregistré, sinon on renvoie le code
     */
    public function getCodeTitle($code) {
        $var = $this->getTitleOfCode($code);
        if (!isset($var[0]['title'])) $var[0]['title'] = $code;
        return $var[0]['title'];
    }

    /**
     * Supprime une ligne d'une table de données
     * @param $table    string Table de données
     * @param $id       int ID de la ligne à supprimer
     */
    protected function deleteTuple($table, $id) {
        $req = $this->getDbh()->prepare( 'DELETE FROM ' . $table . ' WHERE ID = :id');
        $req->bindValue(':id', $id);

        $req->execute();
    }

    /**
     * Supprime un utilisateur
     * @param $id   int ID de l'utilisateur
     */
    public function deleteUser($id) {
        $this->deleteTuple('wp_users', $id);
        $req = $this->getDbh()->prepare('DELETE FROM wp_usermeta WHERE user_id = :id');
        $req->bindValue(':id', $id);

        $req->execute();
    }

    /**
     * Renvoie un utilisateur grâce à son ID
     * @param $id       int ID de l'utilisateur souhaité
     * @return mixed    Renvoie les données concernant l'utilisateur
     */
    public function getById($id) {
        $req = $this->getDbh()->prepare('SELECT * FROM wp_users user, wp_usermeta meta WHERE user.ID = meta.user_id AND user.ID =:id 
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
    public function codeNotBound($type = null) {
        $users = $this->getUsersByRole('etudiant');
        $allCode = array();
        $usersCodes = array();
        if (is_array($users)) {
            foreach ($users as $user) {
                $codes = unserialize($user['code']);
                foreach ($codes as $code) {
                    if ($code != '' && $code != 0) {
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

        if (isset($codesAde)) {
            foreach ($codesAde as $codeAde) {
                $allCode[] = $codeAde['code'];
            }
        }

        if (isset($usersCodes)) {
            if (is_array($usersCodes)) {
                foreach ($usersCodes as $userCode) {
                    if (!in_array($userCode, $allCode)) {
                        $notRegisterCode[] = $userCode;
                    }
                }
            } else {
                if (!in_array($usersCodes, $allCode)) {
                    $notRegisterCode[] = $usersCodes;
                }
            }
        }
        if (empty($notRegisterCode)) {
            return null;
        } else {
            return $notRegisterCode;
        }
    }
}