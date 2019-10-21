<?php
/**
 * Created by PhpStorm.
 * User: Rohrb
 * Date: 25/04/2019
 * Time: 10:52
 */

class ViewSecretary extends ViewG
{
    /**
     * Affiche un formulaire d'inscription pour les secretaires
     * @return string   Renvoie le formulaire
     */
    public function displayFormSecretary() {
        return '<h1> Création compte secrétaire </h1>'.$this->displayBaseForm('Secre');
    }

    /**
     * Affiche l'en-tête du tableau des secrétaires
     * @return string
     */
    public function displayHeaderTabSecretary(){
        $title = "Secrétaires";
        return $this->displayStartTabLog('secre', $title);
    }

    /**
     * Affiche une ligne avec le login d'un secrétaire
     * @param $row      Numéro de ligne
     * @param $id       ID du secrétaire
     * @param $login    Login du secrétaire
     * @return string   Renvoie la ligne
     */
    public function displayAllSecretary($row, $id, $login){
        $tab[] = $login;
        return $this->displayAll($row, 'secre',$id, $tab);
    }
}