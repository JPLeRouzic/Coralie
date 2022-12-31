<!-- tags.html -->
<?php 
$tags = get_tags_info() ;
?>

<a href="<?php echo site_url();?>add/tag">Add tag</a>
<table class="tag-list">
    <tr class="head">
        <th>Name</th>
        <th>Description</th>
        <th>Count</th>
        <th>Operations</th>
    </tr>
    <?php foreach ($tags as $tag):?>
     <!-- loop for tags -->
    <tr>
        <!-- Name -->
        <td>
            <a href="<?php echo $tag->url; ?>" target="_blank">
            <?php echo $tag->value; ?></a>
        </td>
        <!-- Description -->
        <td>
            <?php echo $tag->description; ?></td>

        <!-- Count -->
        <td>
            <?php
            $total = get_tagcount($tag);
            echo $total
            ?>
        </td>
        <!-- Operations -->
        <td>
            <a href="<?php echo $tag->url; ?>/edit?destination=admin/tags">Edit</a> 
            <?php
            echo '<a href="' . $tag->url . '/delete?destination=admin/tags">Delete</a>';
            ?>
        </td>
    </tr>
    <?php endforeach;?>
</table>
