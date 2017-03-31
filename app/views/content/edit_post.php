
<?php
	global $user;
	global $view_data;
	Html::render_view('sidebar');
 ?>

<section id="edit_post" class="manage_content">
	<form class="" action="<?php global $sub_path; echo $sub_path; ?>/content/edit_post/<?php echo $view_data['thread_id']; ?>/<?php echo $view_data['post_id']; ?>" method="post">
		<div class="entry">
			<?php if($view_data != null && !empty($view_data['error'])) {
				echo "<h2><strong>" . $view_data['error'] . "</strong></h2>";
			}?>
			<textarea name="post_body" id="post_body" placeholder="Post Body" required><?php echo $view_data['post_body']; ?></textarea>
		</div>

		<section id="preview" style="display: none;">
			<section id="post">
				<h3>Preview</h3>
				<div class="post">
					<div class="response-by">Response By:</div>
					<a href="<?php global $sub_path; echo $sub_path; ?>/account/profile/<?php echo $view_data['account_id']; ?>" class="author"><?php echo $view_data['username']; ?></a>
					<p id="preview-body"></p>
				</div>
			</section>
		</section>
		<div class="text-right">
			<button type="submit" class="btn" id="btnSubmitPost">Edit Post</a>
		</div>
	</form>
</section>

<script type="text/javascript">
	String.prototype.encode = function(){return this.replace(/[^]/g,function(e){return"&#"+e.charCodeAt(0)+";"})}
	var old_body = null;
	function UpdatePreview() {
		document.getElementById('preview').style.display = 'block';
		document.getElementById('preview-body').innerHTML = document.getElementById('post_body').value.encode();
		old_body = this.value;
	}
	window.addEventListener('load', function() {
		UpdatePreview()
		document.getElementById('post_body').addEventListener('keyup', function() {
			if(this.value != old_body) {
				UpdatePreview();
			}
		})
	})
</script>

<script type="text/javascript">
	var crumbs = [
		{
			href: "/",
			text: "Home"
		},
		{
			href: "/content/thread/<?php echo $view_data['thread_id']; ?>",
			text: "<?php echo Html::special_chars($view_data['thread_name']); ?>"
		},
		{
			href: "/content/edit_post/<?php echo $view_data['thread_id']; ?>/<?php echo $view_data['post_id']; ?>",
			text: "Edit Post"
		}
	]
	$(document).ready(Breadcrumbs(crumbs))
</script>
