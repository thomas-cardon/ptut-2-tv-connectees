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

	/**
	 * Display a table, showing all element from a database
	 *
	 * @param $name
	 * @param $title
	 * @param $dataHeader
	 * @param $dataList
	 * @param string $idTable
	 *
	 * @return string
	 */
	public function displayAll($name, $title, $dataHeader, $dataList, $idTable = '')
	{
		$name = '\''.$name.'\'';
		$table = '
		<input type="text" id="key'.$idTable.'" name="key" onkeyup="search(\''.$idTable.'\')" placeholder="Search">
		<form method="post">
			<h2>' . $title . '</h2>
			<div class="table-responsive">
				<table class="table" id="table'.$idTable.'">
					<thead>
						<tr class="text-center">
							<th width="5%" class="text-center">#</th>
		                    <th scope="col" width="5%" class="text-center"><input type="checkbox" onClick="toggle(this, ' . $name . ')" /></th>';

		$count = 0;
		foreach ($dataHeader as $data) {
			++$count;
			$table .= '<th scope="col" class="text-center" onclick="sortTable('.$count.', \''.$idTable.'\')">'.$data.'</th>';
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

	/**
	 * Create a link for modify an element
	 *
	 * @param $link
	 *
	 * @return string
	 */
	public function buildLinkForModify($link)
	{
		return '<a href="' . $link . '">Modifier</a>';
	}

	/**
	 * Create a checkbox
	 *
	 * @param $name
	 * @param $id
	 *
	 * @return string
	 */
	public function buildCheckbox($name, $id)
	{
		return '<input type="checkbox" name="checkboxstatus' . $name . '[]" value="' . $id . '"/>';
	}

    /**
     * Create the begin of a multi select
     *
     * @return string
     */
    public function displayStartMultiSelect()
    {
        return '<nav>
          <div class="nav nav-tabs" id="nav-tab" role="tablist">';
    }

    /**
     * Create one tab for the multi select
     *
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
     * Close the creation of new tab
     *
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
     * Create the content for one tab
     *
     * @param $id           string
     * @param $content      string
     * @param $active       bool
     *
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
     * Refresh the page
     */
    public function refreshPage()
    {
        echo '<meta http-equiv="refresh" content="0">';
    }

    /**
     * Create the beginning of a modal
     *
     * @param $title    string
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
     * End of the modal
     *
     * @param null $redirect
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
     * Display a message if an user already exist
     *
     * @param $doubles  array de login
     */
    public function displayErrorDouble($doubles)
    {
        $this->displayStartModal('Erreur durant l\'incription ');
        foreach ($doubles as $double) {
            echo '<p class="alert alert-danger">'.$double. ' a rencontré un problème lors de l\'enregistrement, vérifié son login et son email ! </p>';
        }
        $this->displayEndModal();
    }

    /**
     * Display a message if the inscription is a success
     */
    public function displayInsertValidate()
    {
        $this->displayStartModal('Inscription validée');
        echo "<p class='alert alert-success'>Votre inscription a été validée. </p>";
        $this->displayEndModal();
    }

    /**
     * Display a message if the extension of the file is wrong
     */
    public function displayWrongExtension()
    {
        $this->displayStartModal('Mauvais fichier !');
        echo '<p class="alert alert-danger"> Mauvaise extension de fichier ! </p>';
        $this->displayEndModal();
    }

    /**
     * Display a message if the file isn't a good file
     */
    public function displayWrongFile()
    {
        $this->displayStartModal('Mauvais fichier !');
        echo '<p class="alert alert-danger"> Vous utilisez un mauvais fichier excel / ou vous avez changé le nom des colonnes </p>';
        $this->displayEndModal();
    }

    /**
     * Display a message if the modification is a success
     */
    public function displayModificationValidate($redirect = null)
    {
        $this->displayStartModal('Modification réussie');
        echo '<p class="alert alert-success"> La modification a été appliquée </p>';
        $this->displayEndModal($redirect);
    }

    /**
     * Display a message if the creation of an user has failed
     */
    public function displayErrorInsertion()
    {
        $this->displayStartModal('Erreur lors de l\'inscription ');
        echo '<p class="alert alert-danger"> Le login ou l\'adresse mail est déjà utilisé(e) </p>';
        $this->displayEndModal();
    }

    /**
     * Close a div
     *
     * @return string
     */
    public function displayEndDiv()
    {
        return '</div>';
    }

    /**
     * Display a message if the two password are different
     */
    public function displayBadPassword()
    {
        $this->displayStartModal("Mauvais mot de passe");
        echo "<p class='alert alert-danger'>Les deux mots de passe ne sont pas correctes </p>";
        $this->displayEndModal();
    }
}