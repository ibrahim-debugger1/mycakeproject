<?php

class GroupsController extends AppController{
   
    public function index()
    {
        $this->set('group_info',$this->Group->get_data());
    }
}