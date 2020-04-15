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
	    $request = $this->getDatabase()->prepare('INSERT INTO ecran_alert (author, content, creation_date, end_date, for_everyone) VALUES (:author, :content, :creation_date, :end_date, :for_everyone)');

	    $request->bindValue(':author', $this->getAuthor(), PDO::PARAM_INT);
	    $request->bindValue(':content', $this->getContent(), PDO::PARAM_STR);
	    $request->bindValue(':creation_date', $this->getCreationDate(), PDO::PARAM_STR);
	    $request->bindValue(':end_date', $this->getEndDate(), PDO::PARAM_STR);
	    $request->bindValue(':for_everyone', $this->isForEveryone(), PDO::PARAM_INT);

	    $request->execute();

	    $id =  $this->getDatabase()->lastInsertId();

	    foreach ($this->getCodes() as $code) {

	    	if($code !== 'all' || $code !== 0) {
			    $request = $this->getDatabase()->prepare('INSERT INTO ecran_code_alert (id_alert, id_code_ade) VALUES (:idAlert, :idCodeAde)');

			    $request->bindParam(':idAlert', $id, PDO::PARAM_INT);
			    $request->bindValue(':idCodeAde', $code->getId(), PDO::PARAM_INT);

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
		$request = $this->getDatabase()->prepare('UPDATE ecran_alert SET content = :content, end_date = :endDate, for_everyone = :for_everyone WHERE id = :id');

		$request->bindValue(':id', $this->getId(), PDO::PARAM_INT);
		$request->bindValue(':content', $this->getContent(), PDO::PARAM_STR);
		$request->bindValue(':endDate', $this->getEndDate(), PDO::PARAM_STR);
		$request->bindValue(':for_everyone', $this->isForEveryone(), PDO::PARAM_INT);

		$request->execute();

		if(!is_null($this->getAlertLinkToCode()[0])) {
			$request = $this->getDatabase()->prepare('DELETE FROM ecran_code_alert WHERE id_alert = :id_alert');

			$request->bindValue(':id_alert', $this->getId(), PDO::PARAM_INT);

			$request->execute();
		}

		foreach ($this->getCodes() as $code) {

			if($code->getCode() !== 'all' || $code->getCode() !== 0) {
				$request = $this->getDatabase()->prepare('INSERT INTO ecran_code_alert (id_alert, id_code_ade) VALUES (:idAlert, :idCodeAde)');

				$request->bindValue(':idAlert', $this->getId(), PDO::PARAM_INT);
				$request->bindValue(':idCodeAde', $code->getId(), PDO::PARAM_INT);

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
		$request = $this->getDatabase()->prepare('DELETE FROM ecran_alert WHERE id = :id');

		$request->bindValue(':id', $this->getId(), PDO::PARAM_INT);

		$request->execute();

		return $request->rowCount();
	}

	/**
	 * Return the alert corresponding to an ID
	 *
	 * @param $id
	 *
	 * @return Alert | null
	 */
	public function get($id)
	{
		$request = $this->getDatabase()->prepare('SELECT id, content, creation_date, expiration_date, author FROM ecran_alert WHERE alert.id = :id LIMIT 1');

		$request->bindParam(':id', $id, PDO::PARAM_INT);

		$request->execute();

		return $this->setEntity($request->fetch(PDO::FETCH_ASSOC));
	} //get()

    /**
     * @param int $begin
     * @param int $numberElement
     *
     * @return array|Alert[]
     */
    public function getList($begin = 0, $numberElement = 25)
    {
        $request = $this->getDatabase()->prepare("SELECT id, content, creation_date, expiration_date, author FROM ecran_alert ORDER BY id ASC LIMIT :begin, :numberElement");

        $request->bindValue(':begin', (int) $begin, PDO::PARAM_INT);
        $request->bindValue(':numberElement', (int) $numberElement, PDO::PARAM_INT);

        $request->execute();

        if ($request->rowCount() > 0) {
            return $this->setEntityList($request->fetchAll());
        }
        return [];
    }

    /**
     * @param int $begin
     * @param int $numberElement
     *
     * @return array|Alert[]
     */
    public function getAuthorListAlert($begin = 0, $numberElement = 25)
    {
        $request = $this->getDatabase()->prepare("SELECT id, content, creation_date, expiration_date, author FROM ecran_alert  WHERE author = :author ORDER BY id ASC LIMIT :begin, :numberElement");

        $request->bindValue(':begin', (int) $begin, PDO::PARAM_INT);
        $request->bindValue(':numberElement', (int) $numberElement, PDO::PARAM_INT);
        $request->bindParam(':author', $author, PDO::PARAM_INT);

        $request->execute();

        if ($request->rowCount() > 0) {
            return $this->setEntityList($request->fetchAll());
        }
        return [];
    }

	/**
	 * Get all alerts for the user
	 *
	 * @param $id
	 *
	 * @return Alert[]
	 */
	public function getForUser($id)
	{
		$request = $this->getDatabase()->prepare('SELECT ecran_alert.id, content, creation_date, expiration_date, author 
															FROM ecran_alert
															JOIN ecran_code_alert ON ecran_alert.id = ecran_code_alert.id_alert 
															JOIN ecran_code_ade ON ecran_code_alert.id_code_ade = ecran_code_ade.id
															JOIN ecran_code_user ON ecran_code_ade.id = ecran_code_user.id_code_ade
															WHERE ecran_code_user.id_user = :id ORDER BY end_date ASC');

		$request->bindParam(':id', $id, PDO::PARAM_INT);

		$request->execute();

		return $this->setEntityList($request->fetchAll(PDO::FETCH_ASSOC));
	}

	/**
	 * Get all alerts for everyone
	 */
	public function getForEveryone()
	{
		$request = $this->getDatabase()->prepare('SELECT ecran_alert.id, content, creation_date, expiration_date, author FROM ecran_alert WHERE for_everyone = 1 ORDER BY end_date ASC LIMIT 50');

		$request->bindParam(':id', $id, PDO::PARAM_INT);

		$request->execute();

		return $this->setEntityList($request->fetchAll(PDO::FETCH_ASSOC));
	}

	/**
	 * Get all link between the alert and the codes ADE
	 *
	 * @return array|Alert
	 */
	public function getAlertLinkToCode()
	{
		$request = $this->getDatabase()->prepare('SELECT ecran_alert.id, content, creation_date, expiration_date, author FROM code_alert JOIN ecran_alert ON code_alert.id_alert = ecran_alert.id WHERE id_alert = :id_alert LIMIT 50');

		$request->bindValue(':id_alert', $this->getId(), PDO::PARAM_INT);

		$request->execute();

		return $this->setEntityList($request->fetchAll());
	}

    /**
     * @return int
     */
    public function countAll()
    {
        $request = $this->getDatabase()->prepare("SELECT COUNT(*) FROM ecran_alert");

        $request->execute();

        return $request->fetch()[0];
    }

	/**
	 * Build a list of alerts
	 *
	 * @param $dataList
	 *
	 * @return array | Alert
	 */
	public function setEntityList($dataList)
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