<?php
global $sub_path;
$stats = $view_data['stats'];
$daily_visitors = $view_data['daily_visitors']
?>
<section id="administrate" class="flex flex-wrap">
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
		date_comp[2]++;
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

<style media="screen">
	#administrate > [class^=flex] {
		margin-bottom: 2rem;
	}
	.admin-group {
		background: #f8f8f8;
		border-top: 2px solid #ee0000;
		box-shadow: 0 4px 6px 0 rgba(0,0,0,0.15), 0px 0 6px 0 rgba(0,0,0,0.15);
	}
	#members {
		border-top-color: #0028ee;
	}
	#discussion {
		border-top-color: #5dc04b;
	}
	#pie-visitors-chart {
		flex: 2;
	}
	#daily_visitors {
		border-top-color: #5dc04b;
	}
	.chart-wrapper canvas {
		height: 300px!important;
		width: 100%!important;
	}
	.chart-wrapper {
		padding: 0.5rem;
	}
	.admin-header {
		border-bottom: 2px solid #e8e8e8;
		padding: 0.75rem 0.5rem 0.5rem 0.5rem;
		margin: 0 0.5rem;
	}
	.flex {
		justify-content: space-between;
		display: flex;
	}
	.flex-one, .flex-two {
		display: flex;
		padding: 0 1rem;
	}
	.flex-one > *, .flex-two > * {
		flex: 1;
	}
	.flex-one {
		flex: 1;
	}
	.flex-one + .flex-two {
		padding-lefT: 3rem;
	}
	.flex-one + .flex-two > * {
		margin-left: -2rem;
	}
	.flex-two + .flex-one {
		padding-lefT: 3rem;
	}
	.flex-two + .flex-one > * {
		margin-left: -2rem;
	}
	.flex-two {
		flex: 2;
	}
	.flex-wrap {
		flex-wrap: wrap;
	}
	.clear-flex {
		width: 100%;
	}
	.stats {
		margin: 0 0 1rem 0;
		text-align: center
	}
	.stats h2 {
		margin: 1rem 0 0.5rem 0;
	}
	.stats span {
		display: inline-block;
		font-size: 0.8rem;
		margin: 0 0.5rem;
	}
</style>
