<?php
/**
 * Created by PhpStorm.
 * UserView: SFW
 * Date: 06/05/2019
 * Time: 11:01
 */

class AlertModel extends Model {

	private $id;

	private $author;

	private $text;

	private $creation_date;

	private $end_date;

	private $codes;


    /**
     * Add an alert in the database with today date and current user.
     */
    public function insertAlert() {

	    // The request for insert the alert
	    $sth = $this->getDbh()->prepare('INSERT INTO alerts (author, text, creation_date, end_date, codes) 
                                        VALUES (:author, :text, :creation_date, :end_date, :codes)');

	    $codes = serialize($this->getCodes());
	    $this->setCodes($codes);

	    // Link the param with the value
	    $sth->bindParam(':author', $this->getAuthor());
	    $sth->bindParam(':text', $this->getText());
	    $sth->bindParam(':creation_date', $this->getCreationDate());
	    $sth->bindParam(':end_date', $this->getEndDate());
	    $sth->bindParam(':codes', $codes);

	    $sth->execute();

	    return $this->getDbh()->lastInsertId();
    } //addAlertDB()

	/**
	 * Modify the information in database
	 */
	public function modifyAlert() {
		// The request for update the information
		$sth = $this->getDbh()->prepare('UPDATE alerts 
										SET text = :text, end_date = :endDate, codes = :codes
                                        WHERE ID_alert = :id');

		$codes = serialize($this->getCodes());
		$this->setCodes($codes);
		// Link the param with the value
		$sth->bindParam(':id', $this->getId());
		$sth->bindParam(':text', $this->getText());
		$sth->bindParam(':endDate', $this->getEndDate());
		$sth->bindParam(':codes', $codes);

		$sth->execute();

		return $sth->rowCount();

	} //modifyInformation()

	/**
	 * Delete an alert in the database
	 */
	public function deleteAlert() {
		$sth = $this->getDbh()->prepare('DELETE FROM alerts
                                         WHERE ID_alert = :id');
		$sth->bindParam(':id', $this->getId());
		$sth->execute();
		return $sth->rowCount();

	} //deleteAlert()

    /**
     * Return the list of alerts.
     * @return array|null|object
     */
    public function getListAlert() {
	    $sth = $this->getDbh()->prepare('SELECT * 
                                         FROM alerts JOIN wp_users ON alerts.author = wp_users.ID
                                         ORDER BY end_date ASC');

	    $sth->execute();
	    $results = $sth->fetchAll(PDO::FETCH_ASSOC);

	    return $this->setListAlerts($results);
    } //getListAlert()

    /**
     * Return the alert corresponding to an ID
     * @param $id
     * @return array|null|object|void
     */
    public function getAlert($id) {
	    $sth = $this->getDbh()->prepare('SELECT * 
                                         FROM alerts JOIN wp_users ON alerts.author = wp_users.ID
                                         WHERE ID_alert = :id');

	    $sth->bindParam(':id', $id);

	    $sth->execute();
	    $alert = $sth->fetch(PDO::FETCH_ASSOC);

	    // Set the Model
	    $this->setModel($alert['ID_alert'], $alert['user_login'], $alert['text'], $alert['creation_date'], $alert['end_date'], $alert['codes']);

	    return $this;
    } //getAlert()

    /**
     * Return the list of alerts created by an user
     * @param $authorId
     * @return array|null|object
     */
    public function getAuthorListAlert($authorId) {
	    $sth = $this->getDbh()->prepare('SELECT * 
                                    	 FROM alerts JOIN wp_users ON alerts.author = wp_users.ID
                                         WHERE author = :authorId
                                         ORDER BY end_date ASC');

	    $sth->bindParam(':authorId', $authorId);

	    $sth->execute();
	    $results = $sth->fetchAll(PDO::FETCH_ASSOC);

	    // Set the List
	    return $this->setListAlerts($results);
    } //getAuthorListAlert()

	/**
	 * Build a list of alerts
	 * @param $results
	 *
	 * @return array
	 */
	public function setListAlerts($results) {
		$alerts = array();
		foreach ($results as $result) {
			$alert = new AlertModel();
			$alert->setModel($result['ID_alert'], $result['user_login'], $result['text'], $result['creation_date'], $result['end_date'], $result['codes']);
			$alerts[] = $alert;
		}
		return $alerts;
	}

	/**
	 * Create an alert
	 *
	 * @param $id
	 * @param $text
	 * @param $author
	 * @param $creationDate
	 * @param $endDate
	 * @param $codes
	 *
	 * @return $this
	 */
	public function setModel($id, $author, $text, $creationDate, $endDate, $codes) {

		$this->setId($id);
		$this->setAuthor($author);
		$this->setText($text);
		$this->setCreationDate($creationDate);
		$this->setEndDate(date('Y-m-d', strtotime($endDate)));
		$this->setCodes($codes);

		return $this;
	}


	/**
	 * @return mixed
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param mixed $id
	 */
	public function setId($id) {
		$this->id = $id;
	}

	/**
	 * @return mixed
	 */
	public function getAuthor() {
		return $this->author;
	}

	/**
	 * @param mixed $author
	 */
	public function setAuthor($author) {
		$this->author = $author;
	}

	/**
	 * @return mixed
	 */
	public function getText() {
		return $this->text;
	}

	/**
	 * @param mixed $text
	 */
	public function setText($text) {
		$this->text = $text;
	}

	/**
	 * @return mixed
	 */
	public function getCreationDate() {
		return $this->creation_date;
	}

	/**
	 * @param mixed $creation_date
	 */
	public function setCreationDate($creation_date) {
		$this->creation_date = $creation_date;
	}

	/**
	 * @return mixed
	 */
	public function getEndDate() {
		return $this->end_date;
	}

	/**
	 * @param mixed $end_date
	 */
	public function setEndDate($end_date) {
		$this->end_date = $end_date;
	}

	/**
	 * @return mixed
	 */
	public function getCodes() {
		return $this->codes;
	}

	/**
	 * @param mixed $codes
	 */
	public function setCodes($codes) {
		$this->codes = $codes;
	}

}