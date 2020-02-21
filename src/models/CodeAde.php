<?php

namespace Models;

use PDO;

/**
 * Class CodeAde
 *
 * Code ADE entity
 *
 * @package Models
 */
class CodeAde extends Model implements Entity
{

	/**
	 * @var int
	 */
	private $id;

	/**
	 * @var string (year | group | halfGroup)
	 */
	private $type;

	/**
	 * @var string
	 */
	private $title;

	/**
	 * @var string | int
	 */
	private $code;

	/**
	 * @inheritDoc
	 */
	public function create()
	{
		$request = $this->getDatabase()->prepare('INSERT INTO code_ade (type, title, code) VALUES (:type, :title, :code)');

		$request->bindParam(':title', $this->getTitle());
		$request->bindParam(':code', $this->getCode());
		$request->bindParam(':type', $this->getType());

		$request->execute();

		return $this->getDatabase()->lastInsertId();
	}

	/**
	 * @inheritDoc
	 */
	public function update()
	{
		$request = $this->getDatabase()->prepare('UPDATE code_ade SET title = :title, code = :code, type = :type WHERE id = :id');

		$request->bindParam(':id', $this->getId());
		$request->bindParam(':title', $this->getTitle());
		$request->bindParam(':code', $this->getCode());
		$request->bindParam(':type', $this->getType());

		$request->execute();

		return $request->rowCount();
	}

	/**
	 * @inheritDoc
	 */
	public function delete()
	{
		$request = $this->getDatabase()->prepare( 'DELETE FROM code_ade WHERE id = :id');

		$request->bindValue(':id', $this->getId());

		$request->execute();

		return $request->rowCount();
	}

	/**
	 * @inheritDoc
	 */
	public function get($id)
	{
		$request = $this->getDatabase()->prepare('SELECT * FROM code_ade WHERE id = :id');

		$request->bindParam(':id', $id);

		$request->execute();

		return $this->setEntity($request->fetch(PDO::FETCH_ASSOC));
	}

	/**
	 * @inheritDoc
	 */
    public function getAll()
    {
	    $request = $this->getDatabase()->prepare('SELECT * FROM code_ade ORDER BY id DESC');

	    $request->execute();

	    return $this->setListEntity($request->fetchAll(PDO::FETCH_ASSOC));
    }

	/**
	 * @inheritDoc
	 */
	public function setEntity($data)
	{
		$entity = new CodeAde();

		$entity->setId($data['id']);
		$entity->setTitle($data['title']);
		$entity->setCode($data['code']);
		$entity->setType($data['type']);

		return $entity;
	}

	/**
	 * @inheritDoc
	 */
	public function setListEntity($dataList)
	{
		$listEntity = array();
		foreach ($dataList as $data) {
			$listEntity[] = $this->setEntity($data);
		}
		return $listEntity;
	}

	public function checkCode($title, $code)
	{
		$request = $this->getDatabase()->prepare('SELECT * FROM code_ade WHERE title = :title OR code = :code');

		$request->bindParam(':title', $title);
		$request->bindParam(':code', $code);

		$request->execute();

		return $this->setListEntity($request->fetchAll(PDO::FETCH_ASSOC));
	}

	/**
	 * List of codes related to type
	 *
	 * @param $type
	 *
	 * @return CodeAde[]
	 */
    public function getAllFromType($type)
    {
	    $request = $this->getDatabase()->prepare('SELECT * FROM code_ade WHERE type = :type ORDER BY id DESC');

	    $request->bindParam(':type', $type);

	    $request->execute();

	    return $this->setListEntity($request->fetchAll(PDO::FETCH_ASSOC));
    }

	public function getByCode($code)
	{
		$request = $this->getDatabase()->prepare('SELECT * FROM code_ade WHERE code = :code');

		$request->bindParam(':code', $code);

		$request->execute();

		return $this->setEntity($request->fetch(PDO::FETCH_ASSOC));
	}

	public function getByAlert($id)
	{
		$request = $this->getDatabase()->prepare('SELECT * FROM code_ade JOIN code_alert ON code_ade.id = code_alert.id_code_ade WHERE id_alert = :id');

		$request->bindParam(':id', $id);

		$request->execute();

		return $this->setListEntity($request->fetchAll(PDO::FETCH_ASSOC));
	}

	/**
	 * @return int|string
	 */
	public function getCode()
	{
		return $this->code;
	}

	/**
	 * @param $code
	 */
	public function setCode($code)
	{
		$this->code = $code;
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
}