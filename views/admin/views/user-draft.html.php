<!-- user-draft.html -->
<h2 class="post-index"><?php echo $heading ?></h2>
<?php if (!empty($posts)) { ?>
    <table class="post-list">
        <tr class="head">
            <th>Title</th>
            <th>Created</th>
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
                <td><?php echo $p->title->value ?></td>
                <td><?php echo date('d F Y', $p->date) ?></td>
                <td><?php echo strip_tags($p->tag->value) ?></td>
                <td><a href="<?php echo $p->url ?>/edit?destination=admin/draft">Edit</a> <a href="<?php echo $p->url ?>/delete?destination=admin/draft">Delete</a></td>
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
    echo 'No draft found!';
} ?>