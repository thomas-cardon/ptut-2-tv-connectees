<?php


class ViewICS extends ViewG
{
    /**
     * Affiche l'emploi du temps d'un fichier ICS
     * @param $ics_data     Toutes les données du fichier ICS
     * @param $title        Titre de l'emploi du temps
     * @return bool         Renvoie vrai s'il y a des données
     */
    public function displaySchedule($ics_data, $title){
        $current_user = wp_get_current_user();
        if( in_array("technicien", $current_user->roles)){
            echo '<div class="col-sm-6">';
        }
        echo '<h1>'.$title.'</h1>';
        // Empty calendar message
        if (empty($ics_data['events'])){
            echo 'Vous n\'avez pas cours !';
            return false;
        } else {
            $i = 0;
            $study = 0;
            foreach (array_keys((array)$ics_data['events']) as $year) {
                for ($m = 1; $m <= 12; $m++) {
                    $month = $m < 10 ? '0' . $m : '' . $m;
                    // Build month's calendar
                    if (isset($ics_data['events'][$year][$month])) {
                        foreach ((array)$ics_data['events'][$year][$month] as $day => $day_events) {
                            $date = mktime(0,0,0,$month,$day,$year);
                            $date_label = ucwords(date_i18n('l j F Y', $date));
                            $nbevents = 0;
                            $nboccurence = 0;
                            foreach ((array)$day_events as $time => $events) {
                                $all_day_indicator_shown = false;
                                foreach ((array)$events as $event) {
                                    if(date("d") == date("d", strtotime($event['fin']))){
                                        $study = $study + 1;
                                        if(($nboccurence == 0 || $nbevents == 20)){
                                            if($nbevents == 20){
                                                $nbevents = 0;
                                                echo'</tbody>
                                           </table>';
                                            }
                                            echo'<table class="table tabSchedule">
                                            <thead class="headerTab">
                                            <tr>
                                                <th scope="col" class="text-light text-center" width="20%">Horaire</th>';
                                            if(! in_array("technicien", $current_user->roles)){
                                                echo '<th scope="col" class="text-light text-center" width="35%">Cours</th>
                                                <th scope="col" class="text-light text-center" width="25%">Groupe/Enseignant</th>';
                                            }
                                            echo '
                                                <th scope="col" class="text-light text-center" width="20%">Salle</th>
                                            </tr>
                                            </thead>
                                            <tbody>';
                                        }

                                        ++$nboccurence;
                                        // et on supprime cours qui ont déja eu lieu
                                        $heure = date("H:i");
                                        if (!(date("H:i",strtotime($event['fin'])) <= $heure) ){
                                            //Si le cours est en vigueur
                                            if(date("H:i",strtotime($event['deb'])) <= $heure && $heure < date("H:i",strtotime($event['fin']))){
                                                ++$nbevents;
                                                echo '<tr class="table-success" scope="row">';
                                            }
                                            else if(date("H:i",strtotime($event['deb'])) > $heure) {
                                                ++$nbevents;
                                                echo '<tr scope="row">';
                                            }
                                            if ($time == 'all-day') {
                                                if (!$all_day_indicator_shown) {
                                                    echo '<td class="all-day-indicator">'; _e('All Day', 'R34ICS'); echo'</td>';
                                                    $all_day_indicator_shown = true;
                                                }
                                                echo '<td class="event">
                                            <span class="title">';  echo str_replace('/', '/<wbr />',$event['label']).'</span>';
                                                if (!empty($event['sublabel'])) {
                                                    echo '<span class="sublabel">'; echo str_replace('/()', '/<wbr />',$event['sublabel']).'</span>';
                                                }
                                                echo '</td>';
                                            }
                                            else {
                                                if (!empty($event['start'])) {
                                                    echo '<td class="text-center" width="20%">';
                                                    $deb = date("H:i",strtotime($event['deb']));
                                                    $newDeb = str_replace(':','h',$deb);
                                                    echo $newDeb.' ';
                                                    if (!empty($event['end'])) {
                                                        echo '<span class="time">&#8211;'; $fin = date("H:i",strtotime($event['fin']));
                                                        $newFin = str_replace(':','h',$fin);
                                                        echo ' '.$newFin.'</span>';
                                                        echo '<!--'. date("d"). '-->';
                                                        echo '<!--'. date("d",strtotime($event['fin'])).' -->';
                                                    }
                                                    echo '</td>';
                                                }
                                                if(! in_array("technicien", $current_user->roles)) {
                                                    $oldEvent = $event['label'];
                                                    $subEvent = substr($oldEvent, -3);
                                                    if($subEvent == "alt"){
                                                        $oldEvent = substr($oldEvent,0, -3);
                                                    }
                                                    echo '<td class="text-center" width="35%">
                                                    <span class="title">'; echo str_replace('/', '/<wbr />',$oldEvent).'</span>';

                                                    if (!empty($event['sublabel'])) {
                                                        echo '<span class="sublabel">';
                                                        if (empty($event['start']) && !empty($event['end'])) {
                                                            echo '<span class="carryover">&#10554;</span>';
                                                        }
                                                        echo str_replace('/', '/<wbr />',$event['sublabel']).'</span>';
                                                    }
                                                    echo '</td>
                                                <td class="text-center" width="25%">
                                                        <span class="sublabel">'; $des = $event['description'];
                                                    $des = substr($des,0,-29);
                                                    echo $des.'</span>
                                                </td >';
                                                }

                                                echo '
                                        <td class="text-center" width="20%">
                                            <span>'; echo str_replace('/', '/<wbr />',$event['location']).'</span>
                                        </td>';
                                            }
                                            echo '</tr>';
                                        }
                                        if ($nbevents == 8){
                                            break(2);
                                        }
                                    }
                                }
                            }
                            echo '</tbody>
                        </table>';
                            break(3);
                        }
                        echo '</div>';
                    }
                }
            } if($study == 0) {
                echo '<div> Vous n\'avez pas cours !</div>';
            }
        }
        if( in_array("technicien", $current_user->roles)){
            echo '</div>';
        }
    }
}