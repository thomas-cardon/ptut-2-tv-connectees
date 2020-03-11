<?php

namespace Views;

use Models\User;

/**
 * Class SecretaryView
 *
 * All view for secretary (Forms, tables, messages)
 *
 * @package Views
 */
class SecretaryView extends UserView
{

    /**
     * Display the creation form
     *
     * @return string
     */
    public function displayFormSecretary()
    {
        return '<h2> Compte secrétaire </h2>' . $this->displayBaseForm('Secre');
    }

    /**
     * Display a button for download all schedules
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
     * Display all secretary
     *
     * @param $users    User[]
     *
     * @return string
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

	    return $this->displayAll($name, $title, $header, $row, 'secre');
    }

    /**
     * Ask to the user to choose an user
     */
    public function displaynoUser()
    {
        return '<p class="alert alert-danger">Veuillez choisir un utilisateur </p>';
    }
}