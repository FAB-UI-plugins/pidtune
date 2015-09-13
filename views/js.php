<script type="text/javascript">

var max_plot = 100;
var nozzle_temperatures = [];
var target_temperatures = [];
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
var pid = 0;

$(document).ready(function() {

	$(".actionButton").on('click', function() {
		sendReceive({type: 'command', action: $(this).attr('id')});

	});

	$(".valueInput").on('change', function() {
		sendReceive({type: 'valueChange', param: $(this).attr('id'), value: $(this).val()});

	});

	$("input[name='pid-type']").on('change', function() {
		sendReceive({type: 'valueChange',  param: 'pid-type', value: $(this).val()});

	});

	$("#start").on('click', function() {
		runPidTune();

	});



	initGraphs();
	update();
	
}); /* End of init */


function update() {
	sendReceive({type: 'update'});
	
	

	setTimeout(update, 1000);
}


function addTargetTemperature(temp){
	
	var now = new Date().getTime();
	var obj = {'temp': parseFloat(temp), 'time': now};
	
	if(target_temperatures.length == max_plot){
		target_temperatures.shift();
	}
	
	target_temperatures.push(obj);
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
	
	var res1 = [];
	var res2 = [];
	
	for (var i = 0; i < nozzle_temperatures.length; ++i) {
		var obj = nozzle_temperatures[i];
		res1.push([obj.time, obj.temp]);
	}

	for (var i = 0; i < target_temperatures.length; ++i) {
		var obj = target_temperatures[i];
		res2.push([obj.time, obj.temp]);
	}

	return [res1, res2];
	
}

function updateNozzleGraph(){
	
	
	try{
		
		if(typeof nozzlePlot == "object" ){
		
			nozzlePlot.setData(getNozzlePlotTemperatures());
			nozzlePlot.draw();
			nozzlePlot.setupGrid();
		
		}
		
	}catch(e){
		console.log(e);
	}
	
	
}

function  initGraphs(){
	
	
	
	 nozzlePlot = $.plot("#nozzle-chart", getNozzlePlotTemperatures(), {
        	series : {
				lines : {
					show : true,
					lineWidth : 1.2,
					fill : false,
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

var data = {
        type: 'shutdown',
        time: now,
        run: true
    };

function handleReturn(data) {

	console.log(data);
	
	
	if(data['type'] == 'update'){
		 
		$("#min").html(parseFloat(data['min']) + '&deg;C');
		$("#max").html(parseFloat(data['max']) + '&deg;C');
		$("#bias").html(parseFloat(data['bias']));
		$("#Ku").html(parseFloat(data['Ku']));
		$("#Tu").html(parseFloat(data['Tu']));
		$(".nozzle-temperature").html(parseFloat(data['temp']) + '&deg;C');
		$(".target-temperature").html(parseFloat(data['actual-target']) + '&deg;C');
		addTargetTemperature(parseFloat(data['actual-target']));
		addNozzleTemperature(parseFloat(data['temp']));
		updateNozzleGraph();
		if(data['valuesChanged']){
			sendReceive({type: 'valueChange',  param: null});
		}
			
	}

	if(data['type'] == 'valueChange'){
		 
		for ( var elem in data['values']) {

			if(elem == 'pid-type'){
				$('input[name=' + elem + '][value=' + data['values'][elem] + ']').prop('checked', true);
			}else{

				$('#'+elem).val(data['values'][elem]);
				console.log(elem);
				console.log(data['values'][elem]);
			}			
		}
	}
}

function sendReceive(data) {


    $.ajax({
            type: "POST",
            url: 'http://' + window.location.hostname + ':9002/',
            data: data,
            dataType: "json"
        }).done(function(data) {
        	
        	handleReturn(data);
        	
        });
}

function runPidTune() {

    var now = jQuery.now();


    $.ajax({
            type: "POST",
            url: "/fabui/application/plugins/pidtune/ajax/run.php",
            data: {
                time: now
            },
            dataType: "html"
        }).done(function(data) {
        	
        	
        });
}
</script>
