<?php
global $view_data;
 ?>
<section id="profile" class="single-centered">
   <form class="" id="frm-profile" action="/account/profile" method="post" enctype="multipart/form-data">
	   <?php if(!empty($view_data['error'])) {
		   echo "<h3><strong>" . $view_data['error'] . "</strong></h3>";
	   }?>
	   <table>
	   	<thead>
	   		<tr>
	 	   		<th colspan="2"><h3>Profile:</h3></th>
	   		</tr>
	   	</thead>
		<tr>
			<td><strong>Username:</strong></td>
		   <td><?php echo $view_data['username']; ?></td>
		</tr>
		<tr>
			<td><strong>Email: </strong></td>
			<td><input type="email" name="email" value="<?php echo $view_data['email']; ?>" placeholder="Email" required></td>
		</tr>
		<tr>
			<td><strong>Profile Picture</strong></td>
			<td>
				<div class="">
					<img id="profile-image" src="data:image/<?php echo $view_data['content_type'] . ';base64,' . base64_encode($view_data['image']); ?>" alt="">
				</div>
		 	   <div id="image-upload" style="display: none;">
		 		   <div class="">
		 			   Choose a profile image:
		 		   </div>
		 		   <input type="file" name="image" id="image" required>
		 	   </div>
				<button type="button" name="button" class="btn-alt" id="changeImage">Change Image</button></td>
		</tr>
		<tr>
			<td colspan="2">
		 	   <div class="text-center">
		 		   <input type="submit" name="submit" class="btn" value="Update Profile">
		 	   </div>
		   </td>
		</tr>
	   </table>
   </form>
</section>

<script type="text/javascript">
	window.addEventListener('load', function() {
		document.getElementById('changeImage').addEventListener('click', function() {
			document.getElementById('image').click();
		});
		document.getElementById('image').addEventListener('change', function() {
			if(this.files.length > 0) {
				var file = this.files[0];
				console.log(file);

				var reader = new FileReader();
				reader.addEventListener("load", function () {
					var img = new Image();
					img.src = reader.result;
					img.id = 'profile-image';
					var parent = document.getElementById('profile-image').parentNode;
					document.getElementById('profile-image').remove();
					parent.appendChild(img);
					// document.getElementById('image').remove();
					// console.log(reader);
				});
				reader.readAsDataURL(file);
			}
		})
	})
</script>
