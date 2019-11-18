<?php
/**
 * Created by PhpStorm.
 * UserView: SFW
 * Date: 06/05/2019
 * Time: 11:01
 */

class AlertView extends ViewG
{

    /**
     * Affiche un sélecteur des différents groupes enregistrés
     * @param $years        array Liste des années enregistrées
     * @param $groups       array Liste des groupes enregistrés
     * @param $halfgroups   array Liste des demi groupes enregistrés
     * @return string
     */
    public function displaySelect($years, $groups, $halfgroups)
    {
        $string = '<option value="0">Aucun</option>
                   <option value="all">Tous</option>
                   <optgroup label="Année">';
        if (is_array($years)) {
            foreach ($years as $year) {

                $string .= '<option value="' . $year['code'] . '">' . $year['title'] . '</option >';
            }
        } else {
            $string .= '<option value="' . $years['code'] . '">' . $years['title'] . '</option >';
        }

        $string .= '</optgroup>
                    <optgroup label="Groupe">';
        if (is_array($groups)) {
            foreach ($groups as $group) {
                $string .= '<option value="' . $group['code'] . '">' . $group['title'] . '</option>';
            }
        } else {
            $string .= '<option value="' . $groups['code'] . '">' . $groups['title'] . '</option>';
        }

        $string .= '</optgroup>
                    <optgroup label="Demi groupe">';
        if (is_array($halfgroups)) {
            foreach ($halfgroups as $halfgroup) {
                $string .= '<option value="' . $halfgroup['code'] . '">' . $halfgroup['title'] . '</option>';
            }
        } else {
            $string .= '<option value="' . $halfgroups['code'] . '">' . $halfgroups['title'] . '</option>';
        }

        $string .= '</optgroup>
        </select>';
        return $string;
    }

    /**
     * Affiche un sélecte qui présélectionne le titre enregistré de l'alerte
     * @param $years        array Liste des années
     * @param $groups       array Liste des groupes
     * @param $halfgroups   array Liste des demi groupes
     * @param $name         string titre du groupe enregistré
     * @return string
     */
    public function displaySelectModif($years, $groups, $halfgroups, $name)
    {
        $selected = $name;
        $string = '<option value="0">Aucun</option>
                   <option value="all"';
        if ('all' == $selected) $string .= "selected";
        $string .= '> Tous</option>  
                   <optgroup label="Année">';

        if (is_array($years)) {
            foreach ($years as $year) {
                $string .= '<option value="' . $year['code'] . '" ';
                if ($year['code'] == $selected) $string .= "selected";
                $string .= '>' . $year['title'] . '</option >';
            }
        } else {
            $string .= '<option value="' . $years['code'] . '" ';
            if ($years['code'] == $selected) $string .= "selected";
            $string .= '>' . $years['title'] . '</option >';
        }

        $string .= '</optgroup>
                    <optgroup label="Groupe">';

        if (is_array($groups)) {
            foreach ($groups as $group) {
                $string .= '<option value="' . $group['code'] . '"';
                if ($group['code'] == $selected) $string .= "selected";
                $string .= '>' . $group['title'] . '</option>';
            }
        } else {
            $string .= '<option value="' . $groups['code'] . '"';
            if ($groups['code'] == $selected) $string .= "selected";
            $string .= '>' . $groups['title'] . '</option>';
        }

        $string .= '</optgroup>
                          <optgroup label="Demi groupe">';

        if (is_array($halfgroups)) {
            foreach ($halfgroups as $halfgroup) {
                $string .= '<option value="' . $halfgroup['code'] . '" ';
                if ($halfgroup['code'] == $selected) $string .= "selected";
                $string .= '>' . $halfgroup['title'] . '</option>';
            }
        } else {
            $string .= '<option value="' . $halfgroups['code'] . '" ';
            if ($halfgroups['code'] == $selected) $string .= "selected";
            $string .= '>' . $halfgroups['title'] . '</option>';
        }

        $string .= '</optgroup>
        </select>';
        return $string;
    }

    /**
     * Affiche le formulaire de création d'une alerte
     * @param $years        array Liste des années
     * @param $groups       array Liste des groupes
     * @param $halfgroups   array Liste des demi groupes
     * @return string
     */
    public function displayAlertCreationForm($years, $groups, $halfgroups)
    {
        $dateMin = date('Y-m-d', strtotime("+1 day")); //date minimum pour la date d'expiration

        return '
            <form id="creationAlert" method="post">
                <label for="content">Contenu</label>
                <input id="content" type="text" name="content" required maxlength="280">
                <label for="endDateAlert">Date d\'expiration</label>
                <input id="endDateAlert" type="date" name="endDateAlert" min="' . $dateMin . '" required >
                <label for="selectAlert">Année, groupe, demi-groupes concernés</label>
                <select id="selectAlert" class="form-control firstSelect" name="selectAlert[]" required="">
                    ' . $this->displaySelect($years, $groups, $halfgroups) . '
                <input type="button" onclick="addButtonAlert()" value="+">
                <input type="submit" value="Publier" name="createAlert">
            </form>';
    } //displayCreationForm();

    /**
     * Set the head of the table for the alert's management page.
     */
    public function tabHeadAlert()
    {
        $tab = ["Auteur", "Contenu", "Date de création", "Date de fin"];
        return $this->displayStartTab('alert', $tab);
    }//tabHeadAlert();

    /**
     * Affiche toutes les alertes enregistrées
     * @param $id               int id
     * @param $author           string login de l'auteur
     * @param $content          string texte de l'alerte
     * @param $creationDate     string date de création de l'alerte
     * @param $endDate          string date d'expiration de l'alerte
     * @param $row              int numéro de la ligne
     * @return string
     */
    public function displayAllAlert($id, $author, $content, $creationDate, $endDate, $row)
    {
        $page = get_page_by_title('Modification alerte');
        $linkManageAlert = get_permalink($page->ID);
        $tab = [$author, $content, $creationDate, $endDate];
        return $this->displayAll($row, 'alert', $id, $tab) . '
          <td class="text-center"> <a href="' . $linkManageAlert . $id . '" name="modifetud" type="submit" value="Modifier">Modifier</a></td>
        </td>';
    } //displayAllAlert()


    /**
     * Affiche le formulaire de modification d'alerte
     * @param $result       array Alerte actuelle
     * @param $years        array Liste des années
     * @param $groups       array Liste des groupes
     * @param $halfgroups   array Liste des demi groupes
     * @return string
     */
    public function displayModifyAlertForm($result, $years, $groups, $halfgroups)
    {
        $content = $result['text'];
        $endDate = date('Y-m-d', strtotime($result['end_date']));
        $dateMin = date('Y-m-d', strtotime("+1 day"));

        $page = get_page_by_title('Gestion des alertes');
        $linkManageAlert = get_permalink($page->ID);

        $codes = unserialize($result['codes']);

        $count = 0;
        $string = '
                    <form id="modify_alert" method="post">
                        <label for="contentInfo">Contenu</label>
                        <input id="contentInfo" type="text" name="contentInfo" value="' . $content . '" maxlength="280">
                        <label for="endDateInfo">Date d\'expiration</label>
                        <input id="endDateInfo" type="date" name="endDateInfo" min="' . $dateMin . '" value = "' . $endDate . '" required >';
        if (is_array($codes)) {
            foreach ($codes as $code) {
                $count = $count + 1;
                if ($count == 1) {
                    $string .= '<select class="form-control firstSelect" name="selectAlert[]" id="selectId' . $count . '">' .
                        $this->displaySelectModif($years, $groups, $halfgroups, $code);
                } else {
                    $string .= '
                        <div class="row">
                            <select class="form-control select" name="selectAlert[]" id="selectId' . $count . '">' .
                        $this->displaySelectModif($years, $groups, $halfgroups, $code)
                        . '<input type="button" id="selectId' . $count . '" onclick="deleteRowAlert(this.id)" class="selectbtn" value="Supprimer">
                        </div>';
                }
            }
        } else {
            $string .= '<select class="form-control firstSelect" name="selectAlert[]" id="selectId' . $count . '">' .
                $this->displaySelectModif($years, $groups, $halfgroups, $codes);
        }

        $string .= '<input type="button" onclick="addButtonAlert()" value="+">    
                    <input type="submit" name="validateChange" value="Valider" ">
                    <a href="' . $linkManageAlert . '">Annuler</a>
                 </form>';

        return $string;
    } //displayModifyAlertForm()

    /**
     * Affiche les alertes
     * @param $content      string texte de l'alerte
     */
    public function displayAlertMain($content)
    {
        echo '
        <div class="alerts" id="alert">
             <div class="ti_wrapper">
                <div class="ti_slide">
                    <div class="ti_content">';
        for ($i = 0; $i < sizeof($content); ++$i) {
            echo '<div class="ti_news"><span>' . $content[$i] . '</span> </div>';
        }
        echo '
                    </div>
                </div>
            </div>
        </div>
        ';
    } //displayAlertMain()

    /**
     * Affiche un message de validation d'ajout d'alerte dans un modal
     * Ce modal redirige ensuite à la page de gestion des alertes
     */
    public function displayAddValidate()
    {
        $page = get_page_by_title('Gérer les alertes');
        $linkManageAlert = get_permalink($page->ID);
        $this->displayStartModal("Ajout d'alerte");
        echo '<div class="alert alert-success"> Votre alerte a été envoyée ! </div>';
        $this->displayEndModal($linkManageAlert);
    }

    /**
     * Affiche un message de validation de modification dans un modal
     * Ce modal redirige ensuite à la page de gestion des alertes
     */
    public function displayModifyValidate()
    {
        $page = get_page_by_title('Gérer les alertes');
        $linkManageAlert = get_permalink($page->ID);
        $this->displayStartModal("Ajout d'alerte");
        echo '<div class="alert alert-success"> Votre alerte a été modifiée ! </div>';
        $this->displayEndModal($linkManageAlert);
    }

}