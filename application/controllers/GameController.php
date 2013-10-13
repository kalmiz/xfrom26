<?php

class GameController extends Zend_Controller_Action
{

    protected $session = null;

    public function init()
    {
		$this->wordMapper = new Xfrom26_Model_WordMapper();
        $this->session = new Zend_Session_Namespace("Xfrom26Session");
    }

    public function indexAction()
    {
		$this->view->profile = $this->session->profile; 
		$this->view->counter = $this->session->counter;
		$this->view->word = $this->session->word;
    }

    public function newAction()
    {
		$this->session->profile = "A1";
        $this->session->word = $this->wordMapper->fetchRandomWord(new Xfrom26_Model_Word(), $this->session->profile);
		$this->session->counter = 0;
		$this
			->getResponse()
			->setRedirect("/game");
    }

	/**
	 * Check the word
	 *
	 * @param string $word
	 */
	private function validateWord($word)
	{
		if (!$this->wordMapper->exists($word))
		{
			if (strpos($word, '"') === false && strpos($word, '..') === false && strpos($word, '/') === false)
			{
				$output = array();
				$cmd = 'echo "'.$word.'" | aspell --lang=en_US -a';
				$output = shell_exec($cmd);
				var_dump($output);
				if (count($output) > 1)
				{
					return trim($output[1]) === "*";
				}
				else return false;
			} 
			else return false;
		}
		return true;
	}

	// AJAX
    public function checkAction()
    {
		$this->session->counter += 1;
		$bet = trim($this->getRequest()->word);
		$word = $this->session->word->getWord();
		$valid = $this->validateWord($bet);
		$ret = array(
			"word" => $bet,
			"left" => 0,
			"right" => 0, 
			"status" => $valid ? 0 : 2,
			"counter" => $this->session->counter);

		if ($word && $valid) {
			if ($bet == $word) {
				$ret['status'] = 1;
			} else {
				$a = str_split($word);
				$b = str_split($bet);
				foreach ($b as $k => $v) {
					// search for current letter
					if (($p = array_search($v, $a)) !== false) {
						$ret["left"] += 1;
						$ret["right"] += $p == $k ? 1 : 0;
					}
				}
			}
		}
		$this->_helper->json($ret);
    }


}
