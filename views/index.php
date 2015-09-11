
<div class="row">

	<div class="col-md-6 graph-container" style="height: 450px">
		<h5 class="text-center"><i class="fab-lg fab-fw icon-fab-term "></i> Nozzle (<span class="nozzle-temperature"></span>)</h5>
		<div id="nozzle-chart" class="nozzle-graph"></div>
	</div>
		
	<div class="col-sm-6">
		<div class="form-group">
			<div class="col-md-12">
				<p>	
					<h5>
						Bias: <span class="text-info">0</span>
					</h5>
				</p>
				<p>		
					<h5>
						Min: <span class="text-info">34&deg;C</span>
					</h5>
				</p>
				<p>		
					<h5>
						Max: <span class="text-info">45&deg;C</span>
					</h5>
				</p>
				<p>
					<h5>
						Ku: <span class="text-info">345</span>
					</h5>
				</p>
				<p>
					<h5>
						Tu: <span class="text-info">345</span>
					</h5>
				</p>
				<p>
					<h5>
						Kp: <input type="text" id="Kp" name="Kp" style="width: 50px" value="10.5" />
						&nbsp&nbsp
						Ki: <input type="text" id="Ki" name="Ki" style="width: 50px" value="10.5" />
						&nbsp&nbsp
						Kd: <input type="text" id="Kd" name="Kd" style="width: 50px" value="10.5" />
					</h5>
				</p>
				<p>
					<form>
						<h5>
							<input type="radio" name="pid-type" checked="checked"> Classic PID
							<br>
							<input type="radio" name="pid-type"> Pessen Integral Rule
							<br>
							<input type="radio" name="pid-type"> Some Overshoot
							<br>
							<input type="radio" name="pid-type"> No Overshoot
							<br>
						</h5>
					</form>
					
				</p>
				<p>
					<button id="auto">Start Autotuning</button>
					&nbsp
					Extruder: <input type="text" id="extrudedr" style="width: 50px" value="0" />
					Target Temp: <input type="text" id="extrudedr" style="width: 50px" value="200" />
					Cycles: <input type="text" id="extrudedr" style="width: 50px" value="8" />
					
					
					
				</p>
				<p>
						<button id="manual">Start Manual Tuning</button>
						&nbsp
						Manual Setpoint:&nbsp <input type="text" id="setpoint" style="width: 50px" value="200" />
						&nbsp&nbsp
						<button id="set">Set</button>
				</p>
				<p>
					
					<button id="inc">Inc</button>
					<input type="text" id="inc-sp" style="width: 50px" value="10" />
					&nbsp&nbsp
					<button id="dec">Dec</button>
					<input type="text" id="dec-sp" style="width: 50px" value="10" />
				</p>
				<br>
				<p>
					<button id="apply">Apply parameters</button>
					&nbsp&nbsp&nbsp&nbsp
					<button id="save">Save to EEPROM</button>
				</p>
			</div>
		</div>
	</div>
		
		

</div>

<div class="row">
	<div class="col-md-6>
			
	</div>

</div>
	