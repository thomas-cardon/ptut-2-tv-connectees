<?php

namespace Views;


class SecretaryView extends UserView
{

    /**
     * Affiche un formulaire d'inscription pour les secretaires
     *
     * @return string   Renvoie le formulaire
     */
    public function displayFormSecretary()
    {
        return '<h2> Compte secrétaire </h2>' . $this->displayBaseForm('Secre');
    }

    /**
     * Souhaite la bienvenue à l'utilisateur
     */
    public function displayWelcomeAdmin()
    {
        echo '<h1>Écran connecté</h1>
              <form method="post" id="dlAllEDT">
              <label for="dlEDT">Mettre à jours des emplois du temps</label>
                <input id="dlEDT" type="submit" name="dlEDT" value="Télécharger">
              </form>';
    }

    /**
     * Affiche une ligne avec le login d'un secrétaire
     *
     * @return string   Renvoie la ligne
     */
    public function displayAllSecretary($users)
    {
	    $title = 'Secrétaires';
	    $name = 'secre';
	    $header = ['Login'];

	    $row = array();
	    $count = 0;
	    foreach ($users as $user) {
		    ++$count;
		    $row[] = [$count, $this->buildCheckbox($name, $user->getId()), $user->getLogin()];
	    }

	    return $this->displayAll($name, $title, $header, $row);
    }

    /**
     * Demande de sélectionner un utilisateur
     */
    public function displaynoUser()
    {
        return '<p class="alert alert-danger">Veuillez choisir un utilisateur </p>';
    }
}