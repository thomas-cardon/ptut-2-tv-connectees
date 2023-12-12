<?php

namespace Controllers;

use Models\Information;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Views\InformationView;

/**
 * Class InformationController
 *
 * Manage information (create, update, delete, display)
 *
 * @package Controllers
 */
class InformationController extends Controller
{
    /**
     * @var Information
     */
    private $model;

    /**
     * @var InformationView
     */
    private $view;

    /**
     * Constructor of InformationController
     */
    public function __construct()
    {
        $this->model = new Information();
        $this->view = new InformationView();
    }

    /**
     * Create information and add it into the database
     *
     * @return string
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function create()
    {
        $current_user = wp_get_current_user();

        // All forms
        $actionText = filter_input(INPUT_POST, 'createText');
        $actionImg = filter_input(INPUT_POST, 'createImg');
        $actionTab = filter_input(INPUT_POST, 'createTab');
        $actionPDF = filter_input(INPUT_POST, 'createPDF');
        $actionEvent = filter_input(INPUT_POST, 'createEvent');

        // Variables
        $title = filter_input(INPUT_POST, 'title');
        $content = filter_input(INPUT_POST, 'content');
        $endDate = filter_input(INPUT_POST, 'expirationDate');
        $creationDate = date('Y-m-d');

        // If the title is empty
        if ($title == '') {
            $title = 'Sans titre';
        }

        $information = $this->model;

        // Set the base of all information
        $information->setTitle($title);
        $information->setAuthor($current_user->ID);
        $information->setCreationDate($creationDate);
        $information->setExpirationDate($endDate);
        $information->setAdminId(null);

        if (isset($actionText)) {   // If the information is a text
            $information->setContent($content);
            $information->setType("text");

            // Try to insert the information
            if ($information->insert()) {
                $this->view->displayCreateValidate();
            } else {
                $this->view->displayErrorInsertionInfo();
            }
        }
        if (isset($actionImg)) {  // If the information is an image
            $type = "img";
            $information->setType($type);
            $filename = $_FILES['contentFile']['name'];
            $fileTmpName = $_FILES['contentFile']['tmp_name'];
            $explodeName = explode('.', $filename);
            $goodExtension = ['jpg', 'jpeg', 'gif', 'png', 'svg'];
            if (in_array(end($explodeName), $goodExtension)) {
                $this->registerFile($filename, $fileTmpName, $information);
            } else {
                $this->view->buildModal('Image non valide', '<p>Ce fichier est une image non valide, veuillez choisir une autre image</p>');
            }
        }
        if (isset($actionTab)) { // If the information is a table
            $type = "tab";
            $information->setType($type);
            $filename = $_FILES['contentFile']['name'];
            $fileTmpName = $_FILES['contentFile']['tmp_name'];
            $explodeName = explode('.', $filename);
            $goodExtension = ['xls', 'xlsx', 'ods'];
            if (in_array(end($explodeName), $goodExtension)) {
                $this->registerFile($filename, $fileTmpName, $information);
            } else {
                $this->view->buildModal('Tableau non valide', '<p>Ce fichier est un tableau non valide, veuillez choisir un autre tableau</p>');
            }
        }
        if (isset($actionPDF)) {
            $type = "pdf";
            $information->setType($type);
            $filename = $_FILES['contentFile']['name'];
            $explodeName = explode('.', $filename);
            if (end($explodeName) == 'pdf') {
                $fileTmpName = $_FILES['contentFile']['tmp_name'];
                $this->registerFile($filename, $fileTmpName, $information);
            } else {
                $this->view->buildModal('PDF non valide', '<p>Ce fichier est un tableau non PDF, veuillez choisir un autre PDF</p>');
            }
        }
        if (isset($actionEvent)) {
            $type = 'event';
            $information->setType($type);
            $countFiles = count($_FILES['contentFile']['name']);
            for ($i = 0; $i < $countFiles; $i++) {
                $this->model->setId(null);
                $filename = $_FILES['contentFile']['name'][$i];
                $fileTmpName = $_FILES['contentFile']['tmp_name'][$i];
                $explodeName = explode('.', $filename);
                $goodExtension = ['jpg', 'jpeg', 'gif', 'png', 'svg', 'pdf'];
                if (in_array(end($explodeName), $goodExtension)) {
                    $this->registerFile($filename, $fileTmpName, $information);
                }
            }
        }
        // Return a selector with all forms
        return
          $this->view->renderContainer(
              $this->view->displayStartMultiSelect() .
            $this->view->displayTitleSelect('text', 'Texte', true) .
            $this->view->displayTitleSelect('image', 'Image') .
            $this->view->displayTitleSelect('table', 'Tableau') .
            $this->view->displayTitleSelect('pdf', 'PDF') .
            $this->view->displayTitleSelect('event', 'Événement') .
            $this->view->displayEndOfTitle() .
            $this->view->displayContentSelect('text', $this->view->displayFormText(), true) .
            $this->view->displayContentSelect('image', $this->view->displayFormImg()) .
            $this->view->displayContentSelect('table', $this->view->displayFormTab()) .
            $this->view->displayContentSelect('pdf', $this->view->displayFormPDF()) .
            $this->view->displayContentSelect('event', $this->view->displayFormEvent()) .
            $this->view->displayEndDiv(),
              'Créer une information'
          ) . $this->view->renderContainerDivider() .
          $this->view->renderContainer($this->view->contextCreateInformation());
    }

    /**
     * Modify the information
     *
     * @return string
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function modify()
    {
        $id = $_GET['id'];

        if (empty($id) || is_numeric($id) && !$this->model->get($id)) {
            return $this->view->noInformation();
        }

        $current_user = wp_get_current_user();
        $information = $this->model->get($id);

        if (!(members_current_user_has_role('administrator') || members_current_user_has_role('secretaire') || $information->getAuthor()->getId() == $current_user->ID)) {
            return $this->view->noInformation();
        }

        if (!is_null($information->getAdminId())) {
            return $this->view->informationNotAllowed();
        }

        $submit = filter_input(INPUT_POST, 'submit');
        if (isset($submit)) {
            $title = filter_input(INPUT_POST, 'title');
            $content = filter_input(INPUT_POST, 'content');
            $endDate = filter_input(INPUT_POST, 'expirationDate');

            $information->setTitle($title);
            $information->setExpirationDate($endDate);

            if ($information->getType() == 'text') {
                // Set new information
                $information->setContent($content);
            } else {
                // Change the content
                if ($_FILES["contentFile"]['size'] != 0) {
                    echo $_FILES["contentFile"]['size'];
                    $filename = $_FILES["contentFile"]['name'];
                    if ($information->getType() == 'img') {
                        $explodeName = explode('.', $filename);
                        $goodExtension = ['jpg', 'jpeg', 'gif', 'png', 'svg'];
                        if (in_array(end($explodeName), $goodExtension)) {
                            $this->deleteFile($information->getId());   //$_SERVER['DOCUMENT_ROOT'].$this->model->getContent()
                            $this->registerFile($filename, $_FILES["contentFile"]['tmp_name'], $information);
                        } else {
                            $this->view->buildModal('Image non valide', '<p>Ce fichier est une image non valide, veuillez choisir une autre image</p>');
                        }
                    } elseif ($information->getType() == 'pdf') {
                        $explodeName = explode('.', $filename);
                        if (end($explodeName) == 'pdf') {
                            $this->deleteFile($information->getId());
                            $this->registerFile($filename, $_FILES["contentFile"]['tmp_name'], $information);
                        } else {
                            $this->view->buildModal('PDF non valide', '<p>Ce fichier est un PDF non valide, veuillez choisir un autre PDF</p>');
                        }
                    } elseif ($information->getType() == 'tab') {
                        $explodeName = explode('.', $filename);
                        $goodExtension = ['xls', 'xlsx', 'ods'];
                        if (in_array(end($explodeName), $goodExtension)) {
                            $this->deleteFile($information->getId());
                            $this->registerFile($filename, $_FILES["contentFile"]['tmp_name'], $information);
                        } else {
                            $this->view->buildModal('Tableau non valide', '<p>Ce fichier est un tableau non valide, veuillez choisir un autre tableau</p>');
                        }
                    }
                }
            }

            if ($information->update()) {
                $this->view->displayModifyValidate();
            } else {
                $this->view->errorMessageCantAdd();
            }
        }

        $delete = filter_input(INPUT_POST, 'delete');
        if (isset($delete)) {
            $information->delete();
            $this->view->displayModifyValidate();
        }
        return $this->view->displayModifyInformationForm($information->getTitle(), $information->getContent(), $information->getExpirationDate(), $information->getType());
    }


    /**
     * Upload a file in a directory and in the database
     *
     * @param $filename     string
     * @param $tmpName      string
     */
    public function registerFile($filename, $tmpName, $entity)
    {
        $id = 'temporary';
        $extension_upload = strtolower(substr(strrchr($filename, '.'), 1));
        $name = PATH . TV_UPLOAD_PATH . $id . '.' . $extension_upload;

        // Upload the file
        if ($result = move_uploaded_file($tmpName, $name)) {
            $entity->setContent('temporary content');
            if ($entity->getId() == null) {
                $id = $entity->insert();
            } else {
                $entity->update();
                $id = $entity->getId();
            }
        } else {
            $this->view->errorMessageCantAdd();
        }
        // If the file upload and the upload of the information in the database works
        if ($id != 0) {
            $entity->setId($id);

            $md5Name = $id . md5_file($name);
            rename($name, PATH . TV_UPLOAD_PATH . $md5Name . '.' . $extension_upload);

            $content = $md5Name . '.' . $extension_upload;

            $entity->setContent($content);
            if ($entity->update()) {
                $this->view->displayCreateValidate();
            } else {
                $this->view->errorMessageCantAdd();
            }
        }
    }

    /**
     * Delete the file who's link to the id
     *
     * @param $id int Code
     */
    public function deleteFile($id)
    {
        $this->model = $this->model->get($id);
        $source = PATH. TV_UPLOAD_PATH . $this->model->getContent();
        wp_delete_file($source);
    }

    public function displayTable()
    {
        $numberAllEntity = $this->model->countAll();
        $url = $this->getPartOfUrl();
        $number = filter_input(INPUT_GET, 'number');
        $pageNumber = 1;
        if (sizeof($url) >= 2 && is_numeric($url[1])) {
            $pageNumber = $url[1];
        }
        if (isset($number) && !is_numeric($number) || empty($number)) {
            $number = 25;
        }
        $begin = ($pageNumber - 1) * $number;
        $maxPage = ceil($numberAllEntity / $number);
        if ($maxPage <= $pageNumber && $maxPage >= 1) {
            $pageNumber = $maxPage;
        }
        $current_user = wp_get_current_user();
        if (in_array('administrator', $current_user->roles) || in_array('secretaire', $current_user->roles)) {
            $informationList = $this->model->getList($begin, $number);
        } else {
            $informationList = $this->model->getAuthorListInformation($current_user->ID, $begin, $number);
        }

        $name = 'Info';
        $header = ['Titre', 'Contenu', 'Créé le', 'Expire le', 'Auteur', 'Type', ''];
        $dataList = [];
        $row = $begin;
        $imgExtension = ['jpg', 'jpeg', 'gif', 'png', 'svg'];
        foreach ($informationList as $information) {
            ++$row;

            $contentExplode = explode('.', $information->getContent());

            $content = TV_UPLOAD_PATH;
            if (!is_null($information->getAdminId())) {
                $content = URL_WEBSITE_VIEWER . TV_UPLOAD_PATH;
            }

            if (in_array($information->getType(), ['img', 'pdf', 'event', 'tab'])) {
                if (in_array($contentExplode[1], $imgExtension)) {
                    $content = '<img class="img-thumbnail img_table_ecran" src="' . $content . $information->getContent() . '" alt="' . $information->getTitle() . '">';
                } elseif ($contentExplode[1] === 'pdf') {
                    $content = '[pdf-embedder url="' . TV_UPLOAD_PATH . $information->getContent() . '"]';
                } elseif ($information->getType() === 'tab') {
                    $content = 'Tableau Excel';
                }
            } else {
                $content = $information->getContent();
            }

            $type = $information->getType();
            if ($information->getType() === 'img') {
                $type = 'Image';
            } elseif ($information->getType() === 'pdf') {
                $type = 'PDF';
            } elseif ($information->getType() === 'event') {
                $type = 'Événement';
            } elseif ($information->getType() === 'text') {
                $type = 'Texte';
            } elseif ($information->getType() === 'tab') {
                $type = 'Table Excel';
            }
            $dataList[] = [$row, $this->view->buildCheckbox($name, $information->getId()), $information->getTitle(), $content, $information->getCreationDate(), $information->getExpirationDate(), 'inconnu' /* $information->getAuthor()->getLogin() */, $type, /*$this->view->buildLinkForModify(esc_url(get_permalink(get_page_by_title('Modifier une information'))) . '?id=' . $information->getId())*/ "test"];
        }

        $submit = filter_input(INPUT_POST, 'delete');
        if (isset($submit)) {
            if (isset($_REQUEST['checkboxStatusInfo'])) {
                $checked_values = $_REQUEST['checkboxStatusInfo'];
                foreach ($checked_values as $id) {
                    $entity = $this->model->get($id);
                    if (in_array('administrator', $current_user->roles) || in_array('secretaire', $current_user->roles) || $entity->getAuthor()->getId() == $current_user->ID) {
                        $type = $entity->getType();
                        $types = ["img", "pdf", "tab", "event"];
                        if (in_array($type, $types)) {
                            $this->deleteFile($id);
                        }
                        $entity->delete();
                    }
                }
                $this->view->refreshPage();
            }
        }

        return ($pageNumber == 1 ? $this->view->getHeader() : '') .
        $this->view->renderContainerDivider() .
        $this->view->renderContainer(
            $this->view->displayTable($name, 'Informations', $header, $dataList, '', '<a type="submit" class="btn btn-primary" href="' . home_url('/creer-information') . '" role="button" aria-disabled="true">Créer</a>') . $this->view->pageNumber($maxPage, $pageNumber, home_url('/gerer-les-informations'), $number),
            '',
            'container-xl py-5 my-5 text-center'
        );
    }


    /**
     * Check if the end date is today or less
     * And delete the file if the date is past
     *
     * @param $id
     * @param $endDate
     */
    public function endDateCheckInfo($id, $endDate)
    {
        if ($endDate <= date("Y-m-d")) {
            $information = $this->model->get($id);
            $this->deleteFile($id);
            $information->delete();
        }
    }
    
    /**
     * Displays the information slideshow
     * 
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     * @author Thomas Cardon
     */
     public function displaySlideshow() {
       $info = $this->model->getList();
       
       foreach ($info as $i)
         $this->view->carousel->add($i->getTitle(), $i->getContent(), $i->getType());
         
       echo $this->view->carousel->build();
     }

    /**
     * Display a slideshow
     * The slideshow display all the informations
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function informationMain()
    {
        $informations = $this->model->getList();
        $this->view->displayStartSlideshow();
        foreach ($informations as $information) {
            $endDate = date('Y-m-d', strtotime($information->getExpirationDate()));
            if (!$this->endDateCheckInfo($information->getId(), $endDate)) {
                if ($information->getType() == 'tab') {
                    $list = $this->readSpreadSheet(TV_UPLOAD_PATH . $information->getContent());
                    $content = "";
                    foreach ($list as $table) {
                        $content .= $table;
                    }
                    $information->setContent($content);
                }

                $adminSite = true;
                if (is_null($information->getAdminId())) {
                    $adminSite = false;
                }
                
                $this->view->displaySlide($information->getTitle(), $information->getContent(), $information->getType(), $adminSite);
            }
        }
        $this->view->displayEndSlideshow();
    }

    public function registerNewInformation()
    {
        $informationList = $this->model->getFromAdminWebsite();
        $myInformationList = $this->model->getAdminWebsiteInformation();
        foreach ($myInformationList as $information) {
            if ($adminInfo = $this->model->getInformationFromAdminSite($information->getId())) {
                if ($information->getTitle() != $adminInfo->getTitle()) {
                    $information->setTitle($adminInfo->getTitle());
                }
                if ($information->getContent() != $adminInfo->getContent()) {
                    $information->setContent($adminInfo->getContent());
                }
                if ($information->getExpirationDate() != $adminInfo->getExpirationDate()) {
                    $information->setExpirationDate($adminInfo->getExpirationDate());
                }
                $information->update();
            } else {
                $information->delete();
            }
        }
        foreach ($informationList as $information) {
            $exist = 0;
            foreach ($myInformationList as $myInformation) {
                if ($information->getId() == $myInformation->getAdminId()) {
                    ++$exist;
                }
            }
            if ($exist == 0) {
                $information->setAdminId($information->getId());
                $information->insert();
            }
        }
    }

    /**
     *  Display a slideshow of event information in full screen
     */
    public function displayEvent()
    {
        $events = $this->model->getListInformationEvent();
        $this->view->displayStartSlideEvent();
        foreach ($events as $event) {
            $this->view->displaySlideBegin();
            $extension = explode('.', $event->getContent());
            $extension = $extension[1];
            if ($extension == "pdf") {
                echo '
				<div class="canvas_pdf" id="' . $event->getContent() . '"></div>';
            //echo do_shortcode('[pdf-embedder url="'.$event->getContent().'"]');
            } else {
                echo '<img src="' . TV_UPLOAD_PATH . $event->getContent() . '" alt="' . $event->getTitle() . '">';
            }
            echo $this->view->displayEndDiv();
        }
        $this->view->displayEndDiv();
    }

    /**
     * Read an excel file
     *
     * @param $content
     *
     * @return array
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function readSpreadSheet($content)
    {
        $file = PATH. $content;

        $array = explode(".", $file);
        $extension = ucfirst(strtolower(end($array)));
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($extension);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($file);

        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow();

        $contentList = array();
        $content = "";
        $mod = 0;
        for ($i = 0; $i < $highestRow; ++$i) {
            $mod = $i % 10;
            if ($mod == 0) {
                $content .= '<table class ="table table-bordered tablesize">';
            }
            foreach ($worksheet->getRowIterator($i + 1, 1) as $row) {
                $content .= '<tr scope="row">';
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                foreach ($cellIterator as $cell) {
                    $content .= '<td class="text-center">' .
                        $cell->getValue() .
                        '</td>';
                }
                $content .= '</tr>';
            }
            if ($mod == 9) {
                $content .= '</table>';
                array_push($contentList, $content);
                $content = "";
            }
        }
        if ($mod != 9 && $i > 0) {
            $content .= '</table>';
            array_push($contentList, $content);
        }
        return $contentList;
    }
}
