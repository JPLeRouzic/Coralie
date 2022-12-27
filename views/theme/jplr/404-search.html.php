<!-- 404-search.html -->
<?php

/*
 * Object sent to render()
 * 
 * array(7) { 
    ["title"]=> string(53) "Search results not found! - Padirac Innovations' blog" 
    ["description"]=> string(25) "Search results not found!" 
    ["search"]=> object(Search)
            #74 (1) { 
                ["title"]=> string(6) "cancer" 
                } 
    ["breadcrumb"]=> string(50) "Home » No search results" 
    ["canonical"]=> string(6) "/News/" 
    ["bodyclass"]=> string(16) "error-404-search" 
    ["is_404search"]=> bool(true) 
    }
*/


if (!empty($breadcrumb))
    :
    ?>
    <div class="breadcrumb"><?php
        echo $breadcrumb
        ?></div>
    <?php
endif;
?>
<section class="inpage post section">
    <div class="section-inner">
        <div class="content">
            <div class="item">
                <h1 class="title">Search results not found!</h1>
                <p>Please search again, or would you like to try our <a href="<?php
echo site_url()
?>">homepage</a> instead?</p>
                <div class="search-404">
                    <form id="search" class="navbar-form search" role="search">
                        <div class="input-group">
                            <input type="search" name="search" class="form-control" placeholder="Type to search">
                            <span class="input-group-btn"><button type="submit" class="btn btn-default btn-submit"><i class="fa fa-angle-right"></i></button></span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<?php 
	include('model/search/PubMedAPI.php');
	$PubMedAPI = new PubMedAPI();
	if (isset($_GET['page'])) {
		$PubMedAPI->retstart = $PubMedAPI->retmax*((int)$_GET['page'] - 1)+1;
	}
	$results = $PubMedAPI->query($search->title, false); 

?>

<?php if (!empty($results)): ?>
	<p><b>Pubmed search</b> results for <strong><?php echo urldecode($PubMedAPI->term); ?></strong> (<?php echo $PubMedAPI->count; ?> results, showing max 5)</p>
	<table border="0" cellspacing="0" cellpadding="0">
		<tr>
			<th>Title</th>
			<th>Authors</th>
			<th>Journal</th>
			<th>Year</th>
		</tr>
		<?php foreach ($results as $result): ?>
		<tr>
			<td><a href="https://www.ncbi.nlm.nih.gov/pubmed/<?php echo $result['pmid']; ?>" target="_blank"><?php echo $result['title']; ?></a></td>
			<td><?php echo implode(", ",$result['authors']); ?></td>
			<td><?php echo $result['journalabbrev']; ?></td>
			<td><?php echo $result['year']; ?></td>
		</tr>
		<?php endforeach; ?>
	</table>
<?php endif; ?>

