<?php

class UsersController extends AppController
{

  public function index()
  {
  }
  public function add()
  {
    $this->loadModel('Group');
    $this->loadModel('Role');
    $this->loadModel('UserRole');
    if ($this->request->is('post')) {
      // $this->User->create();
      //pr($this->request->data);die;
      $temp = [
        'username' => $this->request->data['User']['username'],
        'password' => $this->request->data['User']['password'],
        'email' => $this->request->data['User']['email']
      ];
      //pr($this->request->data);die;
      if ($this->User->save($temp)) {
        $id = $this->User->find('first', ['recursive' => -1, 'fields' => ['id'], 'conditions' => ['email' => $this->request->data['User']['email']]]);
        foreach ($this->request->data['User']['group_options'] as $lo) :
          $temp2 = ['user_id' => $id['User']['id'], 'group_id' => $lo, 'role_id' => 1];
          $this->UserRole->create();
          $this->UserRole->save($temp2);
        endforeach;
        $this->Flash->success(__('The user has been saved'));
        $this->Session->write('User.id', $id['User']['id']);
        return $this->redirect(['action' => 'main']);
      }
      $this->Flash->error(__('sorry something went wrong'));
    } else {
      $group_options = $this->Group->find('list', [
        'recursive' => -1,
        'fields' => ['Group.id', 'Group.name']
      ]);
      $role_options = $this->Role->find('list', [
        'recursive' => -1,
        'fields' => ['Role.id', 'Role.title']
      ]);
      $this->set(compact('group_options', 'role_options'));
    }
  }
  public function main()
  {
    $this->loadModel('Post');
    $this->loadModel('UserRole');
    $id = $this->Session->read('User.id');
    $groups_id = $this->UserRole->find('all',[
      'recursive' => -1,
      'fields' => ['group_id'],
      'conditions' => ['user_id' => $id]
      ]
    );
    $temp=[];
    foreach($groups_id as $gi):
      $posts=$this->Post->find('list',[
        'recursive' => -1,
        'fields' => ['title','body'],
        'conditions' => ['group_id' => $gi['UserRole']['group_id']]
      ]
    );
    if(!empty($posts))
      array_push($temp,$posts);
    endforeach ;
    $this->set('temp',$temp);
  }

  public function logout()
  {
    return $this->redirect($this->Auth->logout());
  }
  public function login()
  {
    if ($this->request->is('post')) {
      if ($this->Auth->login()) {
        return $this->redirect($this->Auth->redirectUrl());
      }
      $this->Auth->Flash(__('sorry something went wrong'));
    }
  }
}
