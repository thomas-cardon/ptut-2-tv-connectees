<?php
/**
 * Created by PhpStorm.
 * UserView: r17000292
 * Date: 15/04/2019
 * Time: 09:07
 */

class ViewWeather
{
    /**
     * Affiche la météo
     */
    public function displayWeather() {
        echo '
    <aside class="Infos">
        <p class="Time" id="Time">
      
        </p>
        <p class="Date" id="Date">
        </p>
        <p class="Weather" id="Weather">
        </p>
    </aside>';
    }
}