<?php
/**
 * Created by PhpStorm.
 * User: r17000292
 * Date: 15/04/2019
 * Time: 09:29
 */

/**
 * Affiche la météo
 * Class Weather
 */
class Weather{

    /**
     * Vue de Weather
     * @var ViewWeather
     */
    private $view;

    /**
     * Constructeur de Weather.
     */
    public function __construct(){
        $this->view = new ViewWeather();
    }

    /**
     * Affiche la météo si l'utilisateur est connecté
     */
    public function displayWeather()
    {
        if (is_user_logged_in()) {
            $this->view->displayWeather();
        }
    }
}