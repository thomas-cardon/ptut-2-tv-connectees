<?php
/**
 * Created by PhpStorm.
 * UserView: r17000292
 * Date: 06/02/2019
 * Time: 17:23
 */

/**
 * Permet de gérer les emplois du temps,
 * C'est ici qu'on appel le controlleur R34ICS
 * Interface Schedule
 */
interface Schedule
{

    /**
     * Affiche l'emploi du temps demandé
     * @param $code int Code ADE de l'emploi du temps
     */
    public function displaySchedule($code);

    /**
     * Affiche l'emploi du temps de la personne connectée,
     * Si cette personne n'a pas d'emploi du temps, on lui souhaite la bienvenue sur le site
     */
    public function displayMySchedule();
}
