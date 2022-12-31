<!-- posts-list.html -->
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
            <tr class="<?php echo $class ?>">
                <td><a target="_blank" href="<?php echo $p->url ?>"><?php echo $p->title->value ?></a></td>
                <td><?php echo date('d F Y', $p->date) ?></td>
                <?php if (config("views.counter") == "true"): ?>
                	<td><?php echo $p->views ?></td>

			<?php 
			$d3 = strtotime(date('d F Y', $p->date)) ;
			$difference = time() - $d3 ;
			$days = floor($difference / 86400) ;
			if($days == 0) {
				$days = 1 ;
				}
			$ratio = round($p->views / $days, 3) ;
			?>
                	<td><?php echo $ratio ; ?></td>
		<?php endif; ?>


                <td><a target="_blank" href="<?php echo $p->author->url ?>"><?php echo $p->author->name ?></a></td>
                <td><?php echo $p->tag->value ?></td>
                <td><a href="<?php echo $p->url ?>/edit?destination=admin/posts">Edit</a> <a
                        href="<?php echo $p->url ?>/delete?destination=admin/posts">Delete</a></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <?php if (!empty($pagination['prev']) || !empty($pagination['next'])): ?>
        <div class="pager">
            <?php if (!empty($pagination['prev'])): ?>
                <span><a href="?page=<?php echo $page - 1 ?>" class="pagination-arrow newer" rel="prev">Newer</a></span>
            <?php endif; ?>
            <?php if (!empty($pagination['next'])): ?>
                <span><a href="?page=<?php echo $page + 1 ?>" class="pagination-arrow older" rel="next">Older</a></span>
            <?php endif; ?>
        </div>
    <?php endif; ?>
<?php } else {
    echo 'No posts found!';
} ?>
