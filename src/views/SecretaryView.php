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
	    $page = get_page_by_title('Création des comptes');
	    $linkCreateAccount = get_permalink($page->ID);

	    $page = get_page_by_title('Gestion des utilisateurs');
	    $linkManageUser = get_permalink($page->ID);

	    $page = get_page_by_title('Créer une alerte');
	    $linkCreateAlert = get_permalink($page->ID);

	    $page = get_page_by_title('Gérer les alertes');
	    $linkManageAlert = get_permalink($page->ID);

	    $page = get_page_by_title('Créer une information');
	    $linkCreateInfo = get_permalink($page->ID);

	    $page = get_page_by_title('Gérer les informations');
	    $linkManageInfo = get_permalink($page->ID);


        echo '<h1>Écran connecté</h1>
              <form method="post" id="dlAllEDT">
              	<label for="dlEDT">Mettre à jours les emplois du temps</label>
              	<input id="dlEDT" type="submit" name="dlEDT" value="Télécharger">
              </form>
              <h2 class="text-center">Gestion</h2>
              <div class="card-group text-center">
			  	<div class="card">
					<a href="'.$linkCreateAccount.'"><img class="card-img-top add_icon" src="'.TV_PLUG_PATH.'public/img/addUser.png" alt="Ajouter un Utilisateur"></a>
				    <div class="card-body">
				      <a href="'.$linkCreateAccount.'"><h5 class="card-title">Ajouter un utilisateur</h5></a>
				      <p class="card-text">Vous pouvez inscire un ou plusieurs utilisateurs (Etudiant, Enseignant, Directeur d\'études, Secrétaire, technicien, télévision).</p>
				      <a href="'.$linkCreateAccount.'" class="btn btn-primary text-white">Ajouter un utilisateur</a><br/>
				      <a href="'.$linkManageUser.'" class="btn btn-primary text-white btn_list">Liste des utilisateurs</a>
				    </div>
				</div>
				<div class="card">
					<a href="'.$linkCreateInfo.'"><img class="card-img-top add_icon" src="'.TV_PLUG_PATH.'public/img/information.png" alt="Ajouter une information"></a>
				    <div class="card-body">
				      <a href="'.$linkCreateInfo.'"><h5 class="card-title">Ajouter une information</h5></a>
				      <p class="card-text">Vous pouvez créer une information (Du texte, une image, un tableau, un PDF), cette information sera affiché à coté de l\'emploi du temps.</p>
				      <a href="'.$linkCreateInfo.'" class="btn btn-primary text-white">Ajouter une information</a><br/>
				   	  <a href="'.$linkManageInfo.'" class="btn btn-primary text-white btn_list">Liste des informations</a>
				    </div>
				</div>
				<div class="card">
					<a href="'.$linkCreateAlert.'"><img class="card-img-top add_icon" src="'.TV_PLUG_PATH.'public/img/alert.png" alt="Ajouter une alerte"></a>
				    <div class="card-body">
				      <a href="'.$linkCreateAlert.'"><h5 class="card-title">Ajouter une alerte</h5></a>
				      <p class="card-text">Vous pouvez afficher une alerte, l\'alerte sera affiché en dessous de l\'emploi du temps dans une zone rouge.</p>
				      <a href="'.$linkCreateAlert.'" class="btn btn-primary text-white">Ajouter une alerte</a><br/>
				      <a href="'.$linkManageAlert.'" class="btn btn-primary text-white btn_list">Liste des alertes</a>
				    </div>
				</div>
			  </div>';
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
    public function displayNoUser()
    {
        return '<p class="alert alert-danger">Veuillez choisir un utilisateur </p>';
    }
}