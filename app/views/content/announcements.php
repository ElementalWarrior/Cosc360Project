<?php
global $user;
global $sub_path;
?>

<?php if(!empty($user['admin']) && $user['admin']) { ?>
<article id="submit_announcement">
	<form class="" action="<?php echo $sub_path;?>/content/submit_announcement" method="post">
		<h2>Submit a new announcement</h2>
		<input type="text" name="announcement_title" name="announcement_body" value="" required aria-required="true" placeholder="Announcement Title" />
		<textarea name="announcement_body" required aria-required="true" placeholder="Announcement Body"></textarea>
		<input class="btn" type="submit" name="submit" value="Post Announcement">
	</form>
</article>
<?php } ?>

<?php foreach($view_data as $row) {
$announcement_id = $row['announcement_id'];
$account_id = $row['account_id'];
$username = $row['username'];
	?>
	<article class="announcement">
		<h2><?php echo $row['announcement_title']; ?></h2>
		<span>Author: </span><a class="a-alt" href="<?php echo "$sub_path/account/profile/$account_id"; ?>"><?php echo $username; ?></a>
		<br><br>
		<p><?php echo $row['announcement_body']; ?></p>
		<a class="a-alt" href="<?php echo "$sub_path/content/activity_by_date/"; ?><?php echo (new DateTime($row['date_created']))->format('Y-m-d');?>" aria-label="View activity for <?php echo (new DateTime($row['date_created']))->format('l F d, Y'); ?>"><?php echo $row['date_created'];?></a>
		<?php if(!empty($user['admin']) && $user['admin']) { ?>
			<a href="<?php echo "$sub_path/content/remove_announcement/$announcement_id"; ?>" class="btnRemoveAnnouncement btn btn-small btn-alt">Remove</a>
			<?php } ?>
	</article>
<?php } ?>
