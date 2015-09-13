
<div class="row">

	<div class="col-md-6 graph-container" style="height: 450px">
		<h5 class="text-center"><i class="fab-lg fab-fw icon-fab-term "></i> Nozzle (<span class="nozzle-temperature"></span>)
																 &nbsp/&nbsp Target (<span class="target-temperature"></span>)</h5>
		<div id="nozzle-chart" class="nozzle-graph"></div>
	</div>
		
	<div class="col-sm-6">
		<div class="form-group">
			<div class="col-md-12">
				<p>	
					<h5>
						Bias: <span id="bias" class="text-info">0</span>
					</h5>
				</p>
				<p>		
					<h5>
						Min: <span id="min" class="text-info">34&deg;C</span>
					</h5>
				</p>
				<p>		
					<h5>
						Max: <span id="max" class="text-info">45&deg;C</span>
					</h5>
				</p>
				<p>
					<h5>
						Ku: <span id="Ku" class="text-info">345</span>
					</h5>
				</p>
				<p>
					<h5>
						Tu: <span id="Tu" class="text-info">345</span>
					</h5>
				</p>
				<p>
					
					Kp: <input type="text" id="Kp" class="valueInput" name="Kp" style="width: 50px" value="10.5" />
					&nbsp&nbsp
					Ki: <input type="text" id="Ki" class="valueInput" name="Ki" style="width: 50px" value="10.5" />
					&nbsp&nbsp
					Kd: <input type="text" id="Kd" class="valueInput" name="Kd" style="width: 50px" value="10.5" />
					&nbsp&nbsp
					<button id="get-param" class="actionButton">Get Current Parameters</button>
				</p>
				<p>
					<form>
						<h5>
							<input type="radio" name="pid-type" checked="checked" value="cl"> Classic PID
							<br>
							<input type="radio" name="pid-type" value="pe"> Pessen Integral Rule
							<br>
							<input type="radio" name="pid-type" value="so"> Some Overshoot
							<br>
							<input type="radio" name="pid-type" value="no"> No Overshoot
							<br>
						</h5>
					</form>
					
				</p>
				<p>
					<button id="auto" class="actionButton">Start Autotuning</button>
					&nbsp
					Extruder: <input type="text" id="extruder" class="valueInput" style="width: 50px" value="0" />
					Target Temp: <input type="text" id="auto-target" class="valueInput" style="width: 50px" value="200" />
					Cycles: <input type="text" id="cycle" class="valueInput" style="width: 50px" value="8" />
					
					
					
				</p>
				<p>
						Manual Setpoint:&nbsp <input type="text" id="setpoint" class="valueInput" style="width: 50px" value="200" />
						&nbsp&nbsp
						<button id="set" class="actionButton">Set</button>
				</p>
				<p>
					
					<button id="inc" class="actionButton">Inc</button>
					<input type="text" id="inc-sp" class="valueInput" style="width: 50px" value="10" />
					&nbsp&nbsp
					<button id="dec" class="actionButton">Dec</button>
					<input type="text" id="dec-sp" class="valueInput" style="width: 50px" value="10" />
				</p>
				<br>
				<p>
					<button id="apply" class="actionButton">Apply parameters</button>
					&nbsp&nbsp&nbsp&nbsp
					<button id="save" class="actionButton">Save to EEPROM</button>
				</p>
			</div>
		</div>
	</div>
		
		

</div>

<div class="row">
	<div class="col-md-6">
		<button id="start">Start PID Tuner</button>
		&nbsp
		<button id="shutdown" class="actionButton">Shut Down</button>
			
	</div>

</div>
	