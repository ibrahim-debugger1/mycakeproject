<table>
    <tr>
        <th>group id</th>
        <th>group name</th>
        <th>counter</th>
    </tr>
    <?php foreach ($group_info as $group) : ?>
        <tr>
            <td><?php echo $group['Group']['id'];?></td>
            <td><?php echo $group['Group']['name'];?></td>
            <td><?php echo $group['0']['counter'];?></td>
        </tr>
    <?php endforeach; ?>
</table>