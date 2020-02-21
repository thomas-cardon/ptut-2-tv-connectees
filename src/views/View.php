<?php

namespace Views;

/**
 * Class View
 *
 * Main class View,
 * got basics functions for all views
 *
 * @package Views
 */
class View
{
	public function displayAll($name, $title, $dataHeader, $dataList)
	{
		$name = '\''.$name.'\'';
		$table = '
		<form method="post">
			<h2>' . $title . '</h2>
			<div class="table-responsive">
				<table class="table">
					<thead>
						<tr class="text-center">
							<th scope="col" width="5%" class="text-center">#</th>
		                    <th scope="col" width="5%" class="text-center"><input type="checkbox" onClick="toggle(this, ' . $name . ')" /></th>';

		foreach ($dataHeader as $data) {
			$table .= '<th scope="col" class="text-center">'.$data.'</th>';
		}

		$table .= '
			</tr>
		</thead>
		<tbody>';

		foreach ($dataList as $data) {
			$table .= '<tr>';
			foreach ($data as $column) {
				$table .= '<td class="text-center">'.$column.'</td>';
			}
			$table .= '</tr>';
		}

		$table .= '
					</tbody>
				</table>
			</div>
	        <input type="submit" value="Supprimer" name="Delete" onclick="return confirm(\' Voulez-vous supprimer le(s) élément(s) sélectionné(s) ?\');"/>
	    </form>';

		return $table;
	}

	public function buildLinkForModify($link)
	{
		return '<a href="' . $link . '">Modifier</a>';
	}

	public function buildCheckbox($name, $id)
	{
		return '<input type="checkbox" name="checkboxstatus' . $name . '[]" value="' . $id . '"/>';
	}

    /**
     * Affiche le début de la sélection multiple
     * @return string
     */
    public function displayStartMultiSelect()
    {
        return '<nav>
          <div class="nav nav-tabs" id="nav-tab" role="tablist">';
    }

    /**
     * Affiche le titre d'un onglet
     * @param $id           string id de l'onglet
     * @param $title        string titre de l'onglet
     * @param $active       bool affiche l'onglet (si c'est à true) lors du chargement de la page
     * @return string
     */
    public function displayTitleSelect($id, $title, $active = false)
    {
        $string = '<a class="nav-item nav-link';
        if ($active) $string .= ' active';
        $string .= '" id="nav-' . $id . '-tab" data-toggle="tab" href="#nav-' . $id . '" role="tab" aria-controls="nav-' . $id . '" aria-selected="false">' . $title . '</a>';
        return $string;
    }

    /**
     * Affiche la fin des titres des onglets
     * @return string
     */
    public function displayEndOfTitle()
    {
        return '
            </div>
        </nav>
        <br/>
        <div class="tab-content" id="nav-tabContent">';
    }

    /**
     * Affiche le contenu d'un onglet
     * @param $id           string id de l'onglet
     * @param $content      string contenu de l'onglet
     * @param $active       bool affiche l'onglet (si c'est à true) lors du chargement de la page
     * @return string
     */
    public function displayContentSelect($id, $content, $active = false)
    {
        $string = '<div class="tab-pane fade show';
        if ($active) $string .= ' active';
        $string .= '" id="nav-' . $id . '" role="tabpanel" aria-labelledby="nav-' . $id . '-tab">' . $content . '</div>';
        return $string;
    }

    /**
     * Relance la page
     */
    public function refreshPage()
    {
        echo '<meta http-equiv="refresh" content="0">';
    }

    /**
     * Affiche les codes non enregistrées dans un tableau
     * @param $badCodes array Codes non enregistrés dans un tableau de trois colonnes Année - Groupe - Demi-groupe
     * @return string   Renvoie le tableau
     */
    public function displayUnregisteredCode($badCodes)
    {
        if (!is_null($badCodes[0]) || !is_null($badCodes[1]) || !is_null($badCodes[2])) {
            $string = '
        <h3> Ces codes ne sont pas encore enregistrés ! </h3>
        <table class="table text-center"> 
                <thead>
                    <tr class="text-center">
                        <th scope="col" width="33%" class="text-center">Année</th>
                        <th scope="col" width="33%" class="text-center">Groupe</th>
                        <th scope="col" width="33%" class="text-center">Demi-Groupe</th>
                        </tr>
                </thead>
                <tbody>';
            if (is_null($badCodes[0])) {
                $sizeYear = 0;
            } else {
                $sizeYear = sizeof($badCodes[0]);
            }
            if (is_null($badCodes[1])) {
                $sizeGroup = 0;
            } else {
                $sizeGroup = sizeof($badCodes[1]);
            }
            if (is_null($badCodes[2])) {
                $sizeHalfgroup = 0;
            } else {
                $sizeHalfgroup = sizeof($badCodes[2]);
            }
            $size = 0;
            if ($sizeYear >= $sizeGroup && $sizeYear >= $sizeHalfgroup) $size = $sizeYear;
            if ($sizeGroup >= $sizeYear && $sizeGroup >= $sizeHalfgroup) $size = $sizeGroup;
            if ($sizeHalfgroup >= $sizeYear && $sizeHalfgroup >= $sizeGroup) $size = $sizeHalfgroup;
            for ($i = 0; $i < $size; ++$i) {
                $string .= '<tr>
                    <td class="text-center">';
                if ($sizeYear > $i)
                    $string .= $badCodes[0][$i];
                else
                    $string .= ' ';
                $string .= '</td>
            <td class="text-center">';
                if ($sizeGroup > $i)
                    $string .= $badCodes[1][$i];
                else
                    $string .= ' ';
                $string .= '</td>
            <td class="text-center">';
                if ($sizeHalfgroup > $i)
                    $string .= $badCodes[2][$i];
                else
                    $string .= ' ';
                $string .= '</td>
                  </tr>';
            }
            $string .= '
                </tbody>
        </table>
        ';
            return $string;
        }
    }

    /**
     * Affiche le début d'un modal
     * @param $title    string Titre du modal
     */
    public function displayStartModal($title)
    {
        echo '
        <div class="modal" id="myModal" tabindex="-1" role="dialog">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">' . $title . '</h5>
              </div>
              <div class="modal-body">';
    }

    /**
     * Fin du modal
     * @param null $redirect Redirection lorsqu'on clique si Fermer
     */
    public function displayEndModal($redirect = null)
    {
        echo '</div>
              <div class="modal-footer">';
        if (empty($redirect)) {
            echo '<button type="button" onclick="closeModal()">Fermer</button>';
        } else {
            echo '<button type="button" onclick="document.location.href =\' ' . $redirect . ' \'">Fermer</button>';
        }
        echo '</div>
            </div>
          </div>
        </div>
        
        <script>
        	$("#myModal").show();
        </script>';
    }

    /**
     * Texte pour dire qu'il y a un test
     */
    public function displayTest()
    {
        echo '<p class="alert alert-danger"> Cette fonctionnalitée est en test ! </p>';
    }

    /**
     * Prévient s'il n'y a pas d'utilisateur du rôle demandé inscrit
     */
    public function displayEmpty()
    {
        return "<p> Il n'y pas d'utilisateur de ce rôle inscrit!</p>";
    }

    /**
     * Affiche un modal signalant les personnes n'ont enregistrés du à un email ou login déjà utilisé
     * @param $doubles  array de login
     */
    public function displayErrorDouble($doubles)
    {
        $this->displayStartModal('Erreur durant l\'incription ');
        foreach ($doubles as $double) {
            echo "<p class='alert alert-danger'>$double a rencontré un problème lors de l'enregistrement, vérifié son login et son email ! </p>";
        }
        $this->displayEndModal();
    }

    /**
     * Affiche un modal, signalant le succès de l'inscription
     */
    public function displayInsertValidate()
    {
        $this->displayStartModal('Inscription validée');
        echo "<p class='alert alert-success'>Votre inscription a été validée. </p>";
        $this->displayEndModal();
    }

    /**
     * Affiche un modal, signalant que le fichier à une mauvaise extension
     */
    public function displayWrongExtension()
    {
        $this->displayStartModal('Mauvais fichier !');
        echo '<p class="alert alert-danger"> Mauvaise extension de fichier ! </p>';
        $this->displayEndModal();
    }

    /**
     * Affiche un modal, signalant que ce n'est pas le bon fichier excel
     */
    public function displayWrongFile()
    {
        $this->displayStartModal('Mauvais fichier !');
        echo '<p class="alert alert-danger"> Vous utilisez un mauvais fichier excel / ou vous avez changé le nom des colonnes </p>';
        $this->displayEndModal();
    }

    /**
     * Affiche un modal signalant que la modification à été réussie
     */
    public function displayModificationValidate($redirect = null)
    {
        $this->displayStartModal('Modification réussie');
        echo '<p class="alert alert-success"> La modification a été appliquée </p>';
        $this->displayEndModal($redirect);
    }

    /**
     * Affiche un modal signalant que l'inscription d'un/des utilisateur(s) a échouée
     */
    public function displayErrorInsertion()
    {
        $this->displayStartModal('Erreur lors de l\'inscription ');
        echo '<p class="alert alert-danger"> Le login ou l\'adresse mail est déjà utilisé(e) </p>';
        $this->displayEndModal();
    }

    /**
     * Ajoute une ligne
     * @return string
     */
    public function displayRow()
    {
        return '<div class="flex-content">';
    }

    /**
     * Ferme une div
     * @return string
     */
    public function displayEndDiv()
    {
        return '</div>';
    }

    /**
     * Affiche un modal qui signal que la confirmation de mot de passe lors de l'inscription a échoué
     */
    public function displayBadPassword()
    {
        $this->displayStartModal("Mauvais mot de passe");
        echo "<p class='alert alert-danger'>Les deux mots de passe ne sont pas correctes </p>";
        $this->displayEndModal();
    }
}