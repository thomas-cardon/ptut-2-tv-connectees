<?php

namespace Models;

use PDO;

/**
 * Class Alert
 *
 * Alert entity
 *
 * @package Models
 */
class Alert extends Model implements Entity
{

	/**
	 * @var int
	 */
	private $id;

	/**
	 * @var User
	 */
	private $author;

	/**
	 * @var string
	 */
	private $content;

	/**
	 * @var string
	 */
	private $creation_date;

	/**
	 * @var string
	 */
	private $end_date;

	/**
	 * @var CodeAde[]
	 */
	private $codes;

	/**
	 * @var int
	 */
	private $forEveryone;


    /**
     * Add an alert in the database with today date and current user.
     */
    public function create()
    {
	    $request = $this->getDatabase()->prepare('INSERT INTO alert (author, content, creation_date, end_date, for_everyone) VALUES (:author, :content, :creation_date, :end_date, :for_everyone)');

	    $request->bindValue(':author', $this->getAuthor());
	    $request->bindValue(':content', $this->getContent());
	    $request->bindValue(':creation_date', $this->getCreationDate());
	    $request->bindValue(':end_date', $this->getEndDate());
	    $request->bindValue(':for_everyone', $this->isForEveryone());

	    $request->execute();

	    $id =  $this->getDatabase()->lastInsertId();

	    foreach ($this->getCodes() as $code) {

	    	if($code !== 'all' || $code !== 0) {
			    $request = $this->getDatabase()->prepare('INSERT INTO code_alert (id_alert, id_code_ade) VALUES (:idAlert, :idCodeAde)');

			    $request->bindParam(':idAlert', $id);
			    $request->bindValue(':idCodeAde', $code->getId());

			    $request->execute();
		    }
	    }

	    return $id;
    } //create()

	/**
	 * Modify the information in database
	 */
	public function update()
	{
		$request = $this->getDatabase()->prepare('UPDATE alert SET content = :content, end_date = :endDate, for_everyone = :for_everyone WHERE id = :id');

		$request->bindValue(':id', $this->getId());
		$request->bindValue(':content', $this->getContent());
		$request->bindValue(':endDate', $this->getEndDate());
		$request->bindValue(':for_everyone', $this->isForEveryone());

		$request->execute();

		if(!is_null($this->getAlertLinkToCode()[0])) {
			$request = $this->getDatabase()->prepare('DELETE FROM code_alert WHERE id_alert = :id_alert');

			$request->bindValue(':id_alert', $this->getId());

			$request->execute();
		}

		foreach ($this->getCodes() as $code) {

			if($code->getCode() !== 'all' || $code->getCode() !== 0) {
				$request = $this->getDatabase()->prepare('INSERT INTO code_alert (id_alert, id_code_ade) VALUES (:idAlert, :idCodeAde)');

				$request->bindValue(':idAlert', $this->getId());
				$request->bindValue(':idCodeAde', $code->getId());

				$request->execute();
			}
		}

		return $request->rowCount();
	} //update()

	/**
	 * Delete an alert in the database
	 */
	public function delete()
	{
		$request = $this->getDatabase()->prepare('DELETE FROM alert WHERE id = :id');

		$request->bindValue(':id', $this->getId());

		$request->execute();

		if(!is_null($this->getAlertLinkToCode()[0])) {
			$request = $this->getDatabase()->prepare('DELETE FROM code_alert WHERE id_alert = :id');

			$request->bindValue(':id', $this->getId());

			$request->execute();
		}

		return $request->rowCount();
	} //delete()

	/**
	 * Return the alert corresponding to an ID
	 *
	 * @param $id
	 *
	 * @return Alert | null
	 */
	public function get($id)
	{
		$request = $this->getDatabase()->prepare('SELECT *
															FROM alert 
															JOIN wp_users ON alert.author = wp_users.ID 
															WHERE alert.id = :id');

		$request->bindParam(':id', $id);

		$request->execute();

		return $this->setEntity($request->fetch(PDO::FETCH_ASSOC));
	} //get()

    /**
     * Return the list of alerts.
     *
     * @return Alert[]
     */
    public function getAll()
    {
	    $request = $this->getDatabase()->prepare('SELECT * FROM alert JOIN wp_users ON alert.author = wp_users.ID ORDER BY end_date ASC');

	    $request->execute();

	    return $this->setListEntity($request->fetchAll(PDO::FETCH_ASSOC));
    } //getAll()

    /**
     * Return the list of alerts created by an user
     *
     * @param $author
     *
     * @return Alert[]
     */
    public function getAuthorListAlert($author)
    {
	    $request = $this->getDatabase()->prepare('SELECT * FROM alert JOIN wp_users ON alert.author = wp_users.ID WHERE author = :author ORDER BY end_date ASC');

	    $request->bindParam(':author', $author);

	    $request->execute();

	    return $this->setListEntity($request->fetchAll(PDO::FETCH_ASSOC));
    } //getAuthorListAlert()

	/**
	 * Get all alerts for the user
	 *
	 * @param $id
	 *
	 * @return Alert[]
	 */
	public function getForUser($id)
	{
		$request = $this->getDatabase()->prepare('SELECT * 
															FROM alert
															JOIN code_alert ON alert.id = code_alert.id_alert 
															JOIN code_ade ON code_alert.id_code_ade = code_ade.id
															JOIN code_user ON code_ade.id = code_user.id_code_ade
															WHERE code_user.id_user = :id ORDER BY end_date ASC');

		$request->bindParam(':id', $id);

		$request->execute();

		return $this->setListEntity($request->fetchAll(PDO::FETCH_ASSOC));
	}

	/**
	 * Get all alerts for everyone
	 */
	public function getForEveryone()
	{
		$request = $this->getDatabase()->prepare('SELECT * FROM alert WHERE for_everyone = 1 ORDER BY end_date ASC');

		$request->bindParam(':id', $id);

		$request->execute();

		return $this->setListEntity($request->fetchAll(PDO::FETCH_ASSOC));
	}

	public function getAlertLinkToCode()
	{
		$request = $this->getDatabase()->prepare('SELECT * FROM code_alert JOIN alert ON code_alert.id_alert = alert.id WHERE id_alert = :id_alert');

		$request->bindValue(':id_alert', $this->getId());

		$request->execute();

		return $this->setListEntity($request->fetchAll());
	}

	/**
	 * Build a list of alerts
	 *
	 * @param $dataList
	 *
	 * @return array | Alert
	 */
	public function setListEntity($dataList)
	{
		$listEntity = array();
		foreach ($dataList as $data) {
			$listEntity[] = $this->setEntity($data);
		}
		return $listEntity;
	}

	/**
	 * Create an alert
	 *
	 * @param $data
	 *
	 * @return Alert
	 */
	public function setEntity($data)
	{
		$entity = new Alert();
		$author = new User();
		$codeAde = new CodeAde();

		$entity->setId($data['id']);
		$entity->setAuthor($author->get($data['author']));
		$entity->setContent($data['content']);
		$entity->setCreationDate(date('Y-m-d', strtotime($data['creation_date'])));
		$entity->setEndDate(date('Y-m-d', strtotime($data['end_date'])));
		$entity->setForEveryone($data['for_everyone']);

		$codes = array();

		foreach ($codeAde->getByAlert($data['id']) as $code) {
			$codes[] = $code;
		}

		if(sizeof($codes) <= 0) {
			if($entity->isForEveryone()) {
				$codeAde->setTitle('Tous');
				$codeAde->setCode('all');
				$codes[] = $codeAde;
			} else {
				$codeAde->setTitle('Aucun');
				$codeAde->setCode('0');
				$codes[] = $codeAde;
			}
		}

		$entity->setCodes($codes);

		return $entity;
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param $id
	 */
	public function setId($id)
	{
		$this->id = $id;
	}

	/**
	 * @return User
	 */
	public function getAuthor()
	{
		return $this->author;
	}

	/**
	 * @param $author
	 */
	public function setAuthor($author)
	{
		$this->author = $author;
	}

	/**
	 * @return string
	 */
	public function getContent()
	{
		return $this->content;
	}

	/**
	 * @param $content
	 */
	public function setContent($content)
	{
		$this->content = $content;
	}

	/**
	 * @return string
	 */
	public function getCreationDate()
	{
		return $this->creation_date;
	}

	/**
	 * @param $creation_date
	 */
	public function setCreationDate($creation_date)
	{
		$this->creation_date = $creation_date;
	}

	/**
	 * @return string
	 */
	public function getEndDate()
	{
		return $this->end_date;
	}

	/**
	 * @param $end_date
	 */
	public function setEndDate($end_date)
	{
		$this->end_date = $end_date;
	}

	/**
	 * @return CodeAde[]
	 */
	public function getCodes()
	{
		return $this->codes;
	}

	/**
	 * @param CodeAde[] $codes
	 */
	public function setCodes($codes)
	{
		$this->codes = $codes;
	}

	/**
	 * @return int
	 */
	public function isForEveryone()
	{
		return $this->forEveryone;
	}

	/**
	 * @param int $forEveryone
	 */
	public function setForEveryone( $forEveryone )
	{
		$this->forEveryone = $forEveryone;
	}
}