<?php
/**
 * Created by PhpStorm.
 * UserView: Léa Arnaud
 * Date: 17/04/2019
 * Time: 11:35
 */

class ViewInformation extends ViewG
{

    public function tabHeadInformation(){
        $tab = ["Titre","Auteur","Contenu","Date de création","Date de fin"];
        return $this->displayStartTab('info', $tab);
    } //tabHeadInformation()



    public function displayAllInformation($id, $title, $author, $content, $type, $creationDate, $endDate, $row)
    {
        $page = get_page_by_title( 'Modification information');
        $linkModifyInfo = get_permalink($page->ID);
        $tab = [$title, $author, $content, $creationDate, $endDate];
        $string = $this->displayAll($row, 'info',$id, $tab);
        if($type == 'tab'){
            $source = $_SERVER['DOCUMENT_ROOT']."/wp-content/plugins/TeleConnecteeAmu/views/media/".$content;
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
                if(! file_exists($source)) {
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
     */

    public function displayInformationView($title, $content)
    {
        $cpt = 0;
        echo '<div class="container-fluid">
                    <div id="information_carousel">
                        <div id="demo" class="carousel slide" data-ride="carousel" data-interval="10000">
                            
                            <!--The slides -->
                            <div class="carousel-inner">';
                                for($i=0; $i < sizeof($title); ++$i) {
                                    $var = ($cpt == 0) ? ' active">' : '">';
                                    echo '<div class="carousel-item' . $var.'
                                                <h2 class="titleInfo">'.$title[$i].' </h2>
                                                <div class="content_info">'.$content[$i].'</div> 
                                           </div>';
                                    $cpt++;
                                }
                        echo'   </div>
                            </div>
                        </div>
                        </div>
                        </div>';
    } //displayInformationView()

    public function displayFormText() {
        $page = get_page_by_title( 'Gérer les informations');
        $linkManageInfo = get_permalink($page->ID);
        $dateMin = date('Y-m-d', strtotime("+1 day"));
        return '
        <h1>Créer une information avec du texte</h1>
            <div class="cadre">
                <form method="post">
                    Titre : <input type="text" name="titleInfo" placeholder="Inserer un titre" required maxlength="20"> </br>
                    Date d\'expiration : <input type="date" name="endDateInfo" min="' . $dateMin . '" required ></br>
                    Contenu : <textarea name="contentInfo" maxlength="200"></textarea> </br>
                    <input type="submit" value="creer" name="createText">
                </form>
            </div>
                <a href="'.$linkManageInfo.'"> Page de gestion</a>';
    }

    public function displayFormImg() {
        $page = get_page_by_title( 'Gérer les informations');
        $linkManageInfo = get_permalink($page->ID);
        $dateMin = date('Y-m-d', strtotime("+1 day"));
        return '
            <h1>Créer une information avec une image</h1>
                <div class="cadre">
                    <form method="post" enctype="multipart/form-data">
                        Titre : <input type="text" name="titleInfo" placeholder="Inserer un titre" required maxlength="20"> </br>
                        Date d\'expiration : <input type="date" name="endDateInfo" min="' . $dateMin . '" required ></br>
                        Ajouter une image :<input type="file" name="contentFile" /> </br>
                        <input type="hidden" name="MAX_FILE_SIZE" value="5000000" />
                        <input type="submit" value="creer" name="createImg">
                    </form>
                </div>
                    <a href="'.$linkManageInfo.'"> Page de gestion</a>';
    }

    public function displayFormTab() {
        $page = get_page_by_title( 'Gérer les informations');
        $linkManageInfo = get_permalink($page->ID);
        $dateMin = date('Y-m-d', strtotime("+1 day"));
        return '
            <h1>Créer une information avec un tableau</h1>
            <div class="cadre">
                <form method="post" enctype="multipart/form-data">
                    Titre : <input type="text" name="titleInfo" placeholder="Inserer un titre" required maxlength="20"> </br>
                    Date d\'expiration : <input type="date" name="endDateInfo" min="' . $dateMin . '" required ></br>
                    Ajout du fichier Xls (ou xlsx) : <input type="file" name="contentFile" /> </br>
                    <input type="hidden" name="MAX_FILE_SIZE" value="5000000" />
                    <input type="submit" value="creer" name="createTab">
                </form>
            </div>
            <div>Nous vous conseillons de ne pas dépasser trois colonnes.</div>
            <div>Nous vous conseillons également de ne pas mettre trop de contenu dans une cellule.</div>
            <a href="'.$linkManageInfo.'"> Page de gestion</a>';
    }


    /**
     * Affiche un formulaire pour choisir le type d'information que l'on veut créer
     * et affiche le formulaire de création en fonction.
     */
    public function displayInformationCreation()
    {
        $page = get_page_by_title( 'Gérer les informations');
        $linkManageInfo = get_permalink($page->ID);

        $string = 'Quel type de contenu voulez vous pour votre information ? </br>';




        $choice = $_POST['typeChoice'];
        if ($choice == 'text') {

        } elseif ($choice == 'image') {

        } elseif ($choice == 'tab') {

        }
        $string .= '';
        return $string;
    } //displayInformationCreation()



    public function displayModifyInformationForm($title, $content, $endDate, $typeInfo)
    {
        $page = get_page_by_title( 'Gérer les informations');
        $linkManageInfo = get_permalink($page->ID);
        $dateMin = date('Y-m-d', strtotime("+1 day"));
        if ($typeInfo == "text") {
            return '
                <div>
                    <form id="modify_info" method="post">
                  
                      Titre : <input type="text" name="titleInfo" value="' . $title . '" required maxlength="20"> </br>
                      Contenu : <textarea name="contentInfo" maxlength="200">' . $content . '</textarea> </br>
                      Date d\'expiration : <input type="date" name="endDateInfo" min="' . $dateMin . '" value = "' . $endDate . '" required > </br>
                      <input type="submit" name="validateChange" value="Modifier" ">
                 </form>
                 <a href="'.$linkManageInfo.'"> Page de gestion</a>
            </div>';
        } elseif ($typeInfo == "img") {
            return '
                <div>
                    <form id="modify_info" method="post" enctype="multipart/form-data">
                      Titre : <input type="text" name="titleInfo" value="' . $title . '" required maxlength="20"> </br>
                      ' . $content . ' </br>
                       Changer l\'image :<input type="file" name="contentFile" /> </br>
                       <input type="hidden" name="MAX_FILE_SIZE" value="5000000" />
                      Date d\'expiration : <input type="date" name="endDateInfo" min="' . $dateMin . '" value = "' . $endDate . '" required > </br>
                       <input type="submit" name="validateChangeImg" value="Modifier"/>
                 </form>
               <a href="'.$linkManageInfo.'"> Page de gestion</a>
            </div>';
        } elseif ($typeInfo == "tab") {
            return '
                <div>
                    <form id="modify_info" method="post" enctype="multipart/form-data">
                      Titre : <input type="text" name="titleInfo" value="' . $title . '" required maxlength="20"> </br>
                      ' . $content . ' </br>
                       Modifier le fichier:<input type="file" name="contentFile" /> </br>
                       <input type="hidden" name="MAX_FILE_SIZE" value="5000000" />
                      Date d\'expiration : <input type="date" name="endDateInfo" min="' . $dateMin . '" value = "' . $endDate . '" required > </br>
                       <input type="submit" name="validateChangeTab" value="Modifier"/>
                 </form>
               <a href="'.$linkManageInfo.'"> Page de gestion</a>
            </div>';
        } else {
            return 'Désolé, une erreur semble être survenue.';
        }
    } //displayModifyInformationForm()


    /**
     * Affiche un modal qui signal que l'inscription a été validé
     */
    public function displayCreateValidate() {
        $page = get_page_by_title( 'Gérer les informations');
        $linkManageInfo = get_permalink($page->ID);
        $this->displayStartModal("Ajout d'information validé");
        echo '<div class="alert alert-success"> L\'information a été ajoutée </div>';
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
        echo '<div class="alert alert-success"> L\'information a été modifiée </div>';
        $this->displayEndModal($linkManageInfo);
    }
}