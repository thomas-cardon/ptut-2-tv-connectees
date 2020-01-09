<?php
/**
 * Created by PhpStorm.
 * UserView: Rohrb
 * Date: 29/04/2019
 * Time: 09:36
 */

class CodeAdeManager extends Model
{

    /**
     * Vérifie si un code identique existe déjà
     * @param $code     int Code ADE
     * @return bool     Renvoie vrai s'il y a un doublon
     */
    protected function checkIfDoubleCode($code)
    {
        $var = 0;
        $req = $this->getDbh()->prepare('SELECT * FROM code_ade WHERE code =:code');
        $req->bindValue(':code', $code);
        $req->execute();
        while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
            $var = $var + 1;
        }
        if ($var > 0)
            return true;
        else
            return false;

        $req->closeCursor();
    }

    /**
     * Vérifie si un titre identique existe déjà
     * @param $title    string Titre du code ADE
     * @return bool     Renvoie vrai s'il y a un doublon
     */
    protected function checkIfDoubleTitle($title)
    {
        $var = 0;
        $req = $this->getDbh()->prepare('SELECT * FROM code_ade WHERE title =:title');
        $req->bindValue(':title', $title);
        $req->execute();
        while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
            $var = $var + 1;
        }
        if ($var > 0)
            return true;
        else
            return false;

        $req->closeCursor();
    }


    /**
     * Ajoute le code dans la base de données
     * @param $type     string Type du code (Année/Groupe/Demi-Groupe)
     * @param $title    string Titre du code
     * @param $code     int Code ADE
     * @return bool     Renvoie vrai si le code est enregistré
     */
    public function addCode($type, $title, $code)
    {
        if (!($this->checkIfDoubleCode($code)) && !($this->checkIfDoubleTitle($title))) {
            $req = $this->getDbh()->prepare('INSERT INTO code_ade (type, title, code) 
                                         VALUES (:type, :title, :code)');

            $req->bindParam(':type', $type);
            $req->bindParam(':title', $title);
            $req->bindParam(':code', $code);

            $req->execute();

            return true;
        } else {
            return false;
        }
    }

    /**
     * Modifie le code si le titre ou le code écrit n'existe pas déjà
     * @param $result - Données avant modification
     * @param $id       int ID du code
     * @param $title    string Titre du code
     * @param $code     int Code ADE
     * @param $type     string Type du code (Année/Groupe/Demi-Groupe)
     * @return bool     Renvoie vrai si le code est modifié
     */
    public function checkModify($result, $id, $title, $code, $type)
    {
        if ($result[0]['title'] != $title && $result[0]['code'] != $code) {
            if (!($this->checkIfDoubleCode($code)) && !($this->checkIfDoubleTitle($title))) {
                $this->modifyCode($id, $title, $code, $type);
                return true;
            } else {
                return false;
            }
        } elseif ($result[0]['title'] == $title && $result[0]['code'] != $code) {
            if (!($this->checkIfDoubleCode($code))) {
                $this->modifyCode($id, $title, $code, $type);
                return true;
            } else {
                return false;
            }
        } elseif ($result[0]['title'] != $title && $result[0]['code'] == $code) {
            if (!($this->checkIfDoubleTitle($title))) {
                $this->modifyCode($id, $title, $code, $type);
                return true;
            } else {
                return false;
            }
        } elseif ($result[0]['title'] == $title && $result[0]['code'] == $code) {
            $this->modifyCode($id, $title, $code, $type);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Modifie le code
     * @param $id       int ID du code
     * @param $title    string Titre du code
     * @param $code     int Code ADE
     * @param $type     string Type du code (Année/Groupe/Demi-Groupe)
     */
    protected function modifyCode($id, $title, $code, $type)
    {
        $req = $this->getDbh()->prepare('UPDATE code_ade SET title=:title, code=:code, type=:type WHERE ID=:id');
        $req->bindParam(':id', $id);
        $req->bindParam(':title', $title);
        $req->bindParam(':code', $code);
        $req->bindParam(':type', $type);
        $req->execute();
    }

    /**
     * Supprime le code
     * @param $id   int ID du code
     */
    public function deleteCode($id)
    {
        $this->deleteTuple('code_ade', $id);
    }

    /**
     * Renvoie toute la table de code_ade
     * @return array
     */
    public function getAllCode()
    {
        return parent::getAll('code_ade');
    }

    /**
     * Renvoie le code
     * @param $id   int ID du code
     * @return array
     */
    public function getCode($id)
    {
        $var = [];
        $req = $this->getDbh()->prepare('SELECT * FROM code_ade WHERE ID = :id');
        $req->bindParam(':id', $id);
        $req->execute();
        while ($data = $req->fetch()) {
            $var[] = $data;
        }
        return $var;
        $req->closeCursor();
    }
}