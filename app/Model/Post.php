<?php
class Post extends AppModel
{
    public function get_groups_info()
    {
        $temp=ClassRegistry::init('User');
        $temp->virtualFields['full_name'] = 'concat(User.first_name," ",User.last_name)';

        $groups_info = $temp->find('all', [
            'recursive' => -1,
            'fields' => ['User.full_name', 'Group.name', 'Role.title'],
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
                'conditions' => ['User.role_id=Role.id']
            ]]
        ]);
        return $groups_info;
    }
}
