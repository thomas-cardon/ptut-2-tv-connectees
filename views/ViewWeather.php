<?php
/**
 * Created by PhpStorm.
 * User: r17000292
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
    <script src="/wp-content/plugins/TeleConnecteeAmu/views/js/weather.js"></script>
    <div class="Infos">
        <div class="Time" id="Time">
        </div>
        <div class="Date" id="Date">
        </div>
        <div class="Weather" id="Weather">
        </div>
    </div>';
    }
}