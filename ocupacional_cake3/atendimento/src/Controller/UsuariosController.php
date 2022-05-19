<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;


class UsuariosController extends AppController{

    public function index(){

        // Faz automaticamente o load do Funcoes por causa do nome
        // $this->loadModel('Funcoes');
        $listaUsuario = '';
        $query = $this->Usuarios->find();
        
        //$query = $this->Usuarios->find();
        foreach ($query as $usuario) {
            debug('Código: '.$usuario->codigo. '  Nome: '.$usuario->usuarioweb );
        }        
        
        $listaUsuario = $this->Usuarios->find();
        #$listaFuncao = $this->Funcoes->buscarTudo();
        $this->set(['listaFuncao' => $listaUsuario]);
    }

    public function login(){
        if($this->request->is('post')){
            $user = $this->Auth->identify();
            if($user){
                $this->Auth->setUser($user);
                return $this->redirect($this->Auth->redirectUrl());
            }
       }        
    }
}
?>