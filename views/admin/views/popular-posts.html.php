<!-- popular-posts.html -->
<h2 class="post-index"><?php echo $heading ?></h2>
<?php if (!empty($posts)) { ?>
    <table class="post-list">
        <tr class="head">
            <th>Title</th>
            <th>Published</th><?php if (config("views.counter") == "true"): ?>
                <th>Views</th><?php endif; ?>
            <th>Per day</th>
            <th>Author</th>
            <th>Tag</th>
            <th>Operations</th>
        </tr>
        <?php $i = 0;
        $len = count($posts); ?>
        <?php foreach ($posts as $p): ?>
            <?php
            if ($i == 0) {
                $class = 'item first';
            } elseif ($i == $len - 1) {
                $class = 'item last';
            } else {
                $class = 'item';
            }
            $i++;
            ?>
			<?php 
			$d1 = strtotime(date('d F Y', $p->date));
			$difference = time() - $d1 ;
			$days = floor($difference / 86400) ;
            if ($days < 1) 
                { $days = 1 ; } 
			$ratio = round($p->views/$days, 3) ;
			?>
            <tr class="<?php echo $class ?>">
                <!-- Title of published post -->
                <td><a target="_blank" href="<?php echo $p->url ?>"><?php echo $p->title->value ?></a></td>
                <!-- Date it was published -->
                <td><?php echo date('d F Y', $p->date) ?></td>
                <!-- Number of views since it was published -->
                <td><?php echo $p->views ?></td>
                <!-- Number of views per day -->
                <td><?php echo $ratio ?></td>
                <!-- Author of post -->
                <td><a target="_blank" href="<?php echo $p->author->url ?>"><?php echo $p->author->name ?></a></td>
                <!-- Tags of post -->
                <td><?php echo $p->tag->value ?></td>
                <!-- Operations on post -->
                <td><a href="<?php echo $p->url ?>/edit?destination=admin/posts">Edit</a> <a
                        href="<?php echo $p->url ?>/delete?destination=admin/posts">Delete</a></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php } else {
    echo 'No posts found!';
} ?>
