<?php
include('inc/header.php');
include('inc/nav.php');
?>
<div class="wrapper">
	<section class="content">

		<div class="card">
			<div class="card-body">
				<h1>Online pcap file analyzer</h1>
				<p>Allow read and view pcap file, analyze IPv4/IPv6, HTTP, Telnet, FTP, DNS, SSDP, WPA protocols, build map of network structure and nodes activity graph, sniff and analyze network traffic and other pcap data.</p>
				<p>Analyse pcap files to view HTTP headers and data, extract transferred binaries, files, office documents, pictures and find passwords.</p>
				<hr>
				<p>
					<a href="report.php" class="btn btn-primary">View analyzed pcaps</a>
					<a href="upload.php" class="btn btn-primary">Upload pcap file</a>
				</p>

				<div class="video-wrapper"><iframe width="560" height="315" src="https://www.youtube.com/embed/TVzLAmQvw_U" srcdoc="<style>*{padding:0;margin:0;overflow:hidden}html,body{height:100%}img,span{position:absolute;width:100%;top:0;bottom:0;margin:auto}span{height:1.5em;text-align:center;font:48px/1.5 sans-serif;color:white;text-shadow:0 0 0.5em black}</style><a href=https://www.youtube.com/embed/TVzLAmQvw_U?autoplay=1><img src=https://img.youtube.com/vi/TVzLAmQvw_U/hqdefault.jpg alt='A-Packets Online pcap analyzer overview'><span>▶</span></a>" allowfullscreen="" title="A-Packets Online pcap analyzer overview"></iframe></div>
			</div>



		</div>

	</section>

</div>
<?php
include('inc/footer.php');
?>