<?php


class TechnicianView extends UserView
{

    /**
     * Formulaire pour inscrire un utilisateur
     * @return string
     */
    public function displayFormTechnician()
    {
        return '<h2>Compte technicien</h2>' . $this->displayBaseForm('Tech');
    }

    /**
     * En-tête du tableau des techniciens
     * @return string   Renvoie l'en-tête
     */
    public function displayHeaderTabTechnician()
    {
        return $this->displayStartTabLog('tech', 'Techniciens');
    }

    /**
     * Affiche une ligne contenant les données du technicien
     * @param $row      int Numéro de ligne
     * @param $id       int ID du technicien
     * @param $login    string Login du technicien
     * @return string   Renvoie la ligne
     */
    public function displayAllTechnicians($row, $id, $login)
    {
        $tab[] = $login;
        return $this->displayRowTable($row, 'tech', $id, $tab);
    }

    public function displayDivSchedule() {
    	return '<div class="shedule_tech">';
    }
}