<?php

namespace Controllers;

use Models\CodeAde;
use Models\User;
use Views\StudentView;
use WP_User;

/**
 * Class StudentController
 *
 * Manage users (Create, update, delete, display)
 *
 * @package Controllers
 */
class StudentController extends UserController implements Schedule
{

    /**
     * @var User
     */
    private $model;

	/**
	 * @var StudentView
	 */
	public $view;

    /**
     * Constructor of StudentController.
     */
    public function __construct()
    {
	    parent::__construct();
	    $this->model = new User();
        $this->view = new StudentView();
    }

    /**
     * Insert all users in the excel's file
     */
    public function insert()
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
                // On vérifie s'il s'agit bien du bon fichier excel
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
	                        $this->model->setLogin($login);
	                        $this->model->setPassword($hashpass);
	                        $this->model->setEmail($email);
	                        $this->model->setRole('etudiant');

                            if (!$this->checkDuplicateUser($this->model) &&
                                $this->model->create()) {

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
                                 </html>';

                                $headers = array('Content-Type: text/html; charset=UTF-8');

                                wp_mail($to, $subject, $message, $headers);
                            } else {
                                array_push($doubles, $login);
                            }
                        }
                    }
                    if (!is_null($doubles[0])) {
                        $this->view->displayErrorDouble($doubles);
                    } else {
                        $this->view->displayInsertValidate();
                    }
                } else {
                    $this->view->displayWrongFile();
                }
            } else {
                $this->view->displayWrongExtension();
            }
        }
        return $this->view->displayInsertImportFileStudent();
    }

	/**
	 * Modify a student
	 *
	 * @param $user   User
	 *
	 * @return string
	 */
	public function modify($user)
	{
		$page = get_page_by_title('Gestion des utilisateurs');
		$linkManageUser = get_permalink($page->ID);

		$action = filter_input(INPUT_POST, 'modifvalider');

		$codeAde    = new CodeAde();

		if ($action == 'Valider') {
			$year      = filter_input(INPUT_POST, 'modifYear');
			$group     = filter_input(INPUT_POST, 'modifGroup');
			$halfGroup = filter_input(INPUT_POST, 'modifHalfgroup');

			if(is_numeric($year) && is_numeric($group) && is_numeric($halfGroup)) {

				$codes = [$year, $group, $halfGroup];
				$codesAde = array();
				foreach ($codes as $code) {
					if($code !== 0) {
						$code = $codeAde->getByCode($code);
					}
					$codesAde[] = $code;
				}

				if($codesAde[0]->getType() !== 'year') {
					$codesAde[0] = 0;
				}

				if($codesAde[1]->getType() !== 'group') {
					$codesAde[1] = 0;
				}

				if($codesAde[2]->getType() !== 'halfGroup') {
					$codesAde[2] = 0;
				}

				$user->setCodes($codesAde);
				if($user->update()) {
					$this->view->displayModificationValidate($linkManageUser);
				}
			}
		}

		$years      = $codeAde->getAllFromType('year');
		$groups     = $codeAde->getAllFromType('group');
		$halfGroups = $codeAde->getAllFromType('halfGroup');

		return $this->view->displayModifyStudent($user, $years, $groups, $halfGroups);
	}

	/**
	 * Display Schedule
	 *
	 * @return bool|mixed|string
	 */
	public function displayMySchedule()
	{
		$current_user = wp_get_current_user();
		$user = $this->model->get($current_user->ID);

		if(sizeof($user->getCodes()) > 0) {
			$codes = array_reverse($user->getCodes());
			foreach ($codes as $code) {
				if($code instanceof CodeAde) {
					if (file_exists($this->getFilePath($code->getCode()))) {
						return $this->displaySchedule($code->getCode());
					}
				}
			}
		}

		return $this->manageStudent($user);
	}

	/**
	 * Check if the student have a group
	 * If not, ask to select some groups
	 *
	 * @param $user     User
	 *
	 */
	public function manageStudent($user)
	{
		$codeAde = new CodeAde();

		$years = $codeAde->getAllFromType('year');
		$groups = $codeAde->getAllFromType('group');
		$halfGroups = $codeAde->getAllFromType('halfGroup');

		$this->view->selectSchedules($years, $groups, $halfGroups);

		$action = filter_input(INPUT_POST, 'addSchedules');

		if ($action) {

			$year = filter_input(INPUT_POST, 'selectYears');
			$group = filter_input(INPUT_POST, 'selectGroups');
			$halfGroup = filter_input(INPUT_POST, 'selectHalfGroups');

			if(is_numeric($year) && is_numeric($group) && is_numeric($halfGroup)) {

				$codes = [$year, $group, $halfGroup];
				$codesAde = [];
				foreach ($codes as $code) {
					if($code !== 0) {
						$code = $codeAde->getByCode($code);
					}
					$codesAde[] = $code;
				}

				if($codesAde[0]->getType() !== 'year') {
					$codesAde[0] = 0;
				}

				if($codesAde[1]->getType() !== 'group') {
					$codesAde[1] = 0;
				}

				if($codesAde[2]->getType() !== 'halfGroup') {
					$codesAde[2] = 0;
				}

				$user->setCodes($codesAde);
				$user->update();
			}
		}
	}

    /**
     * Display all users in a table
     */
    function displayAllStudents()
    {
        $users = $this->model->getUsersByRole('etudiant');
        return $this->view->displayAllStudent($users);
    }
}