
<h1>Users</h1>
<table>
    <tr>
        <th>Family name</th>
        <th>Group</th>
        <th>Role</th>
        <th>Action</th>
    </tr>
    <?php foreach ($users_info as $user) : ?>
        <tr>
            <td><?php echo $user['User']['full name']; ?></td>
            <td><?php echo $user['Group']['name']; ?></td>
            <td><?php echo $user['Role']['title']; ?></td>
            <td><?php echo $this->Html->link('view',['controller' => 'users' , 'action' => 'view' ,$user['User']['id'] ]) . ' '. $this->Html->link('Edit',['controller' => 'users' , 'action' => 'edit' ,$user['User']['id'] ]); ?></td>
        </tr>
    <?php endforeach; ?>
</table>