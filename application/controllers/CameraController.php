<?php

class CameraController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $bootstrap = $this->getInvokeArg('bootstrap');
        $config = $bootstrap->getOptions();

        // action body
        header("Content-type:image/jpeg");
        $img = file_get_contents($config['camera']['url']);
        if($img != ""){
            header("Content-type:image/jpeg");
            echo $img;
        }
        else{
            header('HTTP/1.0 404 Not Found');
        }
    }


}

