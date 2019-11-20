<?php
/**
 * Created by PhpStorm.
 * UserView: Rohrb
 * Date: 25/04/2019
 * Time: 10:33
 */

/**
 * Permet de créer, modifier et afficher des étudiants
 * Class Student
 */
class Student extends User implements Schedule
{
    /**
     * Vue de Student
     * @var StudentView
     */
    public $view;

    /**
     * Modèle de Student
     * @var StudentModel
     */
    private $model;

    /**
     * Constructeur de Student.
     */
    public function __construct()
    {
        $this->view = new StudentView();
        $this->model = new StudentModel();
    }

    public function displaySchedules()
    {
        $current_user = wp_get_current_user();
        $codes = unserialize($current_user->code); // On utilie cette fonction car les codes dans la base de données sont sérialisés
        if (file_exists($this->getFilePath($codes[2]))) {
            return $this->displaySchedule($codes[2]);
        } else if (file_exists($this->getFilePath($codes[1]))) {
            return $this->displaySchedule($codes[1]);
        } else if ($this->displaySchedule($codes[0])) {
            return $this->displaySchedule($codes[0]);
        } else {
            return $this->view->displayNoStudy();
        }
    }

    public function inscriptionStudent()
    {

        $action = $_POST['createEtu'];
        $login = filter_input(INPUT_POST, 'loginEtu');
        $pwd = filter_input(INPUT_POST, 'pwdEtu');
        $pwdConf = filter_input(INPUT_POST, 'pwdConfirmEtu');
        $email = filter_input(INPUT_POST, 'emailEtu');

        $privatekey = '6LefDq4UAAAAAO0ky6FGIcPDbNJXR9ucTom3E9aO';

        # the response from reCAPTCHA
        $resp = null;
        # the error code from reCAPTCHA, if any
        $error = null;

        if ($_POST["recaptcha_response_field"]) {
            $resp = recaptcha_check_answer($privatekey,
                $_SERVER["REMOTE_ADDR"],
                $_POST["recaptcha_challenge_field"],
                $_POST["recaptcha_response_field"]);

            if ($resp->is_valid) {
                echo "You got it!";
                if ($pwd == $pwdConf) {
                    $pwd = wp_hash_password($pwd);
                    if ($this->model->insertStudent($login, $pwd, $email)) {
                        $this->view->displayInsertValidate();
                    } else {
                        $this->view->displayErrorInsertion();
                    }
                } else {
                    $this->view->displayBadPassword();
                }
            } else {
                # set the error code so that we can display it
                $error = $resp->error;
            }
        }
        //echo recaptcha_get_html($publickey, $error);


        if (isset($action)) {

        }
        return $this->view->displayFormInscription();
    }

    /**
     * Ajoute tous les étudiants présent dans un fichier excel
     */
    public function insertStudent()
    {
        $actionStudent = $_POST['importEtu'];
        if ($actionStudent) {
            $allowed_extension = array("Xls", "Xlsx", "Csv");
            $extension = ucfirst(strtolower(end(explode(".", $_FILES["excelEtu"]["name"]))));
            // On vérifie si l'extension est correct
            if (in_array($extension, $allowed_extension)) {
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($extension);
                $reader->setReadDataOnly(TRUE);
                $spreadsheet = $reader->load($_FILES["excelEtu"]["tmp_name"]);

                $worksheet = $spreadsheet->getActiveSheet();
                $highestRow = $worksheet->getHighestRow();

                $row = $worksheet->getRowIterator(1, 1);
                $cells = [];
                foreach ($row as $value) {
                    $cellIterator = $value->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(FALSE);
                    foreach ($cellIterator as $cell) {
                        $cells[] = $cell->getValue();
                    }
                }
                // On vérifie s'il s'agit bien du bon fichier Excel
                if ($cells[0] == "Identifiant Ent" && $cells[1] == "Adresse mail") {
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
                        //Si le login et le l'email sont indiqués, on inscrit l'utilisateur
                        if (isset($login) && isset($email)) {
                            //On vérifie que le login et l'adresse mail ne sont pas déjà enregistrés
                            if ($this->model->insertStudent($login, $hashpass, $email)) {
                                //On envoie un email pour chaque étudiant inscrit, le mail contient le login et le mot de passe
                                $to = $email;
                                $subject = "Inscription à la télé-connecté";
                                $message = '
                                 <!DOCTYPE html>
                                 <html lang="fr">
                                  <head>
                                   <title>Inscription à la télé-connecté</title>
                                  </head>
                                  <body>
                                   <p>Bonjour, vous avez été inscrit sur le site de la Télé Connecté de votre département en tant qu\'étudiant</p>
                                   <p> Sur ce site, vous aurez accès à votre emploie du temps, à vos notes et aux informations concernant votre scolarité.</p>
                                   <p> Votre identifiant est ' . $login . ' et votre mot de passe est ' . $pwd . '.</p>
                                   <p> Veuillez changer votre mot de passe lors de votre première connexion pour plus de sécurité !</p>
                                   <p> Pour vous connecter, rendez-vous sur le site : <a href="' . home_url() . '"> ' . home_url() . ' </a>.</p>
                                   <p> Nous vous souhaitons une bonne expérience sur notre site.</p>
                                  </body>
                                 </html>
                                 ';

                                $headers = array('Content-Type: text/html; charset=UTF-8');

                                wp_mail($to, $subject, $message, $headers);
                            } else {
                                array_push($doubles, $login);
                            }
                        }
                    }
                    if (!is_null($doubles[0])) {
                        $this->view->displayErrorDouble($doubles); //On affiche les utilisateurs qui n'ont pas été inscrits
                    } else {
                        $this->view->displayInsertValidate(); //Si tous les étudiants sont inscrits
                    }
                } else {
                    $this->view->displayWrongFile(); //Affiche une erreur s'il ne s'agit pas du bon fichier
                }
            } else {
                $this->view->displayWrongExtension(); //Affiche une erreur s'il ne s'agit pas de la bonne extension
            }
        }
        return $this->view->displayInsertImportFileStudent();
    }

    /**
     * Affiche tout les étudiants dans un tableau
     */
    function displayAllStudents()
    {
        $results = $this->model->getUsersByRole('etudiant'); //On récupère tous les étudiants
        // S'il y a des étudiants d'inscrit
        if (isset($results)) {
            $string = $this->view->displayTabHeadStudent();
            $row = 0;
            foreach ($results as $result) {
                ++$row;
                $id = $result['ID'];
                $login = $result['user_login'];
                $code = unserialize($result['code']);
                $year = $this->model->getTitle($code[0]);
                $group = $this->model->getTitle($code[1]);
                $halfgroup = $this->model->getTitle($code[2]);
                $string .= $this->view->displayAllStudent($id, $login, $year, $group, $halfgroup, $row);
            }
            $string .= $this->view->displayEndTab();
            $string .= $this->view->displayRedSignification();
            return $string;
        } else {
            return $this->view->displayEmpty();
        }
    }

    /**
     * Modifie l'étudiant sélectionné
     * @param $result   WP_User Données de l'étudiant avant modification
     * @return string
     */
    public function modifyMyStudent($result)
    {
        $page = get_page_by_title('Gestion des utilisateurs');
        $linkManageUser = get_permalink($page->ID);
        //On récupère toutes les années, groupes et demi-groupes
        // pour pouvoir permettre à l'utilisateur de les sélectionner lors de la modification
        $years = $this->model->getCodeYear();
        $groups = $this->model->getCodeGroup();
        $halfgroups = $this->model->getCodeHalfgroup();
        $action = $_POST['modifvalider'];

        if ($action == 'Valider') {
            $year = filter_input(INPUT_POST, 'modifYear');
            $group = filter_input(INPUT_POST, 'modifGroup');
            $halfgroup = filter_input(INPUT_POST, 'modifHalfgroup');

            $codes = [$year, $group, $halfgroup];
            if ($this->model->modifyStudent($result->ID, $codes)) {
                $this->view->displayModificationValidate($linkManageUser);
            }
        }
        return $this->view->displayModifyStudent($result, $years, $groups, $halfgroups);
    }

    /**
     * Modifie les codes de l'étudiant connecté
     * @return string
     */
    public function modifyMyCodes()
    {
        //On récupère toutes les années, groupes et demi-groupes
        // pour pouvoir permettre à l'utilisateur de les sélectionner lors de la modification
        $current_user = wp_get_current_user();
        $years = $this->model->getCodeYear();
        $groups = $this->model->getCodeGroup();
        $halfgroups = $this->model->getCodeHalfgroup();
        $action = $_POST['modifvalider'];

        if ($action == 'Valider') {
            $year = filter_input(INPUT_POST, 'modifYear');
            $group = filter_input(INPUT_POST, 'modifGroup');
            $halfgroup = filter_input(INPUT_POST, 'modifHalfgroup');

            $codes = [$year, $group, $halfgroup];
            if ($this->model->modifyMyCodes($current_user->ID, $current_user->user_login, $codes)) {
                $this->view->displayModificationValidate();
            }
        }
        return $this->view->displayModifyMyCodes($current_user, $years, $groups, $halfgroups);
    }
}