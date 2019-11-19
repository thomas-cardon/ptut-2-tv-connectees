<?php

/**
 * Fonction qui est reliée au bloc
 * Affiche l'interface de gestion pour l'admin
 * @return string Return la vue du formulaire
 */
function admininterface_render_callback()
{
    if (is_page()) {
        global $wpdb;
        $table = 'ecran_modification';
        $results = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM $table WHERE title = 'column'")
        );
        $col = $results[0]->content;
        $message = $results = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM $table WHERE title = 'hideNoSchedule'")
        );
        $message = $results[0]->content;
        $validCol = $_POST['columnValid'];
        $validMessage = $_POST['messageValid'];
        $admin = new UserView();
        if ($validCol) {
            $col = $_POST['column'];
            $results = $wpdb->get_results(
                $wpdb->prepare("SELECT * FROM $table WHERE title = 'column'")
            );
            if ($results[0]->content != $message) {
                $data = ['content' => $col];
                $where = ['title' => 'column'];
                if ($wpdb->update($table, $data, $where)) {
                    $admin->displayStartModal('Modification de la page d\'accueil');
                    echo '<p>La modification a été effectuée.</p>';
                    $admin->displayEndModal();
                } else {
                    $admin->displayStartModal('Modification de la page d\'accueil');
                    echo '<p>La modification a rencontrée une erreur.</p>';
                    echo '<p>Veuillez réssailler plus tard.</p>';
                    $admin->displayEndModal();
                }
            } else {
                $admin->displayStartModal('Modification de la page d\'accueil');
                echo '<p>Ce modèle était déjà enregistré</p>';
                $admin->displayEndModal();
            }
        }

        if ($validMessage) {
            $message = $_POST['message'];
            $data = ['content' => $message];
            $where = ['title' => 'hideNoSchedule'];
            $results = $wpdb->get_results(
                $wpdb->prepare("SELECT * FROM $table WHERE title = 'column'")
            );
            if ($results[0]->content != $message) {
                if ($wpdb->update($table, $data, $where)) {
                    $admin->displayStartModal('Modification de la page d\'accueil');
                    echo '<p>La modification a été effectuée.</p>';
                    $admin->displayEndModal();
                } else {
                    $admin->displayStartModal('Modification de la page d\'accueil');
                    echo '<p>La modification a rencontrée une erreur.</p>';
                    echo '<p>Veuillez réssailler plus tard.</p>';
                    $admin->displayEndModal();
                }
            }
        }
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
        </form>
        
        <form class="form-admin" method="post">
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
    } else {
        return null;
    }
}

/**
 * Bloc qui permet de gérer le site
 */
function block_admininterface()
{
    wp_register_script(
        'admininterface-script',
        plugins_url('block.js', __FILE__),
        array('wp-blocks', 'wp-element', 'wp-data')
    );

    register_block_type('tvconnecteeamu/admin-interface', array(
        'editor_script' => 'admininterface-script',
        'render_callback' => 'admininterface_render_callback'
    ));
}

add_action('init', 'block_admininterface');