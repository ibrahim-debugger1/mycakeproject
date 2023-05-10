<?php
App::uses('BlowfishPasswordHasher','Controller/Component/Auth');
class User extends AppModel
{
    public function get_not_deleted_group_info($deleted = 0){
        if(isset($deleted))
            $conditions = ['Group.deleted' => $deleted];
        else
            $condtions = ['Group.deleted' => 0];
        $group_model=ClassRegistry::init('Group');
        $group_model->virtualFields['group_count'] = 'count(Group.id)';
            $group_info = $group_model->find('all',[
                'recursive' => -1,
                'fields' => ['Group.id','Group.name','count(Group.id) as Group__group_count'],
                'group' => 'Group.id',
                'conditions' => $conditions,
                'joins' => [[
                    'table' => 'users',
                    'alias' => 'User',
                    'type' => 'inner',
                    'conditions' => ['User.group_id = Group.id']
                ]]
            ]);
            return $group_info;
       }
    public function get_user_info()
    {
        $this->virtualFields['full name']= 'concat(User.first_name," ",User.family_name)';
        $user_info = $this->find('all', [
            'recursive' => -1,
            'fields' => ['full name', 'Group.name', 'Role.title','User.id'],
            'conditions' => ['User.deleted' => 0],
            'joins' => [[
                'table' => 'groups',
                'alias' => 'Group',
                'type' => 'inner',
                'conditions' => ['User.group_id = Group.id']
            ], [
                'table' => 'roles',
                'alias' => 'Role',
                'type' => 'inner',
                'conditions' => ['User.role_id = Role.id']
            ]]
        ]);
        return $user_info;
    }
    public function get_info()
    {
        $temp = $this->find('list', [
            'recursive' => -1,
            'fields' => ['username', 'password']
        ]);
    }

    public function beforeSave($options = []){
        if(isset($this->data['User']['password'])){
            $passwordHasher = new BlowfishPasswordHasher();
            $this->data['User']['password'] = $passwordHasher->hash($this->data['User']['password']);
        }
        return true;
    }
    
    public $validate = [
        'username' => [
            'require' => [
                'rule' => 'notBlank',
                'message' => 'please enter a username'
            ]
        ],
        'password' => [
            'require' => [
                'rule' => 'notBlank',
                'message' => 'please enter a password'
            ]
        ]
    ];
}
