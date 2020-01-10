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
     * Vue de CodeAde
     * @var CodeAdeView
     */
    private $view;

    /**
     * Modèle de CodeAde
     * @var CodeAdeManager
     */
    private $model;

    /**
     * Constructeur de CodeAde.
     */
    public function __construct() {
        $this->view = new CodeAdeView();
        $this->model = new CodeAdeManager();
    }

    /**
     * Ajoute le code ADE rentré dans la base de donnée si le code n'est pas déjà enregistré ou même le titre.
     * Si le code est enregistré, on ajoute ensuite le fichier ICS de l'emploi du temps dans le dossier fileICS
     */
    public function insertCode() {
        $action = $_POST['addCode'];
        $code = filter_input(INPUT_POST, 'codeAde');
        $title = filter_input(INPUT_POST, 'titleAde');
        $type = filter_input(INPUT_POST, 'typeCode');

        if ($action == "Valider") {
            if ($this->model->insertCode($type, $title, $code)) {
                $this->addFile($code);
                $this->view->refreshPage();
            } else {
                $this->view->displayErrorDoubleCode();
            }
        }
    }

    /**
     * Affiche tout les codes ADE enregistrés dans un tableau où on peut soit les supprimer soit les modifier
     * Les codes sont ordonnés par ordre de type Année - Groupe - Demi-groupe, puis par ordre alphabétique
     */
    public function displayAllCodes()
    {
        $years = $this->model->getCodeYear();
        $groups = $this->model->getCodeGroup();
        $halfgroups = $this->model->getCodeHalfgroup();
        $string = $this->view->displayTableHeadCode();
        $row = 0;
        if (isset($years[0])) {
            foreach ($years as $year) {
                $row = $row + 1;
                $string .= $this->view->displayAllCode($year, $row);
            }
        }
        if (isset($groups[0])) {
            foreach ($groups as $group) {
                $row = $row + 1;
                $string .= $this->view->displayAllCode($group, $row);
            }
        }
        if (isset($halfgroups[0])) {
            foreach ($halfgroups as $halfgroup) {
                $row = $row + 1;
                $string .= $this->view->displayAllCode($halfgroup, $row);
            }
        }
        $string .= $this->view->displayEndTab();
        return $string;
    }

    /**
     * Supprime tout les codes qui sont sélectionnés
     * @param $action       Bouton de validation
     */
    public function deleteCodes()
    {
        $model = new CodeAdeManager();
        $actionDelete = $_POST['Delete'];
        if (isset($actionDelete)) {
            if (isset($_REQUEST['checkboxstatuscode'])) {
                $checked_values = $_REQUEST['checkboxstatuscode'];
                foreach ($checked_values as $val) {
                    $oldCode = $model->getCode($val);
                    $this->deleteFile($oldCode[0]['code']);
                    $model->deleteCode($val);
                    $this->view->refreshPage();
                }
            }
        }
    }

    /**
     * On récupère l'ID dans l'url puis on modifie le code relié à cet ID
     * Si le code a été modifié, on supprime son fichier ICS et on installe le nouveau
     */
    public function modifyCode()
    {
        $result = $this->model->getCode($this->getMyIdUrl());
        //$this->view->displayModifyCode($result);

        $action = $_POST['modifCodeValid'];
        $title = filter_input(INPUT_POST, 'modifTitle');
        $code = filter_input(INPUT_POST, 'modifCode');
        $type = filter_input(INPUT_POST, 'modifType');

        if ($action == "Valider") {
            if ($this->model->checkModify($result, $this->getMyIdUrl(), $title, $code, $type)) {
                if ($result[0]['code'] != $code) {
                    $this->deleteFile($result[0]['code']);
                    $this->addFile($code);
                }
                $this->view->refreshPage();
            } else {
                $this->view->displayErrorDoubleCode();
            }
        }
    }
}