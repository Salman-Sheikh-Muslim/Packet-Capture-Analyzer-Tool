<style>
	#chartdiv {
		width: 100%;
		max-width: 100%;
		height: 750px;
	}
</style>
<script src="plugins/amcharts/core.js"></script>
<script src="plugins/amcharts/charts.js"></script>
<script src="plugins/amcharts/forceDirected.js"></script>
<script src="plugins/amcharts/animated.js"></script>
<div id="chartdiv"></div>
<?php
$value = max(array_column($tcpDestinations, 'value'));
?>
<script>
	// Themes begin
	am4core.useTheme(am4themes_animated);
	// Themes end

	var chart = am4core.create("chartdiv", am4plugins_forceDirected.ForceDirectedTree);
	//chart.legend = new am4charts.Legend();

	var networkSeries = chart.series.push(new am4plugins_forceDirected.ForceDirectedSeries())

	networkSeries.data = [{
		name: "<?php echo $sourceIP; ?>",
		info: "<?php echo $sourceCountry; ?>",
		value: <?php echo ($value * 2); ?>,
		children: <?php echo json_encode($tcpDestinations, TRUE); ?>
	}];

	networkSeries.dataFields.linkWith = "linkWith";
	networkSeries.dataFields.name = "name";
	networkSeries.dataFields.id = "name";
	networkSeries.dataFields.value = "value";
	networkSeries.dataFields.info = "info";
	networkSeries.dataFields.children = "children";

	networkSeries.nodes.template.tooltipText = "[bold]IP: [/]{name}\n[bold]Country: [/]{info}\n[bold]Total Requests: [/]{value}";
	networkSeries.nodes.template.fillOpacity = 1;

	networkSeries.nodes.template.label.text = "{name}"
	networkSeries.fontSize = 8;
	networkSeries.maxLevels = 2;
	networkSeries.maxRadius = am4core.percent(10);
	networkSeries.manyBodyStrength = -16;
	networkSeries.nodes.template.label.hideOversized = true;
	networkSeries.nodes.template.label.truncate = true;

	networkSeries.nodes.template.adapter.add("tooltipText", function(text, target) {
		if (target.dataItem) {
			switch (target.dataItem.level) {
				case 0:
					return "[bold]IP: [/]{name}\n[bold]Country: [/]{info}";
				default:
					return "[bold]IP: [/]{name}\n[bold]Country: [/]{info}\n[bold]Total Requests: [/]{value}";
			}
		}
		return text;
	});
</script>