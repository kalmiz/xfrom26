<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function wordsAction()
    {
        $wordMapper = new Xfrom26_Model_WordMapper();
		$this->view->words = $wordMapper->fetchAll();
    }


}



