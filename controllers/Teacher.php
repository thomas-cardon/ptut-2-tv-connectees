<?php
/**
 * Created by PhpStorm.
 * UserView: Rohrb
 * Date: 25/04/2019
 * Time: 11:25
 */

/**
 * Permet de créer, modifier et afficher des enseignants
 * Class Teacher
 */
class Teacher extends User implements Schedule {
    /**
     * Vue de Teacher
     * @var TeacherView
     */
    private $view;

    /**
     * Modèle de Teacher
     * @var TeacherModel
     */
    private $model;

    /**
     * Constructeur de Teacher
     */
    public function __construct(){
        $this->view = new TeacherView();
        $this->model = new TeacherModel();
    }

    public function displaySchedules() {
        $current_user = wp_get_current_user();
        $codes = unserialize($current_user->code); // On utilie cette fonction car les codes dans la base de données sont sérialisés
        $this->displaySchedule($codes[0]); // On affiche le codes[0] car les enseignants n'ont qu'un code
    }

    /**
     * Inscrit tous les professeurs depuis un fichier excel
     */
    public function insertTeacher(){
        $actionTeacher = $_POST['importProf'];
        if ($actionTeacher) {
            $allowed_extension = array("Xls", "Xlsx", "Csv");
            $extension = ucfirst(strtolower(end(explode(".", $_FILES["excelProf"]["name"]))));

            //On vérifie l'extension est valide
            if (in_array($extension, $allowed_extension)) {
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($extension);
                $reader->setReadDataOnly(TRUE);
                $spreadsheet = $reader->load($_FILES["excelProf"]["tmp_name"]);

                $worksheet = $spreadsheet->getActiveSheet();
                $highestRow = $worksheet->getHighestRow();

                $row = $worksheet->getRowIterator(1, 1);
                $cells = [];

                //On lit la première ligne
                foreach ($row as $value){
                    $cellIterator = $value->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(FALSE);
                    foreach ($cellIterator as $cell) {
                        $cells[] = $cell->getValue();
                    }
                }

                //On vérifie si le fichier est le bon
                if($cells[0] == "Identifiant Ent" && $cells[1] == "Adresse mail" && $cells[2] == "Code") {
                    $doubles = array();
                    for ($i = 2; $i < $highestRow + 1; ++$i) {
                        $cells = array();
                        foreach ($worksheet->getRowIterator($i, $i + 1) as $row) {
                            $cellIterator = $row->getCellIterator();
                            $cellIterator->setIterateOnlyExistingCells(FALSE);
                            foreach ($cellIterator as $cell) {
                                $cells[] = $cell->getValue();
                            }
                        }
                        $pwd = wp_generate_password();
                        $hashpass = wp_hash_password($pwd);
                        $login = $cells[0];
                        $email = $cells[1];
                        $codes = [$cells[2]];
                        if(isset($login) && isset($email)) {
                            if ($this->model->insertTeacher($login, $hashpass, $email, $codes)) {
                                foreach ($codes as $code) {
                                    $path = $this->getFilePath($code);
                                    if (!file_exists($path))
                                        $this->addFile($code);
                                }

                                //Envoie un email à l'enseignant inscrit avec son login et son mot de passe
                                $to = $email;
                                $subject = "Inscription à la télé-connecté";
                                $message = '
                                 <html>
                                  <head>
                                   <title>Inscription à la télé-connecté</title>
                                  </head>
                                  <body>
                                   <p>Bonjour, vous avez été inscrit sur le site de la Télé Connecté de votre département en tant qu\'enseignant</p>
                                   <p> Sur ce site, vous aurez accès à votre emploie du temps, aux informations concernant votre scolarité et vous pourrez poster des alertes.</p>
                                   <p> Votre identifiant est ' . $login . ' et votre mot de passe est ' . $pwd . '.</p>
                                   <p> Veuillez changer votre mot de passe lors de votre première connexion pour plus de sécurité !</p>
                                   <p> Pour vous connecter, rendez-vous sur le site : <a href="' . home_url() . '">.</p>
                                   <p> Nous vous souhaitons une bonne expérience sur notre site.</p>
                                  </body>
                                 </html>
                                 ';

                                $headers = array('Content-Type: text/html; charset=UTF-8');

                                wp_mail( $to, $subject, $message, $headers );
                            } else {
                                array_push($doubles, $cells[0]);
                            }
                        }
                    }
                    if(! is_null($doubles[0])) {
                        $this->view->displayErrorDouble($doubles);
                    } else {
                        $this->view->displayInsertValidate();
                    }
                }
                else {
                    $this->view->displayWrongFile();
                }
            } else {
                $this->view->displayWrongExtension();
            }
        }
        return $this->view->displayInsertImportFileTeacher();
    }

    /**
     * Affiche tous les enseignants dans un tableau
     */
    public function displayAllTeachers(){
        $results = $this->model->getUsersByRole('enseignant');
        if(isset($results)){
            $string = $this->view->displayTabHeadTeacher();
            $row = 0;
            foreach ($results as $result){
                ++$row;
                $string .= $this->view->displayAllTeachers($result, $row);
            }
            $string .= $this->view->displayEndTab();
            return $string;
        } else {
            return $this->view->displayEmpty();
        }
    }

    /**
     * Modifie l'enseignant
     * @param $result   Données de l'enseignant avant modification
     * @return string
     */
    public function modifyTeacher($result){
        $page = get_page_by_title( 'Gestion des utilisateurs');
        $linkManageUser = get_permalink($page->ID);
        $action = $_POST['modifValidate'];
        $code = [$_POST['modifCode']];
        if($action === 'Valider'){
            if($this->model->modifyTeacher($result, $code)){
                $this->view->displayModificationValidate($linkManageUser);
            }
        }
        return $this->view->displayModifyMyTeacher($result);
    }
}