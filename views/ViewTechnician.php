<?php


class ViewTechnician extends UserView
{
    /**
     * Formulaire pour inscrire un utilisateur
     * @return string
     */
    public function displayFormTechnician(){
        return '<h1> Création compte technicien</h1>'.$this->displayBaseForm('Tech');
    }

    /**
     * En-tête du tableau des techniciens
     * @return string   Renvoie l'en-tête
     */
    public function displayHeaderTabTechnician(){
        return $this->displayStartTabLog('tech', 'Techniciens');
    }

    /**
     * Affiche une ligne contenant les données du technicien
     * @param $row      Numéro de ligne
     * @param $id       ID du technicien
     * @param $login    Login du technicien
     * @return string   Renvoie la ligne
     */
    public function displayAllTechnicians($row, $id, $login){
        $tab[] = $login;
        return $this->displayAll($row, 'tech', $id, $tab);
    }
}