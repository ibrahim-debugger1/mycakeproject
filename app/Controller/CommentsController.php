<?php

class CommentsController extends AppController
{

    public function index()
    {
    }
    public function add()
    {
        $this->autoRender = false;
        $response = array();
        if ($this->request->is('ajax')) {
            pr($this->request->data);die;
            $this->Comment->create();
            $this->request->data['Comment']['user_id'] = $this->Auth->user('id');
            if ($this->Comment->save($this->request->data)) {
                $file = $this->request->data['Comment']['file'];
                if (!empty($file['name'])) {
                    $filename = $file['name'];
                    $tmpname = $file['tmp_name'];
                    $uploadPath = WWW_ROOT . 'img' . DS . $filename;
                    if (move_uploaded_file($tmpname, $uploadPath)) {
                        $commentId = $this->Comment->getLastInsertID();
                        $this->Comment->saveField('file', $filename, array('id' => $commentId));
                    }
                }
                $comment = $this->Comment->findById($this->Comment->getLastInsertID());
                $response['success'] = true;
                $response['comment'] = $comment['Comment'];
            } else {
                $response['success'] = false;
            }
            echo json_encode($response);
        }
    }
}
