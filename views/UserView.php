<?php


abstract class UserView extends ViewG {

    /**
     * Affiche un formulaire pour ajouter des utilisateurs via fichier Excel
     * @param $name     Nom du rôle à inscrire
     * @return string   Renvoie le formulaire
     */
    protected function displayInsertImportFile($name){
        return '
        <article class="cadre">
            <a href="/wp-content/plugins/TeleConnecteeAmu/models/Excel/addUsers/Ajout '.$name.'s.xlsx"
                download="Ajout '.$name.'s.xlsx">Télécharger le fichier Excel ! </a>
             <form id="'.$name.'" method="post" enctype="multipart/form-data">
				<input type="file" name="excel'.$name.'" class="inpFil" required=""/>
				<button type="submit" name="import'.$name.'" value="Importer">Importer le fichier</button>
			</form>
		</article>';
    }

    /**
     * Affiche un formulaire classique
     * @param $name Nom du rôle à inscrire
     * @return string   Renvoie le formulaire
     */
    protected function displayBaseForm($name) {
        $string = '
            <form method="post" class="cadre">
                <label for="login'.$name.'">Login</label>
                <input minlength="4" type="text" name="login'.$name.'" placeholder="Login" required="">
                <label for="email'.$name.'">Email</label>
                <input type="email" name="email'.$name.'" placeholder="Email" required="">
                <label for="pwd'.$name.'">Mot de passe</label>
                <input minlength="4" type="password" id="pwd'.$name.'" name="pwd'.$name.'" placeholder="Mot de passe" required="" onkeyup=checkPwd("'.$name.'")>
                <input minlength="4" type="password" id="pwdConf'.$name.'" name="pwdConfirm'.$name.'" placeholder="Confirmer le Mot de passe" required="" onkeyup=checkPwd("'.$name.'")>
                <input type="submit" id="valid'.$name.'" name="create'.$name.'">
            </form>';
        return $string;
    }

}