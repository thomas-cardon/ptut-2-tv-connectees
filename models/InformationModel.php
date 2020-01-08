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
	    $req = $this->getDb()->prepare('INSERT INTO informations (title, author, creation_date, end_date, content, type) 
                                        VALUES (:title, :author, :creation_date, :end_date, :content, :type)');

	    // Link the param with the value
	    $req->bindParam(':title', $this->getTitle());
	    $req->bindParam(':author', $this->getAuthor());
	    $req->bindParam(':creation_date', $this->getCreationDate());
	    $req->bindParam(':end_date', $this->getEndDate());
	    $req->bindParam(':content', $this->getContent());
	    $req->bindParam(':type', $this->getType());

	    $req->execute();

	    return $this->getDb()->lastInsertId();

    } //addInformationDB()

	/**
	 * Modify the information in database
	 */
	public function modifyInformation() {

		// The request for update the information
		$req = $this->getDb()->prepare('UPDATE informations 
										SET title = :title, content = :content, end_date = :endDate
                                        WHERE ID_info = :id');

		// Link the param with the value
		$req->bindParam(':id', $this->getId());
		$req->bindParam(':title', $this->getTitle());
		$req->bindParam(':content', $this->getContent());
		$req->bindParam(':endDate', $this->getEndDate());

		$req->execute();

	} //modifyInformation()

    /**
     * Delete an information in the database
     */
    public function deleteInformation() {
	    $result = $this->getDb()->prepare('DELETE FROM informations
                                           WHERE ID_info = :id');
	    $result->bindParam(':id', $this->getId());
	    $result->execute();
	    return $result->rowCount();

    } //deleteInformationDB()

    /**
     * Return the list of information present in database
     * @return array|null|object
     */
    public function getListInformation() {
	    $result = $this->getDb()->prepare('SELECT * 
                                           FROM informations JOIN wp_users ON informations.author = wp_users.ID
                                           ORDER BY end_date DESC');

	    $result->execute();
	    $results = $information = $result->fetchAll(PDO::FETCH_ASSOC);

	    return $this->setListInformations($results);

    } //getListInformation()

	/**
	 * Return the list of event present in database
	 * @return array|null|object
	 */
	public function getListInformationEvent() {
		$result = $this->getDb()->prepare('SELECT * 
                                           FROM informations JOIN wp_users ON informations.author = wp_users.ID
                                           WHERE type = "event"
                                           ORDER BY end_date DESC');


		$result->execute();
		$results = $result->fetchAll(PDO::FETCH_ASSOC);

		// Set the List
		return $this->setListInformations($results);
	} //getListInformation()

    /**
     * Return the list of information created by an user
     * @param $authorId     int id
     * @return array|null|object
     */
    public function getAuthorListInformation($authorId) {
	    $result = $this->getDb()->prepare('SELECT * 
                                           FROM informations JOIN wp_users ON informations.author = wp_users.ID
                                           WHERE author = :authorId
                                           ORDER BY end_date DESC');

	    $result->bindParam(':authorId', $authorId);

	    $result->execute();
	    $results = $result->fetchAll(PDO::FETCH_ASSOC);

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
			$information->setModel($result['ID_info'], $result['title'], $result['author'], $result['creation_date'], $result['end_date'], $result['content'], $result['type']);
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
	    $result = $this->getDb()->prepare('SELECT * 
                                           FROM informations JOIN wp_users ON informations.author = wp_users.ID
                                           WHERE ID_info = :id');

	    $result->bindParam(':id', $id);

	    $result->execute();
	    $information = $result->fetch(PDO::FETCH_ASSOC);

	    // Set the Model
	    $this->setModel($information['ID_info'], $information['title'], $information['author'], $information['creation_date'], $information['end_date'], $information['content'], $information['type']);

	    return $this;

    } //getInformationByID()


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