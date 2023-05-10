<?php

class PostsController extends AppController
{
   public $helpers = array('Html', 'Form');
   public $components = array('Session', 'RequestHandler');
   public function index()
   {
      $posts = $this->Post->find('all');
      $this->set('posts', $posts);
   }
   public function view($id = null)
   {
      $this->loadModel('Comment');
      $this->loadModel('User');
      $this->loadModel('PostCounter');
      $this->loadModel('LikeCounter');
      if (empty($this->request->data)) {
         if (!$id) {
            throw new NotFoundException(__('Invalid post'));
         }
         $post = $this->Post->findById($id);
         $post['Post']['counter']++;
         $this->Post->updateAll(
            ['counter' => $post['Post']['counter']],
            ['id' => $id]
         );
         $check_post = $this->PostCounter->find('all', [
            'recursive' => -1,
            'fields' => ['id', 'post_id', 'user_id'],
            'conditions' => ['post_id' => $id, 'user_id' => $this->Auth->user('id')]
         ]);
         if (empty($check_post)) {
            $this->PostCounter->create();
            $this->PostCounter->save(['post_id' => $id, 'user_id' => $this->Auth->user('id')]);
         }
         $post['Post']['user_id'] = $this->Auth->user('id');

         $comment = $this->Comment->find('all', [
            'recursive' => -1,
            'fields' => ['Comment.id', 'Comment.body', 'Comment.pic_path', 'Comment.created', 'Comment.user_id', 'User.username'],
            'conditions' => ['post_id' => $id],
            'joins' => [[
               'table' => 'users',
               'alias' => 'User',
               'type' => 'inner',
               'conditions' => ['Comment.user_id = User.id']
            ]]
         ]);
         $temp = $this->PostCounter->find('first', [
            'recursive' => -1,
            'fields' => ['count(PostCounter.post_id)'],
            'conditions' => ['PostCounter.post_id' => $id],
            'group' => 'post_id'
         ]);
         $post['Post']['unique_count'] = $temp[0]['count(`PostCounter`.`post_id`)'];
         if (!$post) {
            throw new NotFoundException(__('Invalid post'));
         }
         $check_like = $this->LikeCounter->find('first', [
            'recursive' => -1,
            'fields' => ['id', 'post_id', 'user_id'],
            'conditions' => ['post_id' => $id, 'user_id' =>  $this->Auth->user('id')]
         ]);
         if (empty($check_like)) {
            $post['Post']['like'] = 0;
         } else {
            $post['Post']['like'] = 1;
         }
         $tt = $this->User->findById($this->Auth->user('id'));
         $post['Post']['username'] = $tt['User']['username'];
         $this->set('comments', $comment);
         $this->set('post', $post);
      } else {
         $t = 0;
         if (!empty($this->request->data['like'])) {
            if ($this->request->data['like']['check'] == 0)
               $this->LikeCounter->save(['user_id' => $this->Auth->user('id'), 'post_id' => $this->request->data['like']['id']]);
            else {
               $like_id = $this->LikeCounter->find('first', [
                  'recursive' => -1,
                  'fields' => ['id'],
                  'conditions' => ['user_id' => $this->Auth->user('id'), 'post_id' => $this->request->data['like']['id']]
               ]);
               $this->LikeCounter->delete($like_id['LikeCounter']['id']);
            }

            $t = $this->request->data['like']['id'];
         }
         if (!empty($this->request->data['comment'])) {
            $qw = [
               'body' => $this->request->data['comment']['body'],
               'user_id' => $this->request->data['comment']['user_id'],
               'post_id' => $this->request->data['comment']['post_id']
            ];
            $t = $this->request->data['comment']['post_id'];
            if ($this->Comment->save($qw))
               $this->Flash->success(__('Your Comment has been saved.'));
         }
         unset($this->request->data);
         return $this->redirect(['action' => 'view', $t]);
      }
   }
   public function add()
   {

      $this->loadModel('Group');
      $this->loadModel('UserRole');
      if ($this->request->is('post')) {
         $file = $this->request->data['Post']['upload'];
         $allowedTypes = array('jpg', 'jpeg', 'png', 'gif');
         $allowedSize = 1024 * 1024; // 1 MB
         if (!in_array(pathinfo($file['name'], PATHINFO_EXTENSION), $allowedTypes)) {
            $this->Flash->error(__('Invalid file type.'));
         } elseif ($file['size'] > $allowedSize  || $file['size'] == 0) {
            $this->Flash->error(__('File size exceeds limit.'));
         } else {
            $temp = [
               'title' => $this->request->data['Post']['title'],
               'body' => $this->request->data['Post']['body'],
               'pic_path' => $file['name'],
               'like' => 0,
               'user_id' => $this->Auth->user('id'),
               'group_id' => $this->request->data['Post']['group_id']

            ];
            $this->Post->create();
            $this->Post->save($temp);
            move_uploaded_file($file['tmp_name'], WWW_ROOT . 'img/' . $file['name']);
            $this->Flash->success(__('File uploaded successfully.'));
            return $this->redirect(array('controller' => 'users', 'action' => 'index'));
         }
      } else {
         $this->loadModel('Group');
         $group_options = $this->Group->find('all', [
            'recursive' => -1,
            'fields' => ['id', 'name']
         ]);
         $id = $this->Session->read('User.id');
         $groups_id = $this->UserRole->find(
            'list',
            [
               'recursive' => -1,
               'fields' => ['group_id'],
               'conditions' => ['user_id' => $id]
            ]
         );
         $final = [];
         foreach ($group_options as $key => $value) {
            if (!empty(array_search($key, $groups_id))) {
               $final[$key] = $value;
            }
         }

         $this->set('group_info', $final);
      }
   }
   public function edit($id = null)
   {
      if (!$id) {
         throw new NotFoundException(__('Invalid post'));
      }
      $post = $this->Post->findById($id);
      if (!$post) {
         throw new NotFoundException(__('Invalid post'));
      }
      if ($this->request->is(array('post', 'put'))) {
         $this->Post->id = $id;
         if ($this->Post->save($this->request->data)) {
            $this->Flash->success(__('Your post has been updated.'));
            return $this->redirect(array('action' => 'index'));
         }
         $this->Flash->error(__('Unable to update your post.'));
      }
      if (!$this->request->data) {
         $this->request->data = $post;
      }
   }
   public function temp()
   {
      $data = json_decode(file_get_contents("php://input"), true);
      $this->autoRender = false;
      $this->loadModel('LikeCounter');
      $check_like = $this->LikeCounter->find('first', [
         'recursive' => -1,
         'fields' => ['id', 'post_id', 'user_id'],
         'conditions' => ['post_id' => $data, 'user_id' =>  $this->Auth->user('id')]
      ]);
      if (empty($check_like)) {
         $v = 1;
         $this->LikeCounter->save(['user_id' => $this->Auth->user('id'), 'post_id' => $data]);
      } else {
         $v = 0;
         $like_id = $this->LikeCounter->find('first', [
            'recursive' => -1,
            'fields' => ['id'],
            'conditions' => ['user_id' => $this->Auth->user('id'), 'post_id' => $data]
         ]);
         $this->LikeCounter->delete($like_id['LikeCounter']['id']);
      }
      echo json_encode($v);
   }
   public function temp2()
   {

      $this->autoRender = false;
      $this->loadModel('Comment');
      $data = $this->request->data;
      $temp = "";
      if (!empty($_FILES)) {
         $file = $_FILES;
         $allowedTypes = array('jpg', 'jpeg', 'png', 'gif');
         $allowedSize = 1024 * 1024; // 1 MB
         if (!in_array(pathinfo($file['photo']['name'], PATHINFO_EXTENSION), $allowedTypes)) {
            echo json_encode($temp);
            exit;
         } elseif ($file['photo']['size'] > $allowedSize || $file['photo']['size'] == 0) {
            echo json_encode($temp);
            exit;
         } else {
            $temp = [
               'body' => $data['comment'],
               'pic_path' => $file['photo']['name'],
               'user_id' => $this->Auth->user('id'),
               'post_id' => $data['post_id']
            ];
            move_uploaded_file($file['photo']['tmp_name'], WWW_ROOT . 'img/' . $file['photo']['name']);
         }
         
      } else {
         $temp = [
            'body' => $data['comment'],
            'user_id' => $this->Auth->user('id'),
            'post_id' => $data['post_id']
         ];
      }
         $this->Comment->create();
         $this->Comment->save($temp);
         echo json_encode($temp);
      
   }
   public function pic()
   {
      pr($_FILES);
   }
   // public function add()
   // {
   //    if ($this->request->is('post')) {
   //       $this->Product->create();
   //       if ($this->Product->save($this->request->data)) {
   //          $this->Session->setFlash(__('The product has been saved.'));
   //          return $this->redirect(array('action' => 'index'));
   //       } else {
   //          $this->Session->setFlash(__('The product could not be saved. Please, try again.'));
   //       }
   //       if (!empty($this->data)) {
   //          //Check if image has been uploaded
   //          if (!empty($this->data['products']['upload']['name'])) {
   //             $file = $this->data['products']['upload']; //put the data into a var for easy use

   //             $ext = substr(strtolower(strrchr($file['name'], '.')), 1); //get the extension
   //             $arr_ext = array('jpg', 'jpeg', 'gif'); //set allowed extensions

   //             //only process if the extension is valid
   //             if (in_array($ext, $arr_ext)) {
   //                //do the actual uploading of the file. First arg is the tmp name, second arg is
   //                //where we are putting it
   //                move_uploaded_file($file['tmp_name'], WWW_ROOT . 'CakePHP/app/webroot/img/' . $file['name']);

   //                //prepare the filename for database entry
   //                $this->data['products']['product_image'] = $file['name'];
   //             }
   //          }

   //          //now do the save
   //          $this->products->save($this->data);
   //       }
   //    }
   // }
}
