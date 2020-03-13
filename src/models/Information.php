<?php

namespace Models;

use PDO;

/**
 * Class Information
 *
 * Information entity
 *
 * @package Models
 */
class Information extends Model implements Entity
{

	/**
	 * @var int
	 */
	private $id;

	/**
	 * @var string
	 */
	private $title;

	/**
	 * @var User
	 */
	private $author;

	/**
	 * @var string
	 */
	private  $creation_date;

	/**
	 * @var string
	 */
	private $end_date;

	/**
	 * @var string
	 */
	private $content;

	/**
	 * @var string (Text | Image | excel | PDF | Event)
	 */
	private $type;

    /**
     * Add the information in the database with today date and current user.
     *
     * @return int
     */
    public function create()
    {
	    $request = $this->getDatabase()->prepare('INSERT INTO informations (title, author, creation_date, end_date, content, type) VALUES (:title, :author, :creation_date, :end_date, :content, :type)');

	    $request->bindParam(':title', $this->getTitle(), PDO::PARAM_STR);
	    $request->bindParam(':author', $this->getAuthor(), PDO::PARAM_INT);
	    $request->bindParam(':creation_date', $this->getCreationDate(), PDO::PARAM_STR);
	    $request->bindParam(':end_date', $this->getEndDate(), PDO::PARAM_STR);
	    $request->bindParam(':content', $this->getContent(), PDO::PARAM_STR);
	    $request->bindParam(':type', $this->getType(), PDO::PARAM_STR);

	    $request->execute();

	    return $this->getDatabase()->lastInsertId();
    }

	/**
	 * Modify the information in database
	 */
	public function update()
	{
		$request = $this->getDatabase()->prepare('UPDATE informations SET title = :title, content = :content, end_date = :endDate WHERE ID_info = :id');

		$request->bindParam(':id', $this->getId(), PDO::PARAM_INT);
		$request->bindParam(':title', $this->getTitle(), PDO::PARAM_STR);
		$request->bindParam(':content', $this->getContent(), PDO::PARAM_STR);
		$request->bindParam(':endDate', $this->getEndDate(), PDO::PARAM_STR);

		$request->execute();

		return $request->rowCount();
	} //modifyInformation()

    /**
     * Delete an information in the database
     */
    public function delete()
    {
	    $request = $this->getDatabase()->prepare('DELETE FROM informations WHERE ID_info = :id');

	    $request->bindValue(':id', $this->getId(), PDO::PARAM_INT);

	    $request->execute();

	    return $request->rowCount();
    } //deleteInformation()

    /**
     * Return the list of information present in database
     * @return array|null|object
     */
    public function getAll()
    {
	    $request = $this->getDatabase()->prepare('SELECT * FROM informations JOIN wp_users ON informations.author = wp_users.ID ORDER BY end_date ASC');

	    $request->execute();

	    return $this->setListEntity($request->fetchAll(PDO::FETCH_ASSOC));
    } //getListInformation()

	/**
	 * Return the list of event present in database
	 * @return array|null|object
	 */
	public function getListInformationEvent()
	{
		$request = $this->getDatabase()->prepare('SELECT * FROM informations JOIN wp_users ON informations.author = wp_users.ID WHERE type = "event" ORDER BY end_date ASC');

		$request->execute();

		return $this->setListEntity($request->fetchAll(PDO::FETCH_ASSOC));
	} //getListInformation()

    /**
     * Return the list of information created by an user
     *
     * @param $authorId     int id
     *
     * @return Information[]
     */
    public function getAuthorListInformation($author)
    {
	    $request = $this->getDatabase()->prepare( 'SELECT * FROM informations JOIN wp_users ON informations.author = wp_users.ID WHERE author = :author ORDER BY end_date');

	    $request->bindParam(':author', $author, PDO::PARAM_INT);

	    $request->execute();

	    return $this->setListEntity($request->fetchAll(PDO::FETCH_ASSOC));
    } //getAuthorListInformation()

    /**
     * Return an information corresponding to the ID
     *
     * @param $id   int id
     *
     * @return Information
     */
    public function get($id)
    {
	    $request = $this->getDatabase()->prepare('SELECT * FROM informations JOIN wp_users ON informations.author = wp_users.ID WHERE ID_info = :id');

	    $request->bindParam(':id', $id, PDO::PARAM_INT);

	    $request->execute();

	    return $this->setEntity($request->fetch(PDO::FETCH_ASSOC));
    } //getInformationByID()

	/**
	 * Build a list of informations
	 *
	 * @param $dataList
	 *
	 * @return array | Information
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
	 * Create an information
	 *
	 * @param $data
	 *
	 * @return $this
	 */
	public function setEntity($data)
	{
		$entity = new Information();
		$user = new User();

		$entity->setId($data['ID_info']);
		$entity->setTitle($data['title']);
		$entity->setAuthor($user->get($data['author']));
		$entity->setCreationDate(date('Y-m-d', strtotime($data['creation_date'])));
		$entity->setEndDate(date('Y-m-d', strtotime($data['end_date'])));
		$entity->setContent($data['content']);
		$entity->setType($data['type']);

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
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * @param $title
	 */
	public function setTitle($title)
	{
		$this->title = $title;
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
	public function getCreationDate()
	{
		return $this->creation_date;
	}

	/**
	 * @param mixed $creation_date
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
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @param $type
	 */
	public function setType($type)
	{
		$this->type = $type;
	}
}