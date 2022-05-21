<?php
include('inc/header.php');
include('inc/nav.php');
//https://shantoroy.com/networking/convert-pcap-to-csv-using-tshark/

$errors = []; // Store errors here
$sucess = false;

if (isset($_POST['pcap-submit'])) {
	$uploadDirectory = __DIR__ . "/files/";
	$fileExtensionsAllowed = ['pcap']; // These will be the only file extensions allowed 
	$fileName = $_FILES['pcap_file']['name'];
	$fileSize = $_FILES['pcap_file']['size'];
	$fileTmpName  = $_FILES['pcap_file']['tmp_name'];
	$fileType = $_FILES['pcap_file']['type'];
	$fileExtension = strtolower(end(explode('.', $fileName)));

	$uploadPath = $uploadDirectory . basename($fileName);

	if (empty($fileName)) {
		$errors[] = "There is no file to upload.";
	} else {
		if (!in_array($fileExtension, $fileExtensionsAllowed)) {
			$errors[] = "Sorry, but you have uploaded invalid file. You need to upload pcap file.";
		}

		if ($fileSize > 20000000) {
			//$errors[] = "File exceeds maximum size (20MB)";
		}

		if (empty($errors)) {
			$didUpload = @move_uploaded_file($fileTmpName, $uploadPath);

			if ($didUpload) {
				//Need to do all the logic here... 
				//convert pcap file into csv and save it
				//then read csv file line by line and save data into database
				$csvFile = time() . '-pcapout.csv';
				exec('tshark -r ' . $uploadPath . ' -T tabs > ' . $uploadDirectory . $csvFile, $output, $resultCode);
				if ($resultCode > 0) {
					$errors[] =   "There is problem with reading the PCAP file.";
				} else {
					//truncate table first
					confirm(query('TRUNCATE TABLE pcap_data'));
					confirm(query('TRUNCATE TABLE network_data'));

					$file = $uploadDirectory . $csvFile;
					$fh = fopen($file, 'r');
					while (($line = fgetcsv($fh, 0, "\t")) !== false) {
						$f_no = $line[0];
						$time = escape($line[1]);
						$source = escape($line[2]);
						$destination = escape($line[4]);
						$protocol = escape($line[5]);
						$length = $line[6];
						$details = escape($line[7]);

						$sql = "INSERT INTO pcap_data (`f_no`, `time`, `source`, `destination`, `protocol`, `length`, `details`) ";
						$sql .= "VALUES ($f_no,'$time','$source','$destination','$protocol',$length,'$details')";
						confirm(query($sql));
					}
					//remove the pcap file
					unlink($uploadPath);
					//remove the csv file
					unlink($uploadDirectory . $csvFile);
					$sucess = true;
				}
			} else {
				$errors[] =   "An error occurred while uploading PCAP file.";
			}
		}
	}
}

?>

<div id="pageloader">
	<img src="loader-large.gif" alt="processing..." />
</div>

<div class="wrapper">
	<section class="content">

		<div class="card">
			<div class="card-body">
				<div class="text-center">
					<h1>Upload pcap</h1>
					<h2>to analyze network structure, HTTP headers and data, FTP, Telnet, WiFi, ARP, SSDP and other</h2>
				</div>
				<div class="row">
					<div class="col-lg-6 col-lg-offset-3">
						<?php
						if (!empty($errors)) {
							foreach ($errors as $error) {
								echo '<div class="alert alert alert-danger">' . $error . '
                          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                          <span aria-hidden="true">&times;</span></button></div>';
							}
						}
						?>
					</div>
				</div>
				<div class="row">

					<div class="col-md-6 col-md-offset-3">
						<div class="panel panel-login">
							<div class="panel-heading" style="text-align: left;">
								<a href="javascript:void();" class="active">Upload File</a>
								<hr>
							</div>
							<div class="panel-body">
								<div class="row">
									<div class="col-lg-12">
										<form id="pcap-form" method="post" role="form" name="pcap-form" enctype="multipart/form-data">
											<div class="form-group">
												<input type="file" name="pcap_file" id="fileToUpload">
											</div>

											<div class="form-group">
												<div class="row">
													<div class="col-sm-4 col-sm-offset-4">
														<input type="submit" name="pcap-submit" id="pcap-submit" class="form-control btn btn-login" value="Submit">
													</div>
												</div>
											</div>
										</form>
										<?php
										if ($sucess) {
										?>
											<div class="alert alert-success text-center">
												<h4>Processing pcap completed, <a href="report.php">view report</a></h4>
											</div>
										<?php } ?>
									</div>
								</div>
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>
	</section>
</div>

<?php
include('inc/footer.php');
?>