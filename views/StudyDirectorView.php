<?php

/**
 * Class StudyDirectorView
 */
class StudyDirectorView extends UserView {

    /**
     * Formulaire pour créer un directeur d'études
     * @return string   Renvoie le formulaire
     */
    public function displayCreateDirector() {
        return '
                <form class="cadre" method="post">
                    <h2> Compte directeur d\'études</h2>
                    <label for="loginDirec">Login</label>
                    <input minlength="4" type="text" class="form-control text-center modal-sm" name="loginDirec" placeholder="Login" required="">
                    <label for="emailDirec">Email</label>
                    <input type="email" class="form-control text-center modal-sm" name="emailDirec" placeholder="Email" required="">
                    <label for="pwdDirec">Mot de passe</label>
                    <input minlength="4" type="password" class="form-control text-center modal-sm" id="pwdDirec" name="pwdDirec" placeholder="Mot de passe" required="" onkeyup=checkPwd("Direc")>
                    <input minlength="4" type="password" class="form-control text-center modal-sm" id="pwdConfDirec" name="pwdConfirmDirec" placeholder="Confirmer le Mot de passe" required="" onkeyup=checkPwd("Direc")>
                    <label for="codeADEDirec"> Code ADE</label>
                    <input type="text" class="form-control text-center modal-sm" placeholder="Code ADE" name="codeDirec" required="">
                    <input type="submit" id="validDirec" name="createDirec" value="Créer">
                </form>';
    }

//    public function displayCreateDirector() {
//        return $this->displayBaseForm('Direc');
//    }

    /**
     * En-tête du tableau des directeurs d'études
     * @return string
     */
    public function displayTabHeadDirector(){
        $tab = ["Numéro Ent", "Code ADE"];
        $title = "Directeurs d'études";
        return $this->displayStartTab('direc', $tab, $title);
    }

    /**
     * Affiche une ligne contenant les données d'un directeur d'études
     * @param $result   array Données du directeur d'études
     * @param $row      int Numéro de ligne
     */
    public function displayAllStudyDirector($result, $row){
        return $this->displayAllTeacher($result, 'direc',$row);
    }

    /**
     * Affiche le formulaire pour modifier un directeur d'études
     * @param $result   array Données de l'enseignant
     */
    public function displayModifyStudyDirector($result){
        return $this->displayModifyTeacher($result);
    }
}