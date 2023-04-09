<?php

class User extends AppModel
{
    
    public function get_info()
    {
        $temp = $this->find('list', [
            'recursive' => -1,
            'fields' => ['username', 'password']
        ]);
    }
 
    public function test()
    {
        $passwordHasher = new BlowfishPasswordHasher();
        echo ($passwordHasher->check('12345', '$2a$10$eAJ9ZeLIlR7Kt51ry4EFku/MaeCZiNlP8sTBTy77eJkpr0I//kGNa'));
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
