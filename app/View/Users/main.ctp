<table>
    <tr>
        <th>title</th>
        <th>body</th>
    </tr>
    <?php foreach ($temp as $k => $v) {
        foreach($v as $key => $value){ ?>
        <tr>
            <td><?php echo $key ?></td>
            <td><?php echo $value ?></td>
        </tr>
    <?php }} ?>
</table>