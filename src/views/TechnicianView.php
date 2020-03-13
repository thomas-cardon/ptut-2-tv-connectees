<?php

namespace Views;


use Models\User;

/**
 * Class TechnicianView
 *
 * Contain all view for technician (Forms, tables)
 *
 * @package Views
 */
class TechnicianView extends UserView
{

    /**
     * Display a creation form
     *
     * @return string
     */
    public function displayFormTechnician()
    {
        return '<h2>Compte technicien</h2>' . $this->displayBaseForm('Tech');
    }

	/**
	 * Display all technicians in a table
	 *
	 * @param $users    User[]
	 *
	 * @return string
	 */
    public function displayAllTechnicians($users)
    {
	    $title = 'Techniciens';
	    $name = 'tech';
	    $header = ['Login'];

	    $row = array();
	    $count = 0;
	    foreach ($users as $user) {
		    ++$count;
		    $row[] = [$count, $this->buildCheckbox($name, $user->getId()), $user->getLogin()];
	    }

	    return $this->displayAll($name, $title, $header, $row, 'tech');
    }
}