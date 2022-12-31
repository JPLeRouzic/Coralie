<!-- categories.html -->
<a href="<?php echo site_url(); ?>add/category">Add category</a>
<?php $desc = get_categories_info(); ?>
<table class="category-list">
    <tr class="head">
        <th>Name</th>
        <th>Description</th>
        <th>Count</th>
        <th>Operations</th>
    </tr>
    <tr> <!-- hard coded category -->
        <td><a href="<?php echo site_url(); ?>category/uncategorized" target="_blank">Uncategorized</a></td>
        <td><p>Topics that don't need a category, or don't fit into any other existing category.</p></td>
        <td><?php 
                $total = get_draftcount('uncategorized') + get_categorycount('uncategorized');
                echo $total
        ?></td>
        <td></td>
    </tr>
     <!-- loop for categories -->
    <?php foreach ($desc as $catgry):?>
    <tr>
        <!-- Name -->
        <td>
            <a href="<?php echo $catgry->url; ?>" target="_blank">
            <?php echo $catgry->title; ?></a>
        </td>
        <!-- Description -->
        <td>
            <?php echo $catgry->description; ?></td>

        <!-- Contents -->
        <td>
            <?php
            $total = get_draftcount($catgry->title) + get_categorycount($catgry->title);
            echo $total
            ?>
        </td>
        <!-- Operations -->
        <td>
            <a href="<?php echo $catgry->url; ?>/edit?destination=admin/categories">Edit</a> 
            <?php
            echo '<a href="' . $catgry->url . '/delete?destination=admin/categories">Delete</a>';
            ?>
        </td>
    </tr>
    <?php endforeach;?>
</table>