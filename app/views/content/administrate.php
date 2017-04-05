<?php
global $sub_path;
$stats = $view_data['stats'];
$daily_visitors = $view_data['daily_visitors']
?>
<section id="administrate" class="flex flex-wrap">
	<h2>Administration Page</h2>
	<div class="clear-flex">

	</div>
	<div class="flex-one">
		<div id="visitors" class="admin-group">
			<h4 class="admin-header">Visitors</h4>
			<div class="flex">
				<div class="flex-one">
					<div class="stats">
						<h2><?php echo (int)$stats['visitors_today']; ?></h2>
						<span>Visitors Today</span>
					</div>
				</div>
				<div class="flex-one">
					<div class="stats">
						<h2><?php echo (int)$stats['visitors_daily_average']; ?></h2>
						<span>Daily Average Visitors</span>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="flex-one">
		<div id="members" class="admin-group">
			<h4 class="admin-header">Members</h4>
			<div class="flex">
				<div class="flex-one flex">
					<div class="stats">
						<h2><?php echo (int)$stats['members']; ?></h2>
						<span>Total Members</span>
					</div>
					<div class="stats">
						<h2><?php echo (int)$stats['members_today']; ?></h2>
						<span>Members Today</span>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="flex-one ">
		<div id="discussion" class="admin-group">
			<h4 class="admin-header">Discussion</h4>
			<div class="flex">
				<div class="flex-one flex">
					<div class="stats">
						<h2><?php echo (int)$stats['threads_total']; ?></h2>
						<span>Threads Total</span>
					</div>
					<div class="stats">
						<h2><?php echo (int)$stats['threads_today']; ?></h2>
						<span>Threads Created Today</span>
					</div>
					<div class="stats">
						<h2><?php echo (int)$stats['posts_today']; ?></h2>
						<span>Post Replies Today</span>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="clear-flex"></div>
	<div class="flex-one">
		<div class="admin-group" id="pie-visitors" style="position: relative;">
			<h4 class="admin-header">Visitor Distribution</h4>
			<div class="chart-wrapper">

				<canvas id="pie-visitors-chart" height="300" width="300">

				</canvas>
			</div>
		</div>
	</div>
	<div class="flex-two">
		<div class="admin-group" id="daily_visitors">
			<h4 class="admin-header">Visitors By Day</h4>
			<div class="chart-wrapper">

				<canvas id="daily_visitors_chart" height="300" width="300">

				</canvas>
			</div>
		</div>
	</div>
	<div class="clear-flex"></div>
	<div class="flex-three">
		<div class="admin-group" id="user_activity">
			<h4 class="admin-header">User activity</h4>
			<input type="text" id="activity_filter" value="" placeholder="Filter by Action Taken" /> <a href="javascript:ShowPossibleActions();">Show possible actions</a>
			<div class="">
				<?php echo Html::render_action('activity_admin', 'content'); ?>
			</div>
		</div>
	</div>
	<!-- <div class="flex-one admin-group">

	</div> -->
</section>


<script type="text/javascript" src="<?php echo "$sub_path/content_static/scripts/moment.min.js"; ?>"></script>
<script type="text/javascript" src="<?php echo "$sub_path/content_static/scripts/Chart.min.js"; ?>"></script>
<script type="text/javascript">
	$(document).ready(function() {
		//visitors pie chart
		var ctx = $("#pie-visitors-chart");
		var options = {
			responsize: true,
			maintainAspectRatio: false
		};
		var data = {
		    datasets: [{
		        data: [
		            <?php echo (int)$stats['visitors_today'] - (int)$stats['visitors_members_today']; ?>,
		            <?php echo (int)$stats['visitors_members_today']; ?>,
		        ],
		        backgroundColor: [
		            "#FF6384",
		            "#4BC0C0",
		            "#FFCE56",
		            "#E7E9ED",
		            "#36A2EB"
		        ],
		        label: 'Guests vs Members' // for legend
		    }],
		    labels: [
		        "Guests",
		        "Members",
		    ]
		};
		var myPieChart = new Chart(ctx,{
		    type: 'pie',
		    data: data,
		    options: options
		});

		var ctx = $("#daily_visitors_chart");
		var data = {
		    datasets: [{
		        data: [
					<?php foreach($daily_visitors as $day) {
						echo $day['visitors_day'] . ',';
					} ?>
		        ],
		        backgroundColor: '#5dc04b',
		        label: 'Visitors' // for legend
		    }],
		    labels: [
				<?php foreach($daily_visitors as $day) {
					echo "'" . $day['day'] . "',";
				} ?>
		    ]
		};
		var date_comp = data.labels[data.labels.length-1].split('-');
		date_comp[2] = parseInt(date_comp[2])+1;
		if(date_comp[2].toString().length == 1) {
			date_comp[2] = '0' + date_comp[2].toString();
		}
		var max = date_comp.join('-')
		var options = {
			responsize: true,
			maintainAspectRatio: false,
			scales: {
				xAxes: [{
					type: 'time',
					time: {
						unit: 'day',
						unitStepSize: 1,
						max: max
					}
				}],
				yAxes: [{
					ticks: {
						min: 0
					}
				}]
			},
			legend: {
				display: false
			}
		};
		var myBarChart = new Chart(ctx,{
		    type: 'bar',
		    data: data,
		    options: options
		});
	})
</script>

<script type="text/javascript">
	$('body').on('keyup', '#activity_filter', function(e) {
		var search_term = $(e.target).val().toLowerCase();
		if(search_term.trim() == "") {
			$('[data-action]').show();
			$('[data-action]').each(function() {
				$(this).find('.tdAction').html($(this).attr('data-action'));
			})
		} else {
			$('[data-action]').each(function() {
				if($(this).attr('data-action').toLowerCase().indexOf(search_term) == -1) {
					$(this).hide();
				} else {
					var text = $(this).find('.tdAction').text();
					$(this).find('.tdAction').html(text.replace(search_term.toLowerCase(), '<span class="underline">' + search_term + '</span>'));
				}
			})
		}

	})
	function ShowPossibleActions() {
		var actions = 'view\npost_submit\nthread_submit\nsearch\nthread_remove\npost_remove\npost_edit\nlogin\nlogout\nregister\nprofile_update\nforgot_password_invalid\nforgot_password\nrecover_password\naccount_status';
		alert('Possible Actions:\n\n' + actions)
	}
</script>
