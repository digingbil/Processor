<?php 
/**
 * @version     0.0.1
 * @package     Processor.php
 * @copyright   Copyright (C) 2021 Zoran Tanevski. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Zoran Tanevski <zoran@tanevski.com> - http://tanevski.com
 */
require_once __DIR__.'/Processor.php';
$processor = new Processor();

$run_cmd = isset( $_POST['run_cmd'] );
$check_procs = isset( $_POST['check_procs'] );
$stop_pid = isset( $_POST['stop_pid'] );

if( $run_cmd ) {
	$cmd = trim( $_POST['cmd'] );

	if( empty($cmd) ) {
		exit('No CMD passed. Exiting.');
	}

	$pid = $processor->run( $cmd );

	if( $pid ) {
		header( 'Location: '.$_SERVER['PHP_SELF'].'?pid='.$pid ); 
	}
}

$procs_running = false;
if( $check_procs ) {
	$procs_running = $processor->checkAll();
}

$stopped = false;
if( $stop_pid ) {
	$pid = (int) trim( $_POST['pid'] );
	$stopped = $processor->stop( $pid );

	if( $stopped ) {
		header( 'Location: '.$_SERVER['PHP_SELF'].'?spid='.$pid ); 
	}
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Test processor</title>
	<style>
		*{margin:0;padding:0;box-sizing: border-box;}
		body{font-family: Arial, Helvetica, sans-serif; font-size:16px; color:#000; line-height:1.4; padding-top:50px;}
		.wrapper{width:1100px;max-width:90%;padding:30px;margin:0 auto;background:#e9e9e9;}
		h1{font-weight: 700;font-size: 24px;margin-bottom: 20px;}
		h2{font-weight: 700;font-size: 20px;margin-bottom: 20px;margin-top: 40px;}
		label{display: block;margin-bottom: 10px;}
		form{margin-bottom: 40px;}
		.form-row{margin-bottom: 15px;}
		.informer{margin-bottom: 40px;padding:15px;background: #c3f8ff;border: 1px solid #141414;}
		textarea, input[type=text]{width: 100%;padding:15px;}
		button{ background:#1c6eed; color: #fff; padding:8px 34px; border:none; box-shadow: none;cursor: pointer;}
		small{font-weight: 400;font-style: italic;}
	</style>
</head>
<body>

	<div class="wrapper">
		<h1>Processor.php Tester</h1>

		<hr>

		<h2>Run some command <small>(preferably some long(er) running PHP, Python, Shell etc. script)</small></h2>

		<form action="" method="post">
			<div class="form-row">
				<label for="cmd">Command to execute</label>
				<input type="text" name="cmd" id="cmd" placeholder="For example: php -f ./LongRunningPHPScript.php ">
			</div>
			<div class="form-row">
				<button name="run_cmd" type="submit">Run</button>
			</div>
		</form>
			<?php 
				$pid = $_GET['pid'] ?? null;
				if( $pid ):
			?>
			
			<div class="informer">

				Process with PID: <?php echo $pid; ?> started.

			</div>

			<?php endif; ?>

		<hr>

		<h2>Check processes running</h2>
		
		<form action="" method="post">
			<div class="form-row">
				<label for="procs">Processes</label>
				<textarea name="procs" id="procs" cols="30" rows="5">
					<?php if( $procs_running ) : ?>
						<?php foreach( $procs_running as $p ) : ?>
							PID: <?php echo $p->pid; ?> is <?php echo $p->is_running ? 'running' : 'not running'; ?>
						<?php endforeach; ?>
					<?php endif; ?>
				</textarea>
			</div>
			<div class="form-row">
				<button name="check_procs" type="submit">Check</button>
			</div>
		</form>


		<hr>

		<h2>Stop a process</h2>
		
		<form action="" method="post">
			<div class="form-row">
				<label for="pid">Stop process ID:</label>
				<input type="text" name="pid" id="pid" placeholder="Insert the PID of the process ">
			</div>
			<div class="form-row">
				<button name="stop_pid" type="submit">Stop process</button>
			</div>
		</form>

		<?php 
			$spid = $_GET['spid'] ?? null;
			if( $spid ):
		?>
		
		<div class="informer">

			Process with PID: <?php echo $spid; ?> stopped.

		</div>

		<?php endif; ?>

	</div>
	<script>
		


	</script>
</body>
</html>