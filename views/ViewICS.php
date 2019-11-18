<?php


class ViewICS extends ViewG
{
    /**
     * Affiche l'emploi du temps d'un fichier ICS
     * @param $ics_data     array Toutes les données du fichier ICS
     * @param $title        string Titre de l'emploi du temps
     * @return bool         Renvoie vrai s'il y a des données
     */
    public function displaySchedule($ics_data, $title)
    {
        $current_user = wp_get_current_user();
        $string = "";
        if (in_array("technicien", $current_user->roles)) {
            $string .= '<div class="col-sm-6">';
        }
        $string .= '<h1>' . $title . '</h1>';
        // Empty calendar message
        if (empty($ics_data['events'])) {
            return $string . '<p>Vous n\'avez pas cours !</p>';
        } else {
            $i = 0;
            $study = 0;
            foreach (array_keys((array)$ics_data['events']) as $year) {
                for ($m = 1; $m <= 12; $m++) {
                    $month = $m < 10 ? '0' . $m : '' . $m;
                    // Build month's calendar
                    if (isset($ics_data['events'][$year][$month])) {
                        foreach ((array)$ics_data['events'][$year][$month] as $day => $day_events) {
                            $date = mktime(0, 0, 0, $month, $day, $year);
                            $date_label = ucwords(date_i18n('l j F Y', $date));
                            $nbevents = 0;
                            $nboccurence = 0;
                            foreach ((array)$day_events as $time => $events) {
                                $all_day_indicator_shown = false;
                                foreach ((array)$events as $event) {
                                    if (date("d") == date("d", strtotime($event['fin']))) {
                                        $study = $study + 1;
                                        if (($nboccurence == 0 || $nbevents == 20)) {
                                            if ($nbevents == 20) {
                                                $nbevents = 0;
                                                $string .= '</tbody>
                                           </table>
                                           </div>';
                                            }
                                            $string .= '
                                            <div class="table-responsive">
                                                <table class="table tabSchedule">
                                                    <thead class="headerTab">
                                                        <tr>
                                                            <th scope="col" class="text-light text-center">Horaire</th>';
                                            if (!in_array("technicien", $current_user->roles)) {
                                                //Ancienne val
                                                //width="20%"
                                                //width="35%"
                                                //width="25%"
                                                //width="20%"
                                                $string .= '<th scope="col" class="text-light text-center" >Cours</th>
                                                <th scope="col" class="text-light text-center">Groupe/Enseignant</th>';
                                            }
                                            $string .= '
                                                <th scope="col" class="text-light text-center">Salle</th>
                                            </tr>
                                            </thead>
                                            <tbody>';
                                        }

                                        ++$nboccurence;
                                        // et on supprime cours qui ont déja eu lieu
                                        $heure = date("H:i");
                                        if (!(date("H:i", strtotime($event['fin'])) <= $heure)) {
                                            //Si le cours est en vigueur
                                            if (date("H:i", strtotime($event['deb'])) <= $heure && $heure < date("H:i", strtotime($event['fin']))) {
                                                ++$nbevents;
                                                $string .= '<tr class="table-success" scope="row">';
                                            } else if (date("H:i", strtotime($event['deb'])) > $heure) {
                                                ++$nbevents;
                                                $string .= '<tr scope="row">';
                                            }
                                            if ($time == 'all-day') {
                                                if (!$all_day_indicator_shown) {
                                                    $string .= '<td class="all-day-indicator">';
                                                    _e('All Day', 'R34ICS');
                                                    $string .= '</td>';
                                                    $all_day_indicator_shown = true;
                                                }
                                                $string .= '<td class="event">
                                            <span class="title">';
                                                $string .= str_replace('/', '/<wbr />', $event['label']) . '</span>';
                                                if (!empty($event['sublabel'])) {
                                                    $string .= '<span class="sublabel">';
                                                    $string .= str_replace('/()', '/<wbr />', $event['sublabel']) . '</span>';
                                                }
                                                $string .= '</td>';
                                            } else {
                                                if (!empty($event['start'])) {
                                                    //width="20%"
                                                    $string .= '<td class="text-center">';
                                                    $deb = date("H:i", strtotime($event['deb']));
                                                    $newDeb = str_replace(':', 'h', $deb);
                                                    $string .= $newDeb . ' ';
                                                    if (!empty($event['end'])) {
                                                        $string .= '<span class="time">&#8211;';
                                                        $fin = date("H:i", strtotime($event['fin']));
                                                        $newFin = str_replace(':', 'h', $fin);
                                                        $string .= ' ' . $newFin . '</span>';
                                                        $string .= '<!--' . date("d") . '-->';
                                                        $string .= '<!--' . date("d", strtotime($event['fin'])) . ' -->';
                                                    }
                                                    $string .= '</td>';
                                                }
                                                if (!in_array("technicien", $current_user->roles)) {
                                                    $oldEvent = $event['label'];
                                                    $subEvent = substr($oldEvent, -3);
                                                    if ($subEvent == "alt") {
                                                        $oldEvent = substr($oldEvent, 0, -3);
                                                    }
                                                    //width="35%"
                                                    $string .= '<td class="text-center">
                                                    <span class="title">';
                                                    $string .= str_replace('/', '/<wbr />', $oldEvent) . '</span>';

                                                    if (!empty($event['sublabel'])) {
                                                        $string .= '<span class="sublabel">';
                                                        if (empty($event['start']) && !empty($event['end'])) {
                                                            $string .= '<span class="carryover">&#10554;</span>';
                                                        }
                                                        $string .= str_replace('/', '/<wbr />', $event['sublabel']) . '</span>';
                                                    }
                                                    //width="25%"
                                                    $string .= '</td>
                                                <td class="text-center">
                                                        <span class="sublabel">';
                                                    $des = $event['description'];
                                                    $des = substr($des, 0, -29);
                                                    $string .= $des . '</span>
                                                </td >';
                                                }

                                                //width="20%"
                                                $string .= '
                                        <td class="text-center">
                                            <span>';
                                                $string .= str_replace('/', '/<wbr />', $event['location']) . '</span>
                                        </td>';
                                            }
                                            $string .= '</tr>';
                                        }
                                        if ($nbevents == 8) {
                                            break(2);
                                        }
                                    }
                                }
                            }
                            $string .= '</tbody>
                        </table>
                        </div>';
                            break(3);
                        }
                        $string .= '</div>';
                    }
                }
            }
            if ($study == 0) {
                return $string . '<p> Vous n\'avez pas cours !</p>';
            }
        }
        if (in_array("technicien", $current_user->roles)) {
            $string .= '</div>';
        }
        return $string;
    }
}