<?php

class Xfrom26_Model_WordMapper
{
	protected $_dbTable;
 
    public function setDbTable($dbTable)
    {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }
 
    public function getDbTable()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable('Xfrom26_Model_DbTable_Word');
        }
        return $this->_dbTable;
    }
 
    public function save(Xfrom26_Model_Word $word)
    {
        $data = array(
            'word'		=> $word->getWord(),
            'profile'	=> $word->getProfile(),
			'unit'		=> $word->getUnit(),
			'pos'		=> $word->getPos(),
			'is_double' => $word->getIsDouble(),
			'len'		=> $word->getLength(),
			'definition'=> $word->getDefinition(),
			'example'	=> $word->getExample());
 
		$data['len'] = strlen($data['word']);
        if (null === ($id = $word->getId())) {
            unset($data['id']);
            $id = (int)$this->getDbTable()->insert($data);
			$word->setId($id);
        } else {
            $this->getDbTable()->update($data, array('id = ?' => $id));
        }
		return $word;
    }
 
	public function load(Xfrom26_Model_Word $word, $row) {
        return $word->setId($row->id)
			->setWord($row->word)
			->setProfile($row->profile)
			->setUnit($row->unit)
			->setPos($row->pos)
			->setIsDouble($row->is_double)
			->setLength($row->len)
			->setDefinition($row->definition)
			->setExample($row->example);
	}

    public function find($id, Xfrom26_Model_Word $word)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return $word;
        }
		return $this->load($word, $result->current());
    }
 
	public function findUnique(Xfrom26_Model_Word $word, $value, $pos, $profile) {
		$result = $this->getDbTable()->fetchAll(
			$this->getDbTable()
				->select()
				->where("word = ?", $value)
				->where("pos = ?", $pos)
				->where("profile =?", $profile));
        if (0 == count($result)) {
            return $word;
        }
		return $this->load($word, $result->current());
	}

	public function fetchRandomWord(Xfrom26_Model_Word $word, $profile, $maxLength = 6, array $pos = array()) {
		$sql = $this->getDbTable()
			->select()
			->from(array('w' => 'wordlist'))
			->columns('id')
			->where('profile = ?', $profile)
			->where('len > 2 and len <= ?', $maxLength);
		if (!empty($pos)) {
			$sql = $sql->where("pos IN (" . join("',''", $pos). "')");
		}
		//echo $sql;
		$resultSet = $this->getDbTable()->fetchAll($sql);
		$idList = array();
		foreach ($resultSet as $row) {
			$idList[] = $row;
		}
		if (!empty($idList)) {
			$rnd = $idList[rand(1, count($idList) - 1)];
			$this->find($rnd['id'], $word);
		}
		return $word;

	}

    public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row) {
            $entries[] = $this->load(new Xfrom26_Model_Word(), $row);
        }
        return $entries;
    }

}

