<?php
include('inc/header.php');
include('inc/nav.php');

$sql = "SELECT COUNT(*) as total_records, protocol FROM pcap_data GROUP BY protocol";
$queryRS = query($sql);
$totalRows = row_count($queryRS);
$resultSet = get_rows($queryRS);
$colors = ['#00c0ef', '#00a65a', '#f39c12', '#dd4b39', '#8f5904', '#9b1605', '#0083a3', '#036c3c', '#3ca776', '#b7883d', '#ab3d2f', '#3ca7a7', '#1f5685', '#831f85', '#9b3a15', '#0363c5', '#c5035d', '#7a03c5', '#0363c5', '#03c551', '#9d6e04', '#270136', '#48215c', '#9d3c04', '#5c214a', '#325c21', '#012f36', '#214a5c', '#286446', '#913e34', '#00a65a', '#48215c', '#00a65a', '#f39c12',  '#3ca776', '#b7883d', '#ab3d2f', '#3ca7a7', '#1f5685'];

//Related with Network Graph
$tcpSql = "SELECT source FROM pcap_data WHERE protocol = 'TCP' LIMIT 1";
$tcpQueryRS = query($tcpSql);
$tcpRow = mysqli_fetch_row($tcpQueryRS);
$tcpNetworkTotalRows = 0;
if (!empty($tcpRow)) {
	$sourceIP = $tcpRow[0];
	$tcpNetworkSql = "SELECT DISTINCT(destination) as dest FROM pcap_data WHERE protocol = 'TCP' AND source = '" . $sourceIP . "'";
	$tcpNetworkQueryRS = query($tcpNetworkSql);
	$tcpNetworkTotalRows = row_count($tcpNetworkQueryRS);
}

?>

<div class="wrapper">
	<?php
	if ($totalRows > 0) {
	?>
		<nav id="sidebar">
			<ul class="list-unstyled components mb-5">
				<li class="active">
					<a href="report.php"> Overview</a>
				</li>
				<?php
				$itr = 0;
				foreach ($resultSet as $row) {
				?>
					<li class="">
						<a href="report_detail.php?protocol=<?php echo $row['protocol']; ?>"> <?php echo $row['protocol']; ?></a>
					</li>
				<?php } ?>
				<?php if ($tcpNetworkTotalRows > 0) { ?>
					<li>
						<a href="report_detail.php?protocol=Network"> Network</a>
					</li>
				<?php } ?>
			</ul>
		</nav>
	<?php } ?>
	<div class="row" id="content">
		<section class="content">

			<div class="card">
				<div class="card-body">
					<div class=" col-md-12">
						<?php
						if ($totalRows > 0) {
						?>
							<div class="row">
								<div class="col-lg-12">
									<?php
									$itr = 0;
									foreach ($resultSet as $row) {
									?>
										<div class="col-lg-3 col-xs-6">

											<!-- <div class="small-box bg-aqua" style="background-color: <?php echo $colors[$itr]; ?> !important;"> -->
											<div class="small-box bg-gray-light">
												<div class="inner">
													<sup class="protocol-count">
														<div class="badge bg-black-gradient"><?php echo $row['total_records']; ?></div>
													</sup>
													<h3><?php echo $row['protocol']; ?></h3>

												</div>
											</div>
										</div>
									<?php
										$itr = $itr + 1;
									}
									?>
								</div>
							</div>
							<div class="row">
								<div class="col-lg-12">
									<div id='protocolDIV'>
									</div>
								</div>
							</div>
							<script>
								var data = <?php echo json_encode($resultSet, JSON_NUMERIC_CHECK); ?>;
								let pos_trace = {
									values: [],
									labels: [],
									type: 'pie',
									name: 'Total',
									hole: 0.3,
									textinfo: "label+percent+name",
									marker: {
										colors: <?php echo json_encode($colors); ?>
									}
								};
								data.forEach(function(val) {
									pos_trace.values.push(val["total_records"]);
									pos_trace.labels.push(val["protocol"]);
								});
								var pos_layout = {
									autosize: true,
									height: 550
								};
								var config = {
									responsive: true
								}
								Plotly.newPlot('protocolDIV', [pos_trace], pos_layout, config);
							</script>


						<?php
						} else {
						?>
							<div class="row">
								<div class="col-lg-12">
									<div class="alert alert-danger">
										<h4>Sorry, but there is no information available!</h4>
									</div>
								</div>
							</div>
						<?php
						}
						?>
					</div>
				</div>
			</div>
		</section>
	</div>
</div>
<?php
include('inc/footer.php');
?>