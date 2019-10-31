<?php
/**
 * Created by PhpStorm.
 * UserView: Léa Arnaud
 * Date: 17/04/2019
 * Time: 11:35
 */

class InformationView extends ViewG
{

    public function tabHeadInformation(){
        $tab = ["Titre","Auteur","Contenu","Date de création","Date de fin"];
        return $this->displayStartTab('info', $tab);
    } //tabHeadInformation()



    public function displayAllInformation($id, $title, $author, $content, $type, $creationDate, $endDate, $row) {
        $page = get_page_by_title( 'Modification information');
        $linkModifyInfo = get_permalink($page->ID);
        $tab = [$title, $author, $content, $creationDate, $endDate];
        $string = $this->displayAll($row, 'info',$id, $tab);
        if($type == 'tab'){
            $source = $_SERVER['DOCUMENT_ROOT'].TV_UPLOAD_PATH.$content;
            if(! file_exists($source)) {
                $string .= '<td class="text-center red"> Le ficier n\'exite pas';
            } else {
                $string .= '<td class="text-center">';
            }
        } else {
            if ($type == 'img') {
                $source = explode('src=', $content);
                $source = substr($source[1], 0, -1);
                $source = substr($source, 1, -1);
                $source = home_url() . $source;
                if (! @getimagesize($source)) {
                    $string .= '<td class="text-center red"> Le fichier n\'existe pas ';
                } else {
                    $string .= '<td class="text-center">';
                }
            } else {
                $string .= '<td class="text-center">';
            }
        }
        $string .= '
               <a href="'.$linkModifyInfo. $id . '" 
              name="modifetud" type="submit" value="Modifier">Modifier</a></td>
            </tr>';
        return $string;
    } // displayAllInformation()


    /**
     * Affiche les informations sur la page principal avec un carousel
     * @param $title
     * @param $content
     * @param $types
     */

    public function displayInformationView($title, $content, $types) {
        $current_user = wp_get_current_user();
        $cpt = 0;

        if(in_array("pdf", $types)) {
            $myclass = "info_pdf";
        } else if (in_array("img", $types)) {
            $myclass = "info_img";
        } else {
            $myclass = "info_txt";
        }

        echo '<li id="information_carousel"'; if(in_array("television", $current_user->roles)) echo 'class="tv"'; echo '>';
        echo '<section id="demo" class="carousel slide" data-ride="carousel" data-interval="10000">
                <article class="carousel-inner">';
                for($i=0; $i < sizeof($title); ++$i) {
                    $var = ($cpt == 0) ? ' active">' : '">';
                    echo '<div class="carousel-item' . $var.'
                            <h2 class="titleInfo">'.$title[$i].'</h2>';
                            if($types[$i] == 'pdf') {
                                echo do_shortcode($content[$i]);
                            } else {
                                echo '<p class="content_info '.$myclass.'">'.$content[$i].'</p>';
                            }
                    echo '</div>';
                    $cpt++;
                }
        echo'    </article>
               </section>
             </li>';
    } //displayInformationView()

    public function displayFormText() {
        $page = get_page_by_title( 'Gérer les informations');
        $linkManageInfo = get_permalink($page->ID);
        $dateMin = date('Y-m-d', strtotime("+1 day"));
        return '
            <form class="cadre" method="post">
                <h2>Information avec du texte</h2>
                <label for="titleInfo">Titre</label>
                <input id="titleInfo" type="text" name="titleInfo" placeholder="Inserer un titre" required maxlength="20">
                <label for="endDateInfo">Date d\'expiration</label>
                <input id="endDateInfo" type="date" name="endDateInfo" min="' . $dateMin . '" required >
                <label for="contentInfo">Contenu</label>
                <textarea id="contentInfo" name="contentInfo" maxlength="200"></textarea>
                <input type="submit" value="creer" name="createText">
            </form>';
    }

    public function displayFormImg() {
        $page = get_page_by_title( 'Gérer les informations');
        $linkManageInfo = get_permalink($page->ID);
        $dateMin = date('Y-m-d', strtotime("+1 day"));
        return '
                    <form class="cadre" method="post" enctype="multipart/form-data">
                        <h2>Information avec une image</h2>
                        <label for="titleInfo">Titre</label>
                        <input id="titleInfo" type="text" name="titleInfo" placeholder="Inserer un titre" required maxlength="20">
                        <label for="endDateInfo">Date d\'expiration</label>
                        <input id="endDateInfo" type="date" name="endDateInfo" min="' . $dateMin . '" required >
                        <label for="contentFile">Ajouter une image</label>
                        <input id="contentFile" type="file" name="contentFile" />
                        <input type="hidden" name="MAX_FILE_SIZE" value="5000000" />
                        <input type="submit" value="creer" name="createImg">
                    </form>';
    }

    public function displayFormTab() {
        $page = get_page_by_title( 'Gérer les informations');
        $linkManageInfo = get_permalink($page->ID);
        $dateMin = date('Y-m-d', strtotime("+1 day"));
        return '
            <form class="cadre" method="post" enctype="multipart/form-data">
                <h2>Information avec un tableau</h2>
                <label for="titleInfo">Titre</label>
                <input id="titleInfo" type="text" name="titleInfo" placeholder="Inserer un titre" required maxlength="20">
                <label for="endDateInfo">Date d\'expiration</label>
                <input id="endDateInfo" type="date" name="endDateInfo" min="' . $dateMin . '" required ></br>
                <label for="contentFile">Ajout du fichier Xls (ou xlsx)</label>
                <input id="contentFile" type="file" name="contentFile" />
                <input type="hidden" name="MAX_FILE_SIZE" value="5000000" />
                <input type="submit" value="creer" name="createTab">
            </form>
            <p>Nous vous conseillons de ne pas dépasser trois colonnes.</p>
            <p>Nous vous conseillons également de ne pas mettre trop de contenu dans une cellule.</p>';
    }

    /**
     * Form pour créer une information sous pdf
     * @return string
     */
    public function displayFormPDF() {
        $dateMin = date('Y-m-d', strtotime("+1 day"));
        return '
            <form class="cadre" method="post" enctype="multipart/form-data">
                <h2>Information avec un pdf</h2>
                <label for="titleInfo">Titre</label>
                <input id="titleInfo" type="text" name="titleInfo" placeholder="Inserer un titre" required maxlength="20">
                <label>Date d\'expiration</label>
                <input type="date" name="endDateInfo" min="' . $dateMin . '" required>
                <label>Ajout du fichier PDF</label>
                <input type="file" name="contentFile" />
                <input type="hidden" name="MAX_FILE_SIZE" value="5000000" />
                <input type="submit" value="creer" name="createPDF">
            </form>';
    }

    public function displayModifyInformationForm($title, $content, $endDate, $typeInfo)
    {
        $page = get_page_by_title( 'Gérer les informations');
        $linkManageInfo = get_permalink($page->ID);
        $dateMin = date('Y-m-d', strtotime("+1 day"));
        if ($typeInfo == "text") {
            return '
                 <form id="modify_info" method="post">
                    <label for="titleInfo">Titre</label>
                    <input id="titleInfo" type="text" name="titleInfo" value="' . $title . '" required maxlength="20">
                    <label for="contentInfo">Contenu</label>
                    <textarea id="contentInfo" name="contentInfo" maxlength="200">' . $content . '</textarea>
                    <label for="endDateInfo">Date d\'expiration</label>
                    <input id="endDateInfo" type="date" name="endDateInfo" min="' . $dateMin . '" value = "' . $endDate . '" required >
                    <input type="submit" name="validateChange" value="Modifier" ">
                 </form>';
        } elseif ($typeInfo == "img") {
            return '
                    <form id="modify_info" method="post" enctype="multipart/form-data">
                        <label for="titleInfo">Titre</label>
                        <input id="titleInfo" type="text" name="titleInfo" value="' . $title . '" required maxlength="20">
                        <figure>
                          ' . $content . ' 
                          <figcaption>' . $title . '</figcaption>
                        </figure>
                        <label for="contentFile">Changer l\'image</label>
                        <input id="contentFile" type="file" name="contentFile" />
                        <input type="hidden" name="MAX_FILE_SIZE" value="5000000" />
                        <label for="endDateInfo">Date d\'expiration</label>
                        <input id="endDateInfo" type="date" name="endDateInfo" min="' . $dateMin . '" value = "' . $endDate . '" required > </br>
                        <input type="submit" name="validateChangeImg" value="Modifier"/>
                    </form>';
        } elseif ($typeInfo == "tab") {
            return '
                    <form id="modify_info" method="post" enctype="multipart/form-data">
                        <label for="titleInfo">Titre</label>
                        <input type="text" name="titleInfo" value="' . $title . '" required maxlength="20">
                        ' . $content . '
                        <label for="contentFile">Modifier le fichier</label>
                        <input id="contentFile" type="file" name="contentFile" />
                        <input type="hidden" name="MAX_FILE_SIZE" value="5000000" />
                        <label for="endDateInfo">Date d\'expiration</label>
                        <input id="endDateInfo" type="date" name="endDateInfo" min="' . $dateMin . '" value = "' . $endDate . '" required > </br>
                        <input type="submit" name="validateChangeTab" value="Modifier"/>
                    </form>';
        }elseif ($typeInfo == "pdf") {
            return '
                    <form id="modify_info" method="post" enctype="multipart/form-data">
                        <label for="titleInfo">Titre</label>
                        <input id="titleInfo" type="text" name="titleInfo" value="' . $title . '" required maxlength="20">' . $content . '
                        <label for="contentFile">Modifier le fichier</label>
                        <input id="contentFile" type="file" name="contentFile" />
                        <label for="endDateInfo">Date d\'expiration </label>
                        <input id="endDateInfo" type="date" name="endDateInfo" min="' . $dateMin . '" value = "' . $endDate . '" required > </br>
                        <input type="submit" name="validateChangePDF" value="Modifier"/>
                    </form>';
        } else {
            return '<p>Désolé, une erreur semble être survenue.</p>';
        }
    } //displayModifyInformationForm()


    /**
     * Affiche un modal qui signal que l'inscription a été validé
     */
    public function displayCreateValidate() {
        $page = get_page_by_title( 'Gérer les informations');
        $linkManageInfo = get_permalink($page->ID);
        $this->displayStartModal("Ajout d'information validé");
        echo '<p class="alert alert-success"> L\'information a été ajoutée </p>';
        $this->displayEndModal($linkManageInfo);
    }

    /**
     * Affiche un message de validation dans un modal lorsque une information est modifiée
     * Redirige à la gestion des informations
     */
    public function displayModifyValidate() {
        $page = get_page_by_title( 'Gérer les informations');
        $linkManageInfo = get_permalink($page->ID);
        $this->displayStartModal("Modification d'information validée");
        echo '<p class="alert alert-success"> L\'information a été modifiée </p>';
        $this->displayEndModal($linkManageInfo);
    }
}