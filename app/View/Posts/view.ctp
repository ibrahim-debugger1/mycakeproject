<h1><?php echo h($post['Post']['title']); ?></h1>
<p><small>total count: <?php echo $post['Post']['counter']; ?></small></p>
<p><small>unique count: <?php echo $post['Post']['unique_count']; ?></small></p>
<p><?php echo h($post['Post']['body']); ?></p>
<?php if (!empty($post['Post']['pic_path'])) echo $this->Html->image($post['Post']['pic_path'], array('alt' => 'CakePHP')); ?>
<?php echo '<br>';
foreach ($comments as $c) :
    echo $c['Comment']['body'] . '<br><br>';
    echo "Posted by: " . $c['User']['username'] . ' on ' . $c['Comment']['created'] . '<br><br>';
    if (!empty($c['Comment']['pic_path'])) echo $this->Html->image($c['Comment']['pic_path'], array('alt' => 'Description of the image')) . '<br><br>';
    echo '##############################################################################<br><br>';
endforeach; ?>
<p id="comment"></p>
<div id="image-container"></div>
<img src="">

<!-- Existing comments will be displayed here -->
</div>
<p id="likedornot"><?php echo $post['Post']['like'] ? "liked"  : "like"; ?></p>
<button onclick="getRepos()">Like</button>
<form enctype="multipart/form-data" id="myForm">
    <textarea name="comment" id="comment"></textarea><br><br>
    <input type="file" name="photo" id="photo"><br><br>
    <button type="button" onclick="getRepos2()">Upload</button>
</form>
<p id="text"></p>
<script>
    function getRepos() {
        var myrequest = new XMLHttpRequest();
        var data = "<?php echo $post['Post']['id']; ?>";
        myrequest.onreadystatechange = function() {
            if (this.readyState === 4 && this.status === 200) {
                console.log(this.responseText);
                //var tt = json_decode(file_get_contents("php://input"), true);
                //console.log(tt);
                if (this.responseText === "1")
                    document.getElementById("likedornot").innerHTML = "liked";
                else
                    document.getElementById("likedornot").innerHTML = "like";
            }
        }
        myrequest.open("POST", "/posts/temp/", true);
        myrequest.send(JSON.stringify(data));

    }

    function getRepos2() {
        var form = document.getElementById("myForm");

        var formData = new FormData(form);
        formData.append('post_id', <?php echo $post['Post']['id']; ?>)
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                var response = JSON.parse(xhr.responseText);
                document.getElementById("comment").innerHTML = response.body;
                var te = response.pic_path;
                let mydiv = document.getElementById('image-container');
                let myImage = document.createElement('img');
                myImage.src=`/img/${te}`;
                mydiv.append(myImage);
            }
        };
        xhr.open("POST", "/posts/temp2", true);
        xhr.send(formData);

    }
</script>