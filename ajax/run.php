<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/utilities.php';


/** CREATE LOG FILES */
$_time                      = $_POST['time'];
$PYTHON_PATH = "/var/www/fabui/application/plugins/autotune/assets/python/";
$TEMP_PATH = "/var/www/temp/";



/** LOAD DB */
$db    = new Database();

/** ADD TASK */
$_task_data['controller'] = 'autotune';
$_task_data['type']       = 'PIDtune';
$_task_data['status']     = 'running';
$_task_data['attributes'] = array();
$_task_data['start_date'] = 'now()';
$_task_data['user']       = $_SESSION['user']['id'];

/** ADD TASK RECORD TO DB */
$id_task = $db->insert('sys_tasks', $_task_data);


//call socket
shell_exec('sudo php '.SCRIPT_PATH.'/notifications.php &');




/** EXEC COMMAND */

$_command = 'sudo python '.$PYTHON_PATH.'pidtune.py ' . $id_task . ' > /dev/null & echo $!' ;
$_output_command = shell_exec($_command); 
$_pid      = intval(trim(str_replace('\n', '', $_output_command)));


/** UPDATE TASKS ATTRIBUTES */
$_attributes_items['pid']         =  $_pid;
//$_attributes_items['console_file']     =  $_destination_console;
// $_attributes_items['monitor']     =  $_monitor_file;
// $_attributes_items['uri_monitor'] =  $_uri_monitor;
// $_attributes_items['folder']      =  $_destination_folder;

$_data_update['attributes']= json_encode($_attributes_items);
/** UPDATE TASK INFO TO DB */
$db->update('sys_tasks', array('column' => 'id', 'value' => $id_task, 'sign' => '='), $_data_update);
$db->close();



$_response = $_pid ;

echo $_response; 