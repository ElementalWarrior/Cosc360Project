
<?php
	global $user;
	global $view_data;
	Html::render_view('sidebar');
 ?>

<section id="new_thread">
	<form class="" action="/content/new_thread" method="post">
		<div class="entry">
			<?php if($view_data != null && !empty($view_data['error'])) {
				echo "<h2><strong>" . $view_data['error'] . "</strong></h2>";
			}?>
			<input type="text" name="thread_title" id="thread_title" value="" placeholder="Thread Title" required>
			<textarea name="thread_body" id="thread_body" placeholder="Thread Body" required></textarea>
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
			<button type="submit" class="btn" id="btnSubmitThread">Submit Thread</a>
		</div>
	</form>
</section>

<script type="text/javascript">
	String.prototype.encode = function(){return this.replace(/[^]/g,function(e){return"&#"+e.charCodeAt(0)+";"})}
	var old_title = null;
	var old_body = null;
	window.addEventListener('load', function() {
		document.getElementById('thread_title').addEventListener('keyup', function(e) {
			if(this.value != old_title) {
				document.getElementById('preview').style.display = 'block';
				document.getElementById('preview-title').innerHTML = this.value.encode();
				old_title = this.value;
			}
		})
		document.getElementById('thread_body').addEventListener('keyup', function() {
			if(this.value != old_body) {
				document.getElementById('preview').style.display = 'block';
				document.getElementById('preview-body').innerHTML = this.value.encode();
				old_body = this.value;
			}
		})
	})
</script>
