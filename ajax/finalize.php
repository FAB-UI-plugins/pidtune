<?php
require_once '/var/www/lib/config.php';
require_once '/var/www/lib/database.php';
require_once '/var/www/lib/utilities.php';
//require_once '/var/www/lib/log4php/Logger.php';


/* INIT LOG **/
//Logger::configure(FABUI_PATH.'config/log_fabui_config.xml');
//$log = Logger::getLogger('finalize');
//$log->info('=====================================================');

/** GET ARGS FROM COMMAND LINE */
$_task_id       = $argv[1];
$_type          = $argv[2];
$_status        = isset($argv[3]) && $argv[3] != '' ? $argv[3] : 'performed';
//$_g_pusher_type = isset($argv[4]) && $argv[4] != '' ? $argv[4] : 'fast';




switch($_type){

	case 'PIDtune':
		finalize_general($_task_id, $_type, $_status);
		break;	
	default:
		finalize_general($_task_id, $_type, $_status);
		
}


//$log->info('=====================================================');

/** UPDATE TASK ON DB 
 * 
 * @param $tid: TASK ID
 * @param $status - TASK STATUS (STOPPED - PERFORMED)
 * 
 ***/
function update_task($tid, $status){
	//global $log;
	
	//LOAD DB
	$db = new Database();
	
	$_data_update = array();
	$_data_update['status']      = $status;
	$_data_update['finish_date'] = 'now()';
	
	$db->update('sys_tasks', array('column' => 'id', 'value' => $tid, 'sign' => '='), $_data_update);
	$db->close();
	
	shell_exec('sudo php '.SCRIPT_PATH.'/notifications.php &');
	//$log->info('Task #'.$tid.' updated. New status: '.$status);
	
}




function finalize_general($tid,$type,$status){
	
	//global $log;
	
	//$log->info('Task #'.$tid.' '.$type.' '.$status);
	//$log->info('Task #'.$tid.' start finalizing');
	
	
	//LOAD DB
	$db = new Database();
	//GET TASK
	$task = $db->query('select * from sys_tasks where id='.$tid);
	
	//GET TASK ATTRIBUTES
	$attributes = json_decode($task['attributes'], TRUE);
	$db->close();
	
	//UPDATE TASK
	update_task($tid, $status);
// 	sleep(10);
// 	//REMOVE ALL TEMPORARY FILES
// 	shell_exec('sudo rm -rf '.$attributes['folder']);
// 	//$log->info('Task #'.$tid.' end finalizing');
	
}



///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////




?>