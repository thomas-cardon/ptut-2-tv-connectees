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
        return '
        <h2>Compte secrétaire</h2>
        <p class="lead">Pour créer des secrétaires, remplissez ce formulaire avec les valeurs demandées.</p>
        ' . $this->displayBaseForm('Secre');
    }

    /**
     * Displays the admin dashboard
     * @author Thomas Cardon
     */
    public function displayContent()
    {
        return '<section class="container col-xxl-10">
      <div class="row flex-lg-row-reverse align-items-center g-5 mb-5">
        <div class="col-10 col-sm-8 col-lg-6">
          <img draggable="false" src="https://upload.wikimedia.org/wikipedia/commons/thumb/f/f8/Aix-Marseille_université_%28logo%29.png/1920px-Aix-Marseille_université_%28logo%29.png" class="d-block mx-lg-auto img-fluid" alt="Bootstrap Themes" loading="lazy" width="700" height="500">
        </div>
        <div class="col-lg-6">
          <h1 class="display-5 fw-bold title-bold">' . get_bloginfo("name") . '</h1>
          <p class="lead">
            Créez des informations pour toutes les télévisions connectées, les informations seront affichées sur chaque télévisions en plus des informations déjà publiées.
            Les informations sur les télévisions peuvent contenir du texte, des images et même des pdf.
            <br /> <br />
            Vous pouvez faire de même avec les <b>alertes</b> des télévisions connectées.
            Les informations seront affichées dans la partie droite, et les alertes dans le bandeau rouge en bas des TV.
          </p>
        </div>
      </div>
      <div class="row align-items-md-stretch my-2">
        <div class="col-md-6">
          <div class="h-100 p-5 text-white bg-dark rounded-3">
            <h2 class="title-block">(+) Ajouter</h2>
            <p>Ajoutez une information ou une alerte.</p>
            <a href="' . home_url('/creer-information') . '" class="btn btn-outline-light" role="button">Information</a>
            <a href="' . home_url('/gerer-les-alertes') . '" class="btn btn-outline-light" role="button">Alerte</a>
          </div>
        </div>
        <div class="col-md-6">
          <div class="h-100 p-5 text-white bg-danger border rounded-3">
            <h2 class="title-block">Interface secrétaires</h2>
            <p>Accédez au mode tablette.</p>
            <a href="' . home_url('/tablet-view') . '" class="btn btn-dark" role="button">Voir</a>
          </div>
        </div>
      </div>
      <div class="row align-items-md-stretch my-2 mb-5">
        <div class="col-md-6">
          <div class="h-100 p-5 bg-light border rounded-3">
            <h2 class="title-block title-bold">👷 Personnel</h2>
            <p>Ajoutez des utilisateurs qui pourront à leur tour des informations, alertes, etc.</p>
            <a href="' . home_url('/creer-utilisateur') . '" class="btn btn-danger" role="button">Créer</a>
            <a href="' . home_url('/liste-utilisateur') . '" class="btn btn-dark" role="button">Voir</a>
          </div>
        </div>
        <div class="col-md-6">
          <div class="h-100 p-5 text-white bg-info rounded-3">
            <h2 class="title-block">Emploi du temps</h2>
            <p>Forcez l\'actualisation des emplois du temps.</p>
            <form method="post" id="dlAllEDT">
              <input id="dlEDT" class="btn btn-outline-light" type="submit" name="dlEDT" value="🔄️ Actualiser" />
            </form>
          </div>
        </div>
      </div>
    </section>';
    }

    /**
     * Display all secretary
     *
     * @param $users    User[]
     *
     * @return string
     */
    public function displayTableSecretary($users)
    {
        $title = '<b>Rôle affiché: </b> Secrétaire';
        $name = 'Secre';
        $header = ['Identifiant'];

        $row = array();
        $count = 0;
        foreach ($users as $user) {
            ++$count;
            $row[] = [$count, $this->buildCheckbox($name, $user->getId()), $user->getLogin()];
        }

        return $this->displayTable($name, $title, $header, $row, 'Secre', '<a type="submit" class="btn btn-primary" role="button" aria-disabled="true" href="' . home_url('/creer-utilisateur') . '">Créer</a>');
    }

    /**
     * Ask to the user to choose an user
     */
    public function displayNoUser()
    {
        return '<p class="alert alert-danger">Veuillez choisir un utilisateur</p>';
    }

    /**
     * Displays the form to create a new user
     *
     * @return string
     */
    public function displayUserCreationForm() : string
    {
        return '<div class="container col-xxl-10">
        <h2 class="display-6">Créer un utilisateur</h2>
        <p class="lead">Pour créer un utilisateur, remplissez ce formulaire avec les valeurs demandées.</p>

        <hr class="my-4">
        
        ' . (isset($_GET['message']) ? '<div class="alert alert-' . $_GET['message'] . '">' . $_GET['message_content'] . '</div>' : '') . '

        <form method="post" action="' . admin_url('admin-post.php') . '">
          <div class="form-outline mb-2">
            <label class="form-label" for="form3Example1cg">Identifiant du compte</label>
            <input type="text" id="login" name="login" placeholder="Exemple: prenom.nom" class="form-control form-control-lg" minlength="3" required />
          </div>

          <div class="form-outline mb-2">
            <label class="form-label" for="email">Votre adresse e-mail</label>
            <input type="email" id="email" name="email" class="form-control form-control-lg" required />
          </div>

          <div class="form-outline mb-2">
            <label class="form-label" for="password">Mot de passe - <i>requis: 1 chiffre, 1 lettre majuscule, 1 lettre minuscule, et 1 symbole parmis ceux-ci: <kbd> !@#$%^&*_=+-</kbd></i></label>
            <input type="password" id="password" name="password1" class="form-control form-control-lg" minlength="8" required />
          </div>

          <div class="form-outline mb-2">
            <label class="form-label" for="password2">Confirmez votre mot de passe</label>
            <input type="password" id="password2" name="password2" class="form-control form-control-lg" minlength="8" required />
          </div>

          <input type="hidden" name="action" value="create_user">

          <div class="form-outline mb-2 pb-4">
            <label class="form-label" for="role">Rôle</label>
            <select class="form-control form-control-lg" id="role" name="role">
              <option value="secretary">Secrétaire</option>
              <option value="admin">Administrateur</option>
              <option value="teacher">Enseignant</option>
              <option value="television">Télévision</option>
              <option value="technician">Technicien</option>
              <option value"studyDirector">Directeur d\'études</option>
            </select>
          </div>
          
          <input type="submit" class="btn btn-primary" role="button" aria-disabled="true" value="Créer">
          <a href="' . home_url('/users/list') . '" class="btn btn-secondary" role="button" aria-disabled="true">Annuler</a>
        </form>
      </div>';
    }

    public function displayUserCreationFormExcel() : string {
        return '<div class="container col-xxl-10">
        <h2 class="display-6">Créer un utilisateur</h2>
        <p class="lead">
          Pour créer un utilisateur, <a href="#">téléchargez le fichier CSV</a> et remplissez les champs demandés.
        </p>

        <hr class="my-4">
        
        ' . (isset($_GET['message']) ? '<div class="alert alert-' . $_GET['message'] . '">' . $_GET['message_content'] . '</div>' : '') . '

        <form method="post" action="' . admin_url('admin-post.php') . '">
          <div class="form-outline mb-2">
            <label for="file" class="form-label">Déposez le fichier Excel ici</label>
            <input class="form-control form-control-lg" id="file" type="file" />
          </div>

          <input type="hidden" name="action" value="createUsers">
        </form>
      </div>';
    }

    public function displaySecretaryWelcome() : string{
        return
            '<a class="btn" href="' . home_url('/secretary/year-student-schedule') . '">BUT 1</a>
            <input type="submit" name="BUT2" value="BUT 2" />
            <input type="submit" name="BUT3" value="BUT 3" />
            <a class="btn" href="' . home_url('/secretary/teacher-search-schedule') . '">ENSEIGNANTS</a>
            <a class="btn" href="' . home_url('/secretary/computer-rooms') . '">SALLES MACHINE</a>
            <a class="btn" href="' . home_url('/secretary/room-schedule') . '">SALLES DISPONIBLES</a>';
    }
}
