<?php
// (c)Perez Karjee(www.aas9.in)
// Project Site www.aas9.in/zerocms
// Created March 2014
require '../includes/db.kate.php';
require '../includes/functions.kate.php';
include '../includes/header.kate.php';

$dbx = mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD)
	or die('Fuck!, Unable To Connect.');

mysqli_select_db(MYSQL_DB, $dbx) or die(mysqli_error($dbx));

$article_id = (isset($_GET['article_id']) && ctype_digit($_GET['article_id'])) ? $_GET['article_id'] : '';
output_story($dbx, $article_id);
?>
<h3>Add A Comment</h3>
<form method="post" action="zero_transact_article.php">
	<div>
		<label for="comment_text">Comment: </label><br/>
		<textarea id="comment_text" name="comment_text" rows="10" cols="60"></textarea><br/>
		<input type="submit" name="action" value="Submit Comment" />
		<input type="hidden" name="article_id" value="<?php echo $article_id; ?>" />
	</div>
</form>
<?php
show_comments($dbx, $article_id, FALSE);
include '../includes/footer.kate.php';
?>
