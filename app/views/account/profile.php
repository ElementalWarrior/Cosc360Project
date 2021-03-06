<?php

global $user;
global $sub_path;
global $page_title;
$page_title = "Profile for " . $view_data['username'];
$allow_submit = is_array($user) && ($user['account_id'] == $view_data['account_id'] || $user['admin']);
 ?>
<section id="profile" class="single-centered">
   <form class="" id="frm-profile" action="<?php global $sub_path; echo $sub_path; ?>/account/profile/<?php echo $view_data['account_id']; ?>" method="post" enctype="multipart/form-data">
		<h2>Profile:</h2>
		<?php if(!empty($view_data['error'])) {
			echo "<h3 role=\"alert\"><strong>" . $view_data['error'] . "</strong></h3>";
		}?>
		<a href="<?php echo $sub_path;?>/content/activity_by_date/null/<?php echo $view_data['account_id']; ?>">View this users activity.</a>
	   <table>
		   <?php if($user['admin']) { ?>
		<tr>
			<td><strong>Account ID:</strong></td>
		   <td><?php echo $view_data['account_id']; ?></td>
		</tr>
		<?php } ?>
		<tr>
			<td><strong>Username:</strong></td>
		   <td><?php echo $view_data['username']; ?></td>
		</tr>
		<tr>
			<td><strong><label for="email">Email: </label></strong></td>
			<?php if($allow_submit){ ?>
				<td><input type="email" name="email" value="<?php echo $view_data['email']; ?>" placeholder="Email" required aria-required="true"></td>
			<?php } else { ?>
				<td><?php echo $view_data['email']; ?></td>
				<?php } ?>
		</tr>
		<tr>
			<td><strong>Profile Picture:</strong></td>
			<td>
				<div class="">
					<img id="profile-image" src="data:image/<?php echo $view_data['content_type'] . ';base64,' . base64_encode($view_data['image']); ?>" alt="<?php echo $view_data['username'];?>&apos;s profile picture">
				</div>
		 	   <div id="image-upload" style="display: none;">
		 		   <div class="">
		 			   Choose a profile image:
		 		   </div>
		 		   <input type="file" name="image" id="image">
		 	   </div>
				 <?php if($allow_submit){ ?>
				<button type="button" name="button" class="btn-alt" id="changeImage">Change Image</button></td>
				<?php } ?>
		</tr>
		<?php if(is_array($user) && $user['admin'] && $view_data['account_id'] != $user['account_id']) { ?>
			<tr>
				<td>
					<?php if($view_data['active']) { ?>
						<strong>Account Status:</strong>
					<?php } else { ?>

					<?php } ?>
				</td>
				<td id="tdStatus">
					<?php if($view_data['active']) { ?>
						Account is active
						<br>
						<button type="button" name="button" class="btn-alt" id="toggleAccountStatus">Click to disable</button>
					<?php } else { ?>
						Account is in-active
						<br>
						<button type="button" name="button" class="btn-alt" id="toggleAccountStatus">Click to enable</button>
					<?php } ?>
				</td>
			</tr>
		<?php } ?>
		<?php if($allow_submit){ ?>
		<tr>
			<td colspan="2">
		 	   <div class="text-center">
		 		   <input type="submit" name="submit" class="btn" value="Update Profile">
		 	   </div>
		   </td>
		</tr>
		<?php } ?>
	   </table>
   </form>
</section>

<script type="text/javascript">
	window.addEventListener('load', function() {
		var btnImageChange = document.getElementById('changeImage');
		if(btnImageChange != null) {
			btnImageChange.addEventListener('click', function() {
				document.getElementById('image').click();
			});
		}
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

<script type="text/javascript">
	var crumbs = [
		{
			href: "/",
			text: "Home"
		},
		{
			href: "/account/profile/<?php echo $view_data['account_id']; ?>",
			text: "Profile"
		}
	]
	$(document).ready(Breadcrumbs(crumbs))

	function RenderStatus(status) {
		console.log(status);
		$('#tdStatus').html('Account is ' + (status ? 'active' : 'in-active') + '<br><button type="button" name="button" class="btn-alt" id="toggleAccountStatus">Click to ' + (status ? 'disable' : 'enable') + '</button>')
	}
	var accountStatus = <?php echo $view_data['active'] ? 'true' : 'false'; ?>;
	$('body').on('click', '#toggleAccountStatus', function() {
		accountStatus = !accountStatus;
		newStatus = accountStatus;
		RenderStatus(newStatus);
		$.ajax({method: 'get', url:'/account/set_status/<?php echo $view_data['account_id']; ?>/' + (accountStatus ? 1 : 0)}).fail(function() {
			RenderStatus(!newStatus);
			alert('There was a problem setting the account status');
		})
	})
</script>
