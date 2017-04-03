
<?php
	global $user;

	Html::render_view('sidebar');
 ?>

<section id="edit_thread" class="manage_content">
	<form class="" action="<?php global $sub_path; echo $sub_path; ?>/content/edit_thread/<?php echo $view_data['thread_id']; ?>" method="post">
		<div class="entry">
			<h2>Edit Thread</h2>
			<?php if($view_data != null && !empty($view_data['error'])) {
				echo "<h3><strong>" . $view_data['error'] . "</strong></h3>";
			}?>
			<input type="text" name="thread_title" id="thread_title" value="<?php echo $view_data['thread_name']; ?>" placeholder="Thread Title" required>
			<textarea name="thread_body" id="thread_body" placeholder="Thread Body" required><?php echo $view_data['thread_body']; ?></textarea>
		</div>

		<section id="preview" style="display: none;">
			<section id="thread">
				<h3>Preview</h3>
				<section class="thread-content">
					<h3>
						<span id='preview-title'></span>
						<div class="author"><?php echo Html::special_chars($user['username']); ?></div>
					</h3>
					<p id="preview-body"></p>
				</section>
			</section>
		</section>
		<div class="text-right">
			<button type="submit" class="btn" id="btnSubmitThread">Edit Post</a>
		</div>
	</form>
</section>

<script type="text/javascript">
	String.prototype.encode = function(){return this.replace(/[^]/g,function(e){return"&#"+e.charCodeAt(0)+";"})}
	var old_title = null;
	var old_body = null;
	function UpdatePreview() {
		document.getElementById('preview').style.display = 'block';
		document.getElementById('preview-title').innerHTML = document.getElementById('thread_title').value.encode();
		document.getElementById('preview-body').innerHTML = document.getElementById('thread_body').value.encode();
		old_title = this.value;
		old_body = this.value;
	}
	window.addEventListener('load', function() {
		UpdatePreview()
		document.getElementById('thread_title').addEventListener('keyup', function(e) {
			if(this.value != old_title) {
				UpdatePreview();
			}
		})
		document.getElementById('thread_body').addEventListener('keyup', function() {
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
			href: "/content/edit_thread/<?php echo $view_data['thread_id']; ?>",
			text: "Edit Thread"
		}
	]
	$(document).ready(Breadcrumbs(crumbs))
</script>
