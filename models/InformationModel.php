<?php
/**
 * Created by PhpStorm.
 * UserView: Léa Arnaud
 * Date: 17/04/2019
 * Time: 11:34
 */


class InformationModel extends Model {

	private $id;

	private $title;

	private $author;

	private  $creation_date;

	private $end_date;

	private $content;

	private $type;

    /**
     * Add the information in the database with today date and current user.
     *
     * @return int
     */
    public function insertInformation() {

	    // The request for insert the information
	    $sth = $this->getDbh()->prepare('INSERT INTO informations (title, author, creation_date, end_date, content, type) 
                                        VALUES (:title, :author, :creation_date, :end_date, :content, :type)');

	    // Link the param with the value
	    $sth->bindParam(':title', $this->getTitle());
	    $sth->bindParam(':author', $this->getAuthor());
	    $sth->bindParam(':creation_date', $this->getCreationDate());
	    $sth->bindParam(':end_date', $this->getEndDate());
	    $sth->bindParam(':content', $this->getContent());
	    $sth->bindParam(':type', $this->getType());

	    $sth->execute();

	    return $this->getDbh()->lastInsertId();

    } //addInformationDB()

	/**
	 * Modify the information in database
	 */
	public function modifyInformation() {

		// The request for update the information
		$sth = $this->getDbh()->prepare('UPDATE informations 
										SET title = :title, content = :content, end_date = :endDate
                                        WHERE ID_info = :id');

		// Link the param with the value
		$sth->bindParam(':id', $this->getId());
		$sth->bindParam(':title', $this->getTitle());
		$sth->bindParam(':content', $this->getContent());
		$sth->bindParam(':endDate', $this->getEndDate());

		$sth->execute();

		return $sth->rowCount();

	} //modifyInformation()

    /**
     * Delete an information in the database
     */
    public function deleteInformation() {
	    $sth = $this->getDbh()->prepare('DELETE FROM informations
                                         WHERE ID_info = :id');
	    $sth->bindParam(':id', $this->getId());
	    $sth->execute();
	    return $sth->rowCount();

    } //deleteInformationDB()

    /**
     * Return the list of information present in database
     * @return array|null|object
     */
    public function getListInformation() {
	    $sth = $this->getDbh()->prepare('SELECT * 
                                         FROM informations JOIN wp_users ON informations.author = wp_users.ID
                                         ORDER BY end_date ASC');

	    $sth->execute();
	    $results = $sth->fetchAll(PDO::FETCH_ASSOC);

	    return $this->setListInformations($results);

    } //getListInformation()

	/**
	 * Return the list of event present in database
	 * @return array|null|object
	 */
	public function getListInformationEvent() {
		$sth = $this->getDbh()->prepare('SELECT * 
                                         FROM informations JOIN wp_users ON informations.author = wp_users.ID
                                         WHERE type = "event"
                                         ORDER BY end_date ASC');

		$sth->execute();
		$results = $sth->fetchAll(PDO::FETCH_ASSOC);

		// Set the List
		return $this->setListInformations($results);
	} //getListInformation()

    /**
     * Return the list of information created by an user
     * @param $authorId     int id
     * @return array|null|object
     */
    public function getAuthorListInformation($authorId) {
	    $sth = $this->getDbh()->prepare('SELECT * 
                                    	 FROM informations JOIN wp_users ON informations.author = wp_users.ID
                                         WHERE author = :authorId
                                         ORDER BY end_date ASC');

	    $sth->bindParam(':authorId', $authorId);

	    $sth->execute();
	    $results = $sth->fetchAll(PDO::FETCH_ASSOC);

	    // Set the List
	    return $this->setListInformations($results);
    } //getAuthorListInformation()

	/**
	 * Build a list of informations
	 * @param $results
	 *
	 * @return array
	 */
	public function setListInformations($results) {
		$informations = array();
		foreach ($results as $result) {
			$information = new InformationModel();
			$information->setModel($result['ID_info'], $result['title'], $result['user_login'], $result['creation_date'], $result['end_date'], $result['content'], $result['type']);
			$informations[] = $information;
		}
		return $informations;
	}

    /**
     * Return an information corresponding to the ID
     * @param $id   int id
     * @return InformationModel
     */
    public function getInformation($id) {
	    $sth = $this->getDbh()->prepare('SELECT * 
                                           FROM informations
                                           WHERE ID_info = :id');

	    $sth->bindParam(':id', $id);

	    $sth->execute();
	    $information = $sth->fetch(PDO::FETCH_ASSOC);

	    // Set the Model
	    $this->setModel($information['ID_info'], $information['title'], $information['user_login'], $information['creation_date'], $information['end_date'], $information['content'], $information['type']);

	    return $this;

    } //getInformationByID()


	/**
	 * Create an information
	 *
	 * @param $id
	 * @param $title
	 * @param $author
	 * @param $creationDate
	 * @param $endDate
	 * @param $content
	 * @param $type
	 *
	 * @return $this
	 */
	public function setModel($id, $title, $author, $creationDate, $endDate, $content, $type) {

		$this->setId($id);
		$this->setTitle($title);
		$this->setAuthor($author);
		$this->setCreationDate($creationDate);
		$this->setEndDate(date('Y-m-d', strtotime($endDate)));
		$this->setContent($content);
		$this->setType($type);

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
	public function setId( $id ) {
		$this->id = $id;
	}

	/**
	 * @return mixed
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * @param mixed $title
	 */
	public function setTitle( $title ) {
		$this->title = $title;
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
	public function setAuthor( $author ) {
		$this->author = $author;
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
	public function setCreationDate( $creation_date ) {
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
	public function setEndDate( $end_date ) {
		$this->end_date = $end_date;
	}

	/**
	 * @return mixed
	 */
	public function getContent() {
		return $this->content;
	}

	/**
	 * @param mixed $content
	 */
	public function setContent( $content ) {
		$this->content = $content;
	}

	/**
	 * @return mixed
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @param mixed $type
	 */
	public function setType( $type ) {
		$this->type = $type;
	}
}