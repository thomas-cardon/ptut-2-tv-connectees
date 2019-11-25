<?php


class ICSView extends ViewG {
	/**
	 * Affiche l'emploi du temps d'un fichier ICS
	 *
	 * @param $ics_data     array Toutes les données du fichier ICS
	 * @param $title        string Titre de l'emploi du temps
	 * @param $count        int|bool
	 *
	 * @return bool         Renvoie vrai s'il y a des données
	 */
	public function displaySchedule( $ics_data, $title, $count = false ) {
		$current_user = wp_get_current_user();
		$string       = '<h1>' . $title . '</h1>';
		if ( isset( $ics_data['events'] ) ) {
			$current_study = 0;
			foreach ( array_keys( (array) $ics_data['events'] ) as $year ) {
				for ( $m = 1; $m <= 12; $m ++ ) {
					$month = $m < 10 ? '0' . $m : '' . $m;
					foreach ( (array) $ics_data['events'][ $year ][ $month ] as $day => $day_events ) {
						if ( in_array( 'television', $current_user->roles ) ) {
							if ( $day == date( 'j' ) ) {
								$string .= $this->displayStartSchedule( $current_user );
							}
						} else {
							$string .= $this->displayStartSchedule( $current_user );
						}
						foreach ( $day_events as $day_event => $events ) {
							foreach ( $events as $event ) {
								if ( in_array( 'television', $current_user->roles ) ) {
									if ( $day == date( 'j' ) ) {
										++ $current_study;
										$string .= $this->getContent( $event );
										if ( $current_study > 9 ) {
											break;
										}
									}
								} elseif ( in_array( 'enseignant', $current_user->roles ) ) {
									++ $current_study;
									$string .= $this->getContent( $event );
									if ( $current_study > 9 ) {
										break;
									}
								} else {
									if ( $day == date( 'j' ) ) {
										++ $current_study;
										$string .= $this->getContent( $event );
									}
								}
							}
						}
						if ( in_array( 'television', $current_user->roles ) ) {
							if ( $day == date( 'j' ) ) {
								$string .= $this->displayEndSchedule();
							}
						} else {
							$string .= $this->displayEndSchedule();
						}
					}
				}
			}
			if ( $current_study < 1 ) {
				return $this->displayNoSchedule( $title, $current_user );
			}
		} else {
			return $this->displayNoSchedule( $title, $current_user );
		}

		return $string;
	}

	/**
	 * Affiche le début de l'emploi du temps
	 *
	 * @param $current_user     WP_User utilisateur connecté
	 *
	 * @return string
	 */
	public function displayStartSchedule( $current_user ) {
		$string = '<div class="table-responsive">
                   	<table class="table tabSchedule">
                    	<thead class="headerTab">
                        	<tr>
                            	<th scope="col" class="text-center">Horaire</th>';
		if ( ! in_array( "technicien", $current_user->roles ) ) {
			$string .= '<th scope="col" class="text-center" >Cours</th>
                        <th scope="col" class="text-center">Groupe/Enseignant</th>';
		}
		$string .= '<th scope="col" class="text-center">Salle</th>
                 </tr>
              </thead>
           <tbody>';

		return $string;
	}

//	public function displayAllSchedule() {
//	if ( date( 'j' ) != $day ) {
//			 $this->displayLigneSchedule( [
//				$duration,
//				$label,
//				$description,
//				$event['location']
//			] );
//		}
//	}

	public function giveDate( $day, $month, $year ) {
		$day_of_week = $day + 1;

		return '<h2>' . date_i18n( 'l j F', mktime( 0, 0, 0, $month, $day_of_week, $year ) ) . '</h2>';
	}

	public function getContent( $event, $day = 0 ) {
		if ( $day == 0 ) {
			$day = date( 'j' );
		}
		$time     = date( "H:i" );
		$duration = str_replace( ':', 'h', date( "H:i", strtotime( $event['deb'] ) ) ) . ' - ' . str_replace( ':', 'h', date( "H:i", strtotime( $event['fin'] ) ) );
		if ( $day == date( 'j' ) ) {
			if ( date( "H:i", strtotime( $event['deb'] ) ) <= $time && $time < date( "H:i", strtotime( $event['fin'] ) ) ) {
				$active = true;
			} else {
				$active = false;
			}
		}
		if ( substr( $event['label'], - 3 ) == "alt" ) {
			$label = substr( $event['label'], 0, - 3 );
		} else {
			$label = $event['label'];
		}
		$description = substr( $event['description'], 0, - 29 );
		if ( ! ( date( "H:i", strtotime( $event['fin'] ) ) <= $time ) ) {

			return $this->displayLigneSchedule( [ $duration, $label, $description, $event['location'] ], $active );
		}

		return '';
	}

	public function displayLigneSchedule( $datas, $active = false ) {
		if ( $active ) {
			$string = '<tr class="table-success" scope="row">';
		} else {
			$string = '<tr scope="row">';
		}
		foreach ( $datas as $data ) {
			$string .= '<td class="text-center">' . $data . '</td>';
		}

		return $string . '</tr>';
	}

	/**
	 * Affiche la fin de l'emploi du temps
	 * @return string
	 */
	public
	function displayEndSchedule() {
		return '</tbody>
             </table>
          </div>';
	}


	/**
	 * Affiche un message qu'il n'y a pas cours
	 *
	 * @param $title            string titre de l'emploi du temps
	 * @param $current_user     WP_User utilisateur connecté
	 *
	 * @return bool|string
	 */
	public
	function displayNoSchedule(
		$title, $current_user
	) {
		if ( get_theme_mod( 'ecran_connecte_schedule_msg', 'show' ) == 'show' && in_array( 'television', $current_user->roles ) ) {
			return '<h1>' . $title . '</h1><p> Vous n\'avez pas cours !</p>';
		} else if ( ! in_array( 'television', $current_user->roles ) ) {
			return '<h1>' . $title . '</h1><p> Vous n\'avez pas cours !</p>';
		} else {
			return false;
		}
	}
}
