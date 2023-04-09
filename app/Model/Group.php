<?php 

class Group extends AppModel{
 
    public function get_data(){
        $user_model=ClassRegistry::init('User');
        
        $group_info=$this->find('all',[
            'recursive' =>-1,
            'fields' => ['Group.id','Group.name','count(Group.id) as counter'],
            'group' => 'Group.id',
            'joins' => [[
                'table' => 'users',
                'alias' => 'User',
                'type' => 'inner',
                'conditions' => ['User.group_id = Group.id']
            ]]
        ]);
        return $group_info;
    }
}