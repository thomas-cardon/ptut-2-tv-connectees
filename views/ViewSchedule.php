<?php
/**
 * Created by PhpStorm.
 * UserView: r17000292
 * Date: 30/01/2019
 * Time: 11:54
 */

class ViewSchedule extends ViewG
{
    /**
     * Affiche un titre
     * @param $title    Titre à afficher
     */
    public function displayName($title) {
        echo '<h1>'.$title.'</h1>';
    }

    /**
     * Début du diaporama
     */
    public function displayStartSlide(){
        echo '
            <div class="slideshow-container">
                <div class="mySlides">';
    }

    /**
     * Milieu du dipao, on l'utilise une fois par objet à afficher
     */
    public function displayMidSlide(){
        echo '
                </div>
              <div class="mySlides">';
    }

    /**
     * Fin du diaporama
     */
    public function displayEndSlide() {
        echo '          
                       </div>
                   </div>';
    }

    /**
     * Signal qu'il n'y pas cours
     */
    public function displayEmptySchedule(){
        echo '<div> Vous n\'avez pas cours !</div>';
    }

    /**
     * Souhaite la bienvenue à l'utilisateur
     */
    public function displayWelcome(){
        echo '<h3> Bienvenue sur l\'écran connecté ! </h3>';
    }

    /**
     * Souhaite la bienvenue à l'utilisateur
     */
    public function displayWelcomeAdmin(){
        echo '<h3> Bienvenue sur l\'écran connecté ! </h3>
                <form method="post" id="dlAllEDT">
                    <input type="submit" name="dlEDT" value="Retélécharger les emplois du temps">
                </form>';
    }

    /**
     * Demande à la persone de choisir son emploi du temps
     */
    public function displaySelectSchedule(){
        echo '<div> Veuillez sélectionner un emploi du temps </div>';
    }
}