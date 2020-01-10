<?php
/**
 * Created by PhpStorm.
 * UserView: Rohrb
 * Date: 29/04/2019
 * Time: 09:36
 */

class CodeAdeModel extends Model {

	private $id;

	private $type;

	private $title;

	private $code;

	/**
	 * Insert the code in the database
	 *
	 * @return bool
	 */
	public function insertCode() {
		if (! ($this->checkDuplicate($this->getCode())) && ! ($this->checkDuplicate($this->getTitle()))) {

			$sth = $this->getDbh()->prepare('INSERT INTO code_ade (type, title, code) 
                                         	 VALUES (:type, :title, :code)');

			$sth->bindParam(':type', $this->getType());
			$sth->bindParam(':title', $this->getTitle());
			$sth->bindParam(':code', $this->getCode());

			$sth->execute();

			return true;
		} else {
			return false;
		}
	}

	/**
	 * Modify code Ade
	 */
	public function modifyCodeAde() {
		if(! $this->checkDuplicate($this->getTitle(), $this) && ! $this->checkDuplicate($this->getCode(), $this)) {
			$sth = $this->getDbh()->prepare('UPDATE code_ade 
											 SET title = :title, code = :code, type = :type 
										     WHERE ID = :id');
			$sth->bindParam(':id', $this->getId());
			$sth->bindParam(':title', $this->getTitle());
			$sth->bindParam(':code', $this->getCode());
			$sth->bindParam(':type', $this->getType());
			$sth->execute();

			return $sth->rowCount();
		} else {
			return false;
		}
	}

	/**
	 * Delete code ade
	 */
	public function deleteCode() {
		$this->deleteTuple('code_ade', $this->getId());
	}

	/**
	 * Give the list of all codes ade
	 *
	 * @return array
	 */
    public function getCodeAdeList() {
	    $sth = $this->getDbh()->prepare('SELECT * 
                                         FROM code_ade
                                         ORDER BY ID DESC');

	    $sth->execute();
	    $results = $sth->fetchAll(PDO::FETCH_ASSOC);

	    return $this->setListCodeAde($results);
    }

	/**
	 * List of codes related to type
	 * @param $type
	 *
	 * @return array
	 */
    public function getCodeAdeListType($type) {
    	$sth = $this->getDbh()->prepare('SELECT * 
                                         FROM code_ade
                                         WHERE type = :type
                                         ORDER BY ID DESC');

    	$sth->bindParam(':type', $type);

    	$sth->execute();
	    $results = $sth->fetchAll(PDO::FETCH_ASSOC);

	    return $this->setListCodeAde($results);
    }

	/**
	 * Create a list of codes
	 * @param $results
	 *
	 * @return array
	 */
    public function setListCodeAde($results) {
	    $codesAde = array();
	    foreach ($results as $result) {
		    $codeAde = new CodeAdeModel();
		    $codeAde->setModel($result['ID'], $result['type'], $result['title'], $result['code']);
		    $codesAde[] = $codeAde;
	    }
	    return $codesAde;
    }

	/**
	 * Check duplicate codes or title
	 * @param $value        int | string Value to check
	 * @param $codeExist    CodeAdeModel
	 *
	 * @return bool
	 */
	protected function checkDuplicate($value, $codeExist = null) {
		$codesAde = $this->getCodeAdeList();

		// Remove the code Ade from the checklist
		if($codeExist) {
			for($i = 0; $i < sizeof($codesAde); ++$i) {
				if($codesAde[$i]->getId() == $codeExist->getId()) {
					unset($codesAde[$i]);
				}
			}
		}

		// Check each code Ade
		foreach ($codesAde as $codeAde) {
			if($codeAde->getCode() == $value || $codeAde->getTitle() == $value) {
				return true;
			}
		}
		return false;
	}

    /**
     * Get code ade link to the id
     * @param $id   int
     * @return CodeAdeModel
     */
    public function getCodeAde($id) {
        $sth = $this->getDbh()->prepare('SELECT * 
										 FROM code_ade 
										 WHERE ID = :id');
        $sth->bindParam(':id', $id);

	    $sth->execute();
	    $codeAde = $sth->fetch(PDO::FETCH_ASSOC);

	    $this->setModel($codeAde['ID'], $codeAde['type'], $codeAde['title'], $codeAde['code']);

        return $this;
    }

	/**
	 * Create a code ade
	 *
	 * @param $id
	 * @param $type
	 * @param $title
	 * @param $code
	 *
	 * @return $this
	 */
    public function setModel($id, $type, $title, $code) {

    	$this->setId($id);
	    $this->setType($type);
    	$this->setTitle($title);
    	$this->setCode($code);

    	return $this;
    }

	/**
	 * @return mixed
	 */
	public function getCode() {
		return $this->code;
	}

	/**
	 * @param mixed $code
	 */
	public function setCode( $code ) {
		$this->code = $code;
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
	public function getType() {
		return $this->type;
	}

	/**
	 * @param mixed $type
	 */
	public function setType( $type ) {
		$this->type = $type;
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
}