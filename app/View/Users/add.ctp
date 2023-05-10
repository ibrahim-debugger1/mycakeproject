<div class="form users">
    <?php echo $this->Form->create('User'); ?>
    <fieldset>
        <legend>
            <?php echo __('Add user') ?>
        </legend>
        <?php 
            echo $this->Form->input('username');
            echo $this->Form->input('password');
            echo $this->Form->input('first_name');
            echo $this->Form->input('family_name');
            echo $this->Form->input('role_id',[
                'options' => [
                    1 => 'Manager',
                    2 => 'Editor' ,
                    3 => 'Auther',
                    4 => 'Admin',
                    5 => 'Super'
                ]
            ]);
            echo $this->Form->input('group_id',[
                'options' => [
                    12 => 'Sports',
                    13 => 'Politics' ,
                    14 => 'Economics'
                ]
            ]);
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Submit')); ?>
</div>