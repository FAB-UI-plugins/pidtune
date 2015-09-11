<script type="text/javascript">

var max_plot = 100;
var nozzle_temperatures = [];
var nozzlePlot = "";
var now = new Date().getTime();
var values = [];

var $chrt_border_color = "#efefef";
var $chrt_grid_color = "#DDD";
var $chrt_main = "#E24913";
/* red       */
var $chrt_second = "#6595b4";
/* blue      */
var $chrt_third = "#FF9F01";
/* orange    */
var $chrt_fourth = "#7e9d3a";
/* green     */
var $chrt_fifth = "#BD362F";
/* dark red  */
var $chrt_mono = "#000";

var prev = 100;
var manualActive = false;
var autoActive = false;

$(document).ready(function() {

	$("#manual").on('click', function() {
		manualActive = !manualActive;

		if(manualActive){
			$("#manual").html("Manual Tuning Active");
		}else{
			$("#manual").html("Start Manual Tuning");
		}

	});

	$("#auto").on('click', function() {
		autoActive = !autoActive;

		if(autoActive){
			$("#auto").html("Autotuning Active");
		}else{
			$("#auto").html("Start Autotuning");
		}

	});

	initGraphs();
	update();
	
}); /* End of init */


function update() {
	if(manualActive){
		
		prev = prev + Math.random() *20 - 10;
		if(prev>300){
			prev = 300;
		}
	
		if(prev < 0){
			prev = 0;
		}
	
		addNozzleTemperature(prev);
		updateNozzleGraph();
		$(".nozzle-temperature").html(parseInt(prev) + '&deg;C');
	}

	setTimeout(update, 100);
}

function addNozzleTemperature(temp){
	
	var now = new Date().getTime();
	var obj = {'temp': parseFloat(temp), 'time': now};
	
	if(nozzle_temperatures.length == max_plot){
		nozzle_temperatures.shift();
	}
	
	nozzle_temperatures.push(obj);
}


function getNozzlePlotTemperatures(){
	
	var res = [];
	
	for (var i = 0; i < nozzle_temperatures.length; ++i) {
		var obj = nozzle_temperatures[i];
		res.push([obj.time, obj.temp]);
	}

	return res;
	
}

function updateNozzleGraph(){
	
	
	try{
		
		if(typeof nozzlePlot == "object" ){
		
			nozzlePlot.setData([getNozzlePlotTemperatures()]);
			nozzlePlot.draw();
			nozzlePlot.setupGrid();
		
		}
		
	}catch(e){
		console.log(e);
	}
	
	
}

function  initGraphs(){
	
	
	
	 nozzlePlot = $.plot("#nozzle-chart", [ getNozzlePlotTemperatures() ], {
        	series : {
				lines : {
					show : true,
					lineWidth : 1.2,
					fill : true,
					fillColor : {
						colors : [{
							opacity : 0.1
						}, {
							opacity : 0.15
						}]
					}
				},
				
				shadowSize : 0
			},
			xaxis: {
			    mode: "time",
			    show: true
			},
			yaxis: {
		        min: 0,
		        max: 300,
		        tickSize: 50,        
		        tickFormatter: function (v, axis) {
		            return v + "&deg;C";
		        }
        
    		},
			grid : {
				hoverable : true,
				clickable : true,
				tickColor : $chrt_border_color,
				borderWidth : 0,
				borderColor : $chrt_border_color,
			},
			tooltip : true,
			tooltipOpts : {
				content : "%y &deg;C",
				defaultTheme : false
			},
			colors : [$chrt_main, $chrt_second],
							
			});
	
	
	
	
	updateNozzleGraph();
	
	
}
</script>
