<?php

class SceneController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function showAction()
    {
        $sceneId = $this->_getParam('scene_id');
        $this->view->sceneId = $sceneId;
    }

    public function getAction(){
        $sceneId = $this->_getParam('scene_id');

        $this->_helper->json(Application_Model_SceneMapper::getInstance()->getScene($sceneId));
    }


}



