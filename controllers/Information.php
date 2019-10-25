<?php
/**
 * Created by PhpStorm.
 * UserView: Léa Arnaud
 * Date: 17/04/2019
 * Time: 11:33
 */

class Information extends ControllerG {
    private $DB;
    private $view;

    /**
     * Constructeur d'information, initialise le modèle et la vue.
     */
    public function __construct(){
        $this->DB = new InformationManager();
        $this->view = new ViewInformation();
    }



    /**
     * Supprime les informations sélectionnées dans la page de gestion des informations.
     * @param $action
     */
    public function deleteInformations() {
        $actionDelete = $_POST['Delete'];
        if(isset($actionDelete)) {
            if (isset($_REQUEST['checkboxstatusinfo'])) {
                $checked_values = $_REQUEST['checkboxstatusinfo'];
                foreach ($checked_values as $val) {
                    $res = $this->DB->getInformationByID($val);
                    $type = $res['type'];
                    $types = ["img", "pdf", "tab"];
                    if(in_array($type, $types)) {
                        $source = "";
                        $content = $res['content'];
                        if ($type == "img") {
                            $source = explode('src=', $content);
                            $source = substr($source[1],0,-1);
                            $source = substr($source,1,-1);
                            $source = $_SERVER['DOCUMENT_ROOT'].$source;
                        } else {
                            $source = $_SERVER['DOCUMENT_ROOT'].TV_UPLOAD_PATH.$content;
                        }
                        unlink($source);
                    }
                    $this->DB->deleteInformationDB($val);
                }
            }
            $this->view->refreshPage();
        }
    } //deleteInformations()

    /**
     * Affiche un tableau avec toutes les informations et des boutons de modification ainsi qu'un bouton de suppression.
     * cf snippet Handle Informations
     */
    function informationManagement(){
        $current_user = wp_get_current_user();
        $user = $current_user->user_login;
        if(in_array("administrator", $current_user->roles)) {
            $result = $this->DB->getListInformation();
        } else {
            $result = $this->DB->getListInformationByAuthor($user);
        }

        $string = $this->view->tabHeadInformation();
        $i = 0;

        foreach ($result as $row){
            $id = $row['ID_info'];
            $title = $row['title'];
            $author = $row['author'];
            $content = $row['content'];
            $type = $row['type'];
            $creationDate = $row['creation_date'];
            $endDate = $row['end_date'];

            $this->endDateCheckInfo($id, $endDate);

            // change l'affichage de la date en français (jour-mois-année)
            $endDatefr = date("d-m-Y", strtotime($endDate));
            $creationDatefr = date("d-m-Y", strtotime($creationDate));

            $string .= $this->view->displayAllInformation($id, $title, $author, $content, $type, $creationDatefr, $endDatefr, ++$i);
        }
        $string .= $this->view->displayEndTab();
        return $string;
    } // informationManagement()

    /**
     * Récupère l'id de l'information depuis l'url et affiche le formulaire de modification pré-remplis.
     * cf snippet Modification Info
     */
    public function modifyInformation() {
        $id = $this->getMyIdUrl();

        $actionText = $_POST['validateChange'];
        $actionImg = $_POST['validateChangeImg'];
        $actionTab = $_POST['validateChangeTab'];

        $result = $this->DB->getInformationByID($id);
        $title = $result['title'];
        $content = $result['content'];
        $endDate = date('Y-m-d',strtotime($result['end_date']));
        $typeI = $result['type'];

        if($actionText == "Modifier") {
            $title = filter_input(INPUT_POST, 'titleInfo');
            $content = filter_input(INPUT_POST, 'contentInfo');
            $endDate = $_POST['endDateInfo'];

            $this->DB->modifyInformation($id,$title,$content,$endDate);
            $this->view->displayModifyValidate();
        }
        elseif($actionImg == "Modifier") { //si il s'agit d'une modification d'affiche
            $contentFile = $_FILES['contentFile'];

            $title = filter_input(INPUT_POST,'titleInfo');
            $endDate = $_POST['endDateInfo'];
            if($_FILES['contentFile']['size'] != 0) {    //si l'image est modifié
                $contentNew = $this->uploadFile($contentFile,"modify","img",$id);
                if($contentNew != null || $contentNew != 0) {
                    $this->DB->modifyInformation($id,$title,$contentNew,$endDate);
                    $this->view->displayModifyValidate();
                }
            }
            else { // si le texte et/ou la date de fin est modifié
                $this->DB->modifyInformation($id,$title,$content,$endDate);
                $this->view->displayModifyValidate();
            }

        }
        elseif($actionTab == "Modifier") { //si il s'agit d'une modification d'un tableau
            $contentFile = $_FILES['contentFile'];

            $title = filter_input(INPUT_POST,'titleInfo');
            $endDate =$_POST['endDateInfo'];
            if($_FILES['contentFile']['size'] != 0) {    //si le fichier est modifié
                $contentNew = $this->uploadFile($contentFile,"modify","tab",$id);
                if($contentNew != null || $contentNew != 0) {
                    $this->DB->modifyInformation($id,$title,$contentNew,$endDate);
                    $this->view->displayModifyValidate();
                }
            }
            else { // si le texte et/ou la date de fin est modifié
                $this->DB->modifyInformation($id,$title,$content,$endDate);
                $this->view->displayModifyValidate();
            }

        }
    } //modifyInformation()

    /**
     * Verifie si la date de fin est dépassée et supprime l'info si c'est le cas.
     * @param $id
     * @param $endDate
     */
    public function endDateCheckInfo($id, $endDate){
        if($endDate <= date("Y-m-d")) {
            $this->DB->deleteInformationDB($id);
        }
    } //endDateCheckInfo()


    /**
     * Affiche les informations sur la page principale (ou widget)
     * cf snippet Display Information
     */
    public function informationMain(){

        $result = $this->DB->getListInformation();
        $idList = array();
        $titleList = array();
        $contentList = array();
        $typeList = array();
        foreach ($result as $row) {
            $id = $row['ID_info'];
            $title = $row['title'];
            $content = $row['content'];
            $endDate = date('Y-m-d',strtotime($row['end_date']));
            $type = $row['type'];
            array_push($typeList, $type);
            $this->endDateCheckInfo($id,$endDate);
            if($type == 'tab'){
                $source = $_SERVER['DOCUMENT_ROOT'].TV_UPLOAD_PATH.$content;
                if(! file_exists($source)) {
                    array_push($idList,$id);
                    array_push($titleList,$title);
                    array_push($contentList, 'Un beau tableau devrait être ici !');
                } else {
                    $list = $this->readSpreadSheet($id);
                    foreach ($list as $table) {
                        array_push($idList,$id);
                        array_push($titleList,$title);
                        array_push($contentList, $table);
                    }
                }
            } else {
                if($type == 'img'){
                    $source = explode('src=', $content);
                    $source = substr($source[1],0,-1);
                    $source = substr($source,1,-1);
                    $source = home_url().$source;
                    if (! @getimagesize($source)) {
                        array_push($idList,$id);
                        array_push($titleList,$title);
                        array_push($contentList,'Une belle image devrait être ici !');
                    } else {
                        array_push($idList,$id);
                        array_push($titleList,$title);
                        array_push($contentList,$content);
                    }
                } else {
                    array_push($idList,$id);
                    array_push($titleList,$title);
                    array_push($contentList,$content);
                }
            }
        }
        $this->view->displayInformationView($titleList,$contentList, $typeList);
    } // informationMain()


    /**
     * Affiche le formulaire de création en fonction du type d'information et ajoute l'information
     * cf snippet create info
     * @param $actionText
     * @param $actionImg
     * @param $actionTab
     * @param $title
     * @param $content
     * @param $endDate
     */
    public function insertInformation(){
        $actionText = $_POST['createText'];
        $actionImg = $_POST['createImg'];
        $actionTab = $_POST['createTab'];
        $actionPDF = $_POST['createPDF'];

        $title = filter_input(INPUT_POST,'titleInfo');
        $content = filter_input(INPUT_POST,'contentInfo');
        $endDate = filter_input(INPUT_POST,'endDateInfo');
        $contentFile = $_FILES['contentFile'];

        if(isset($actionText)) { // si c'est une création de texte
            $this->DB->addInformationDB($title, $content, $endDate,"text");
            $this->view->displayCreateValidate();
        } elseif (isset($actionImg)) { // si c'est une création d'affiche
            //upload le fichier avec un nom temporaire
            $result = $this->uploadFile($contentFile,"create", "img", 0, $title, $endDate);
            if($result != 0) {

                $id = $result;
                //récupère l'extension du fichier
                $_FILES['file'] = $contentFile;
                $extension_upload = strtolower(  substr(  strrchr($_FILES['file']['name'], '.')  ,1)  );

                //renomme le fichier avec l'id de l'info
                rename($_SERVER['DOCUMENT_ROOT'].TV_UPLOAD_PATH."temporary.".$extension_upload,
                    $_SERVER['DOCUMENT_ROOT'].TV_UPLOAD_PATH.$id.".".$extension_upload);

                //modifie le contenu de l'information pour avoir le bon lien de l'image
                $content = '<img src="'.TV_UPLOAD_PATH.$id.'.'.$extension_upload.'">';
                $this->changeContentFile($id, $content);
            }
            $this->view->displayCreateValidate();
        } elseif (isset($actionTab)) { //si c'est une création d'un tableau de note
            $result = $this->uploadFile($contentFile,"create", "tab", 0, $title, $endDate);
            if($result != 0) {
                $id = $result;
                //récupère l'extension du fichier
                $_FILES['file'] = $contentFile;
                $extension_upload = strtolower(  substr(  strrchr($_FILES['file']['name'], '.')  ,1)  );

                //renomme le fichier avec l'id de l'info
                rename($_SERVER['DOCUMENT_ROOT'].TV_UPLOAD_PATH."temporary.".$extension_upload,
                    $_SERVER['DOCUMENT_ROOT'].TV_UPLOAD_PATH.$id.".".$extension_upload);

                //modifie le contenu de l'information pour avoir le bon nom du fichier
                $content = $id.'.'.$extension_upload;
                $this->changeContentFile($id, $content);
            }
            $this->view->displayCreateValidate();
        } else if ($actionPDF) {
            $result = $this->uploadFile($contentFile,"create", "pdf", 0, $title, $endDate);
            if($result != 0) {

                $id = $result;
                //récupère l'extension du fichier
                $_FILES['file'] = $contentFile;
                $extension_upload = strtolower(  substr(  strrchr($_FILES['file']['name'], '.')  ,1)  );

                //renomme le fichier avec l'id de l'info
                rename($_SERVER['DOCUMENT_ROOT'].TV_UPLOAD_PATH."temporary.".$extension_upload,
                    $_SERVER['DOCUMENT_ROOT'].TV_UPLOAD_PATH.$id.".".$extension_upload);

                //modifie le contenu de l'information pour avoir le bon lien de l'image
                $content = '[pdf-embedder url="'.TV_UPLOAD_PATH.$id.'.pdf"]';
                //$content =  '<embed src="'.TV_PLUG_PATH.'views/media/' . $id . '.pdf'.'"pdf#toolbar=0&navpanes=0&scrollbar=0">';
                //$content = '<img src="'.TV_PLUG_PATH.'views/media/'.$id.'.'.$extension_upload.'">';
                $this->changeContentFile($id, $content);
            }
        }
        return
            $this->view->displayStartMultiSelect().
            $this->view->displayTitleSelect('text', 'Texte', true).
            $this->view->displayTitleSelect('image', 'Image').
            $this->view->displayTitleSelect('table', 'Tableau').
            $this->view->displayTitleSelect('pdf', 'PDF').
            $this->view->displayEndOfTitle().
            $this->view->displayContentSelect('text', $this->view->displayFormText(), true).
            $this->view->displayContentSelect('image', $this->view->displayFormImg()).
            $this->view->displayContentSelect('table', $this->view->displayFormTab()).
            $this->view->displayContentSelect('pdf', $this->view->displayFormPDF()).
            $this->view->displayEndDiv();

    } //insertInformation()

    public function changeContentFile($id, $content){
        $result = $this->DB->getInformationByID($id);
        $title = $result['title'];
        $endDate = date('Y-m-d',strtotime($result['end_date']));
        $this->DB->modifyInformation($id, $title, $content, $endDate);
    }


    /**
     * Upload un fichier sur le serveur, créer l'information avec un contenu temporaire
     * ou renvoie le nouveau contenu quand il s'agit d'une modification.
     * @param $id
     * @param $file
     * @param $title
     * @param $endDate
     * @param $action
     * @return int|string
     */
    public function uploadFile($file, $action, $type, $id =0, $title="", $endDate=""){
        if($action == "create"){ //si la fonction a été appelée pour la création d'une info
            $id = "temporary"; //met un id temporaire pour le nom du fichier
        } elseif ($action == "modify"){ //si la fonction a été appelée pour la modification d'une info
            $this->deleteFile($id); // efface le fichier correspondant a l'info modifié
        } else {
            echo "il y a une erreur dans l'appel de la fonction";
        }

        $_FILES['file'] = $file;
        $maxsize = 5000000; //5Mo
        if ($_FILES['file']['error'] > 0) echo "Erreur lors du transfert <br>";
        if ($_FILES['file']['size'] > $maxsize) echo "Le fichier est trop volumineux <br>";

        if($type == "img"){$extensions_valides = array( 'jpg' , 'jpeg' , 'gif' , 'png' );}
        if($type == "tab") {$extensions_valides = array( 'xls' , 'xlsx' , 'ods' );}
        if($type == "pdf") { $extensions_valides = array("pdf"); }

        $extension_upload = strtolower(  substr(  strrchr($_FILES['file']['name'], '.')  ,1)  );
        if ( in_array($extension_upload,$extensions_valides) ) {
            $nom =  $_SERVER['DOCUMENT_ROOT'].TV_UPLOAD_PATH.$id.".".$extension_upload;
            $resultat = move_uploaded_file($_FILES['file']['tmp_name'],$nom);
        } else {
            echo "Extension incorrecte <br>";
        }

        $goodtypes = ["img", "tab", "pdf"];

        if ($resultat){
            if($action == "create"){
                if(in_array($type, $goodtypes)) {
                    $result = $this->DB->addInformationDB($title,"temporary content",$endDate, $type);
                    return $result;
                } else {
                    echo "<p>le type d'information n'est pas le bon </p>";
                }
            } elseif ($action == "modify"){
                if($type == "img") {
                    //renvoie le nouveau contenu de l'info
                    $content = '<img src="'.TV_UPLOAD_PATH.$id. '.' . $extension_upload . '">';
                    return $content;
                } elseif ($type == "tab"){
                    //renvoie le nouveau contenu de l'info
                    $content =  $id .'.'. $extension_upload;
                    return $content;
                } else if($type == "pdf"){
                    $content = '[pdf-embedder url="'.TV_UPLOAD_PATH.$id.'.pdf]';
                    return $content;
                } else {
                    echo "le type d'information n'est pas le bon";
                }
            }
        } else {
            echo "<p>le fichier n'as pas été upload</p>";
            return 0;
        }
    }//uploadFile()

    public function readSpreadSheet($id){

        $file = glob($_SERVER['DOCUMENT_ROOT'].TV_UPLOAD_PATH.$id."."."*");
        foreach ($file as $i) {
            $filename = $i;
        }
        $extension = ucfirst(strtolower(end(explode(".", $filename))));
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($extension);
        $reader->setReadDataOnly(TRUE);
        $spreadsheet = $reader->load($filename);

        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow();

        $contentList = array();
        $content = "";
        $mod = 0;

        for ($i = 0; $i < $highestRow; ++$i) {
            $mod = $i % 10;
            if($mod == 0){
                $content .= '<table class ="table table-bordered tablesize">';
            }
            foreach ($worksheet->getRowIterator($i+1,1) as $row) {
                $content .= '<tr scope="row">';
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(FALSE);
                foreach ($cellIterator as $cell) {
                    $content .='<td class="text-center">' .
                        $cell->getValue() .
                        '</td>';
                }
                $content .='</tr>';
            }
            if($mod == 9){
                $content .= '</table>';
                array_push($contentList,$content);
                $content = "";
            }
        }
        if($mod != 9 && $i >0){
            $content .= '</table>';
            array_push($contentList,$content);
            $content = "";
        }
        return $contentList;
    }
}