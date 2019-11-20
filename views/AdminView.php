<?php


class AdminView extends UserView
{
    public function displayFormChangeModel($col)
    {
        $string = '
        <form class="form-admin" method="post">
            <h2>Colonne(s) de la page d\'accueil</h2>
            
            <input class="input-hide" type="radio" id="zeroColumn" name="column" value="none" ';
        if ($col == "none") {
            $string .= 'checked';
        }
        $string .= '>
            <label class="flex-column" for="zeroColumn"><img class="img-border" src="' . TV_PLUG_PATH . 'views/images/Sans colonne.png" alt="Page d\'accueil sans colonne"></label>
    
            <input class="input-hide" type="radio" id="rightColumn" name="column" value="right"';
        if ($col == "right") {
            $string .= 'checked';
        }
        $string .= '>
            <label class="flex-column" for="rightColumn"><img class="img-border" src="' . TV_PLUG_PATH . 'views/images/Colonne droite.png" alt="Page d\'accueil avec une colonne à droite"></label>
   
            <input class="input-hide" type="radio" id="leftColumn" name="column" value="left"';
        if ($col == "left") {
            $string .= 'checked';
        }
        $string .= '>
            <label class="flex-column" for="leftColumn"><img class="img-border" src="' . TV_PLUG_PATH . 'views/images/Colonne gauche.png" alt="Page d\'accueil avec une colonne à gauche"></label>
            
            <input class="input-hide" type="radio" id="twoColumn" name="column" value="two"';
        if ($col == "two") {
            $string .= 'checked';
        }
        $string .= '>
            <label class="flex-column" for="twoColumn"><img class="img-border" src="' . TV_PLUG_PATH . 'views/images/Deux colonnes.png" alt="Page d\'accueil avec une colonne à gauche et à droite"></label>

            <input type="submit" name="columnValid" value="Valider">
        </form>';

        return $string;
    }

    public function displayFormChangeMsgTv($message)
    {
        $string = '<form class="form-admin" method="post">
            <h2>Cachez le texte lorsqu\'il n\'y a pas cours</h2>
            <p>Cela ne vaut seulement pour les comptes télévisions</p>
            <input type="radio" id="WithMessage" name="message" value="true"';
        if ($message == "true") {
            $string .= 'checked';
        }
        $string .= '>
            <label class="flex-column" for="WithMessage"><img class="img-border" src="' . TV_PLUG_PATH . 'views/images/Vous n\'avez pas cours.png" alt="Page d\'accueil avec le message"></label>
    
            <input type="radio" id="noMessage" name="message" value="false"';
        if ($message == "false") {
            $string .= 'checked';
        }
        $string .= '>
            <label class="flex-column" for="noMessage"><img class="img-border" src="' . TV_PLUG_PATH . 'views/images/Rien.png" alt="Page d\'accueil sans le message"></label>

            <input type="submit" name="messageValid" value="Valider">
        </form>';
        return $string;
    }

    public function displayErrorToChange() {
        $this->displayStartModal('Modification de la page d\'accueil');
        echo '<p>La modification a rencontrée une erreur.</p>';
        echo '<p>Veuillez réssailler plus tard.</p>';
        $this->displayEndModal();
    }

    public function displayAlreadyRegister() {
        $this->displayStartModal('Modification de la page d\'accueil');
        echo '<p>Ce modèle était déjà enregistré</p>';
        $this->displayEndModal();
    }
}