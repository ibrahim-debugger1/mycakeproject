<div class="form users">

    <?php echo $this->Form->create('User'); ?>
    <fieldset>
        <legend>
            <?php echo __('Add user');  ?>
        </legend>
        <?php
        echo $this->Form->input('username');
        echo $this->Form->input('password');
        echo $this->Form->input('password', array('label' => 'Re-enter password'));
        echo $this->Form->input('email');
        echo $this->Form->input('role_id', ['options' => $role_options]);
        echo $this->Form->input('group_options',array('multiple' => 'checkbox', 'options' => $group_options));?>

    </fieldset>
    <?php echo $this->Form->end(__('Sign Up')); ?>
</div>