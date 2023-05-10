<h1>Groups</h1>
<table>
    <tr>
        <th>ID</th>
        <th>Group Name</th>
    </tr>
    <?php foreach ($groups_info as $key => $value ) : ?>
        <tr>
            <td><?php echo $key ?></td>
            <td><?php echo $value ?></td>
        </tr>
    <?php endforeach; ?>
</table>
<h1>Users</h1>
<table>
    <tr>
        <th>First name</th>
        <th>Family name</th>
        <th>Group</th>
        <th>Role</th>
    </tr>
    <?php foreach ($users_info as $user) : ?>
        <tr>
            <td><?php echo $user['User']['first_name']; ?></td>
            <td><?php echo $user['User']['family_name']; ?></td>
            <td><?php echo $user['Group']['name']; ?></td>
            <td><?php echo $user['Role']['title']; ?></td>
        </tr>
    <?php endforeach; ?>
</table>