<?php 
//Todos los elementos de nuestro controllers heredaran de BaseController
namespace app\controllers;


class BaseController{

    protected $templateEngine;

    public function __construct(){
        //Es la base de donde cargaremos nuestros archivos
        $loader = new \Twig_Loader_Filesystem('../views');
        $this->templateEngine = new \Twig_Environment($loader, [
            'debug' => true,
            'cache' => false
        ]);
    }

    //Todas las clases rederen con este metodo.
    public function render($fileName, $data = []){
        return $this->templateEngine->render($fileName, $data);
    }
}

?>