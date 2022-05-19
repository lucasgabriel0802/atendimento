<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 *
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UsersController extends AppController
{

    public function index()
    {
        $users = $this->paginate($this->Users);

        $this->set(compact('users'));
    }

    public function view($codigo = null, $unidade = null)    
    {
        $query = $this->Users
        ->find()
        ->where(['codigo =' => $codigo, 'unidade = ' => $unidade]);
        foreach ($query as $usuario) {
            debug($usuario->usuarioweb);
            $user = $usuario;
        }   
        $this->set('user', $user);
    }

    public function add()
    {
        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('Usuário cadastrado com sucesso'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Erro: Usuário não foi cadastrado com sucesso'));
        }
        $this->set(compact('user'));
    }

    public function edit($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('Usuário editado com sucesso'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Erro: Usuário não foi editado com sucesso'));
        }
        $this->set(compact('user'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('Usuário apagado com sucesso'));
        } else {
            $this->Flash->error(__('Erro: Usuário não foi apagado com sucesso'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function login($username = null, $password = null)
    {
        if($this->request->is('post')){
            echo $username;
            echo $password;
            die;
            
            $query = $this->Users
            ->find()
            ->where(['email =' => $username, 'senhaweb = ' => $password]);
            foreach ($query as $usuario) {
                debug($usuario->usuarioweb);
                $user = $usuario;
            }   
            if($user){
                $this->Auth->setUser($user);
                return $this->redirect($this->Auth->redirectUrl());
            }
       }
    }

    public function logout()
    {
        return $this->redirect($this->Auth->logout());
    }
}
