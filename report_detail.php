<?php
include('inc/header.php');
include('inc/nav.php');

if (isset($_GET['protocol']) && $_GET['protocol'] != '') {
	$protocolQueryString = $_GET['protocol'];
} else {
	header("location:report.php");
}

$sql = "SELECT COUNT(*) as total_records, protocol FROM pcap_data GROUP BY protocol";
$queryRS = query($sql);
$totalRows = row_count($queryRS);
$resultSet = get_rows($queryRS);

$totalRowsDetail = 0;

//Related with Network Graph
//$tcpSql = "SELECT source FROM pcap_data WHERE protocol = 'TCP' LIMIT 1";
$tcpSql = "SELECT source, COUNT(*) AS total_records FROM pcap_data WHERE protocol = 'TCP' group by source order by total_records desc LIMIT 1";
$tcpQueryRS = query($tcpSql);
$tcpRow = mysqli_fetch_row($tcpQueryRS);
$tcpDestinations = array();
$tcpNetworkTotalRows = 0;
if (!empty($tcpRow)) {
	$sourceIP = $tcpRow[0];
	$tcpNetworkSql = "SELECT DISTINCT(destination) as dest FROM pcap_data WHERE protocol = 'TCP' AND source = '" . $sourceIP . "'";
	$tcpNetworkQueryRS = query($tcpNetworkSql);
	$tcpNetworkTotalRows = row_count($tcpNetworkQueryRS);
}
if ($protocolQueryString == 'Network') {
	if ($tcpNetworkTotalRows > 0) {
		$sourceCountry = getCountryByIp($sourceIP);
		//check if network data is already store or not if not then store it
		$networkDataSql = "SELECT * FROM network_data";
		$networkDataSqlQueryRS = query($networkDataSql);
		$networkDataRows = get_rows($networkDataSqlQueryRS);
		if (!empty($networkDataRows)) {
			$itr = 0;
			foreach ($networkDataRows as $row) {
				$tcpDestinations[$itr]['name'] = trim($row['destination']);
				$tcpDestinations[$itr]['info'] = trim($row['country']);
				$tcpDestinations[$itr]['value'] = $row['destination_count'];
				$itr++;
			}
		} else {
			$tcpNetworkRows = get_rows($tcpNetworkQueryRS);
			$itr = 0;
			foreach ($tcpNetworkRows as $row) {
				$tcpDestinations[$itr]['name'] = $dest = trim($row['dest']);
				$tcpDestinations[$itr]['info'] = $country = trim(getCountryByIp($row['dest']));
				//Here we need to get total number of records by sourceIp and destination from main table i.e. pcap_data				
				$tcpNRS = query("SELECT id FROM pcap_data WHERE protocol = 'TCP' AND TRIM(source) = '" . trim($sourceIP) . "' AND TRIM(destination) = '" . $dest . "'");
				$tcpDestinations[$itr]['value'] = $destination_count = row_count($tcpNRS);

				$sql = "INSERT INTO network_data (`destination`, `country`, `destination_count`) ";
				$sql .= "VALUES ('$dest','$country', $destination_count)";
				confirm(query($sql));

				$itr++;
			}
		}
	}
} else {
	$sqlDetail = "SELECT * FROM pcap_data WHERE protocol = '" . $protocolQueryString . "' GROUP BY source, destination";
	$queryRSDetail = query($sqlDetail);
	$totalRowsDetail = row_count($queryRSDetail);
	$resultSetDetail = get_rows($queryRSDetail);
}
?>

<div class="wrapper">
	<?php
	if ($totalRows > 0) {
	?>
		<nav id="sidebar">
			<ul class="list-unstyled components mb-5">
				<li>
					<a href="report.php"> Overview</a>
				</li>
				<?php
				$itr = 0;
				foreach ($resultSet as $row) {
				?>
					<li class="<?php if ($protocolQueryString == $row['protocol']) {
									echo 'active';
								} ?>">
						<a href="report_detail.php?protocol=<?php echo $row['protocol']; ?>"> <?php echo $row['protocol']; ?></a>
					</li>
				<?php } ?>
				<?php if ($tcpNetworkTotalRows > 0) { ?>
					<li class="<?php if ($protocolQueryString == 'Network') {
									echo 'active';
								} ?>">
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
						<h3><?php echo $protocolQueryString; ?></h3>
						<?php
						if ($protocolQueryString != 'Network') {
						?>
							<div class="text-muted">Note: Unique Records with respect to Source & Destination</div>
						<?php } ?>

						<?php
						if ($protocolQueryString != 'Network') {
						?>
							<?php
							if ($totalRowsDetail > 0) {
							?>

								<div class="row">
									<div class="col-sm-12">
										<table id="example1" class="table table-bordered table-striped dataTable dtr-inline" role="grid" aria-describedby="example1_info">
											<thead>
												<tr role="row">
													<th>Source (IP)</th>
													<th>Destination (IP)</th>
													<th>Details</th>
												</tr>
											</thead>
											<tbody>
												<?php
												$itr = 0;
												foreach ($resultSetDetail as $rowDetail) {
												?>
													<tr>
														<td><?php echo $rowDetail['source']; ?></td>
														<td><?php echo $rowDetail['destination']; ?></td>
														<td><?php echo $rowDetail['details']; ?></td>
													</tr>
												<?php } ?>
											</tbody>
										</table>
									</div>
								</div>

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
						} else {
							include_once('network.php');
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