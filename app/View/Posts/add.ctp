<h1>Add Post</h1>
<?php
echo $this->Form->create('Post', array('type' => 'file'));
echo $this->Form->input('title');
echo $this->Form->input('body', array('rows' => '3'));
echo $this->Form->input('group_id',[
    'options' => [
        12 => 'Sports',
        13 => 'Politics' ,
        14 => 'Economics'
    ]
]);
echo $this->Form->input('upload', array('type'=>'file'));
echo $this->Form->end('Save Post');
?>