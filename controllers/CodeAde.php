<?php
/**
 * Created by PhpStorm.
 * UserView: Rohrb
 * Date: 29/04/2019
 * Time: 09:53
 * Ce controller permet de créer/modifier/supprimer des codes ADE
 */

class CodeAde extends ControllerG {
    /**
     * View of CodeAde
     * @var CodeAdeView
     */
    private $view;

    /**
     * Model of CodeAde
     * @var CodeAdeModel
     */
    private $model;

    /**
     * Constructor of CodeAde.
     */
    public function __construct() {
        $this->view = new CodeAdeView();
        $this->model = new CodeAdeModel();
    }

    /**
     * Insert a code Ade in the database and upload the schedule of this code
     */
    public function insertCode() {
        $action = $_POST['addCode'];
        $code = filter_input(INPUT_POST, 'codeAde');
        $title = filter_input(INPUT_POST, 'titleAde');
        $type = filter_input(INPUT_POST, 'typeCode');

        if ($action == "Valider") {
        	$this->model->setType($type);
        	$this->model->setTitle($title);
        	$this->model->setCode($code);

            if ($this->model->insertCode()) {
                $this->addFile($code);
                // @TODO Message validate creation
                $this->view->refreshPage();
            } else {
                $this->view->displayErrorDoubleCode();
            }
        }
	    $badCodesYears = $this->model->codeNotBound(0);
	    $badCodesGroups = $this->model->codeNotBound(1);
	    $badCodesHalfGroups = $this->model->codeNotBound(2);
	    $badCodes = [$badCodesYears, $badCodesGroups, $badCodesHalfGroups];

	    $string = "";
	    if(sizeof($badCodesYears) < 1 || sizeof($badCodesGroups) < 1 || sizeof($badCodesHalfGroups) < 1){
		    $string .= $this->view->displayUnregisteredCode($badCodes);
	    }
	    return $this->view->displayFormAddCode().$string;
    }

	/**
	 * Modify code Ade
	 */
	public function modifyCode() {
		$result = $codeAde = $this->model->getCodeAde($this->getMyIdUrl());

		$action = $_POST['modifCodeValid'];
		// Take new value
		$title = filter_input(INPUT_POST, 'modifTitle');
		$code = filter_input(INPUT_POST, 'modifCode');
		$type = filter_input(INPUT_POST, 'modifType');

		if ($action == "Valider") {
			// Set new value
			$codeAde->setTitle($title);
			$codeAde->setCode($code);
			$codeAde->setType($type);

			if ($codeAde->modifyCodeAde()) {
				// @TODO Message Validation
				if ($result->getCode() != $code) {
					$this->addFile($code);
				}
				$this->view->refreshPage();
			} else {
				$this->view->displayErrorDoubleCode();
			}
		}
		return $this->view->displayModifyCode($codeAde->getTitle(), $codeAde->getType(), $codeAde->getCode());
	}

	/**
	 * Display all codes Ade in a table
	 *
	 * @return string
	 */
    public function displayAllCodes() {
        $years = $this->model->getCodeAdeListType("Annee");
        $groups = $this->model->getCodeAdeListType("Groupe");
        $halfGroups = $this->model->getCodeAdeListType("Demi-groupe");
        $string = $this->view->displayTableHeadCode();
        $row = 0;
        if (isset($years[0])) {
            foreach ($years as $year) {
                $row = $row + 1;
                $string .= $this->view->displayAllCode($year->getId(), $year->getTitle(), $year->getType(), $year->getCode(), $row);
            }
        }
        if (isset($groups[0])) {
            foreach ($groups as $group) {
                $row = $row + 1;
                $string .= $this->view->displayAllCode($group->getId(), $group->getTitle(), $group->getType(), $group->getCode(), $row);
            }
        }
        if (isset($halfGroups[0])) {
            foreach ($halfGroups as $halfGroup) {
                $row = $row + 1;
                $string .= $this->view->displayAllCode($halfGroup->getId(), $halfGroup->getTitle(), $halfGroup->getType(), $halfGroup->getCode(), $row);
            }
        }
        $string .= $this->view->displayEndTab();
        return $string;
    }

    /**
     * Delete codes
     */
    public function deleteCodes() {
        $actionDelete = $_POST['Delete'];
        if (isset($actionDelete)) {
            if (isset($_REQUEST['checkboxstatuscode'])) {
                $checked_values = $_REQUEST['checkboxstatuscode'];
                foreach ($checked_values as $val) {
	                $this->model = $this->model->getCodeAde($val);
                    $this->model->deleteCode();
                    $this->view->refreshPage();
                }
            }
        }
    }
}