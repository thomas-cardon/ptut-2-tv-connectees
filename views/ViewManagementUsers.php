<?php
/**
 * Created by PhpStorm.
 * UserView: Rohrb
 * Date: 26/04/2019
 * Time: 11:03
 */

class ViewManagementUsers extends ViewG
{
    /**
     * Demande de sélectionner un utilisateur
     */
    public function displaynoUser() {
        echo '<div class="alert alert-danger">Veuillez choisir un utilisateur </div>';
    }
}