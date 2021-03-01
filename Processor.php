<?php 
/**
 * @version     0.0.1
 * @package     Processor.php
 * @copyright   Copyright (C) 2021 Zoran Tanevski. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Zoran Tanevski <zoran@tanevski.com> - http://tanevski.com
 */

require __DIR__ . '/vendor/autoload.php';

use Cocur\BackgroundProcess\BackgroundProcess;


class Processor {

	protected $processes_file;

	public function __construct(){

		//Path to the processes text file to keep track of them
		$this->processes_file = __DIR__.'/processes.txt';

		if( !file_exists( $this->processes_file ) ) {
			touch( $this->processes_file );
		}
		
	}

	/**
	 * Run a command
	 */
	public function run( $cmd = null ) {
		
		if(! $cmd ) {
			die('No command was passed to the run function. Exiting.');
		}

		$process = new BackgroundProcess( $cmd );
		$process->run();

		$pid = $process->getPid();

		if( !$pid ) {
			die('Process could not be started. Exiting.');
		}

		$added = $this->addPidToArray( $pid );

		if(! $added ) {
			$this->stop( $pid );
			die('Process could not saved in the '.$this->processes_file.' file . Exiting.');
		}

		return $pid;

	}

	public function isRunning($pid) {
		if($pid){
			$process = BackgroundProcess::createFromPID( (int) $pid );
			if( $process->isRunning() ) {
				return true;
			}
		}
		return false;
	}

	public function addPidToArray($pid){

		if(! file_exists($this->processes_file) ) {
			die('Processes file could not be found. Exiting');
		}

		$content = file_get_contents($this->processes_file);

		if( trim($content) == '' ) {
			$processes = [];
		} else {
			$processes = json_decode($content, true);
		}
		$processes[] = $pid;
		$processes = array_unique($processes);
		$to_write = json_encode($processes);
		$written = file_put_contents($this->processes_file, $to_write);
			
		return $written !== false;

	}

	/**
	 * Stops a process by PID
	 */
	public function stop( $pid ) {

		if( $pid ){
			$process = BackgroundProcess::createFromPID( (int) $pid );
			if( $process->isRunning() ) {
				$process->stop();
				$this->removeFromProcesses( $pid );
				return true;
			}
		}
		return false;
	}

	/**
	 * Stops all the processes started and stored to the text file
	 */
	public function stopAll() {

		if(! file_exists( $this->processes_file ) ){
			die('Processes file could not be found. Exiting');
		}

		$content = file_get_contents($this->processes_file);

		if( trim( $content ) == '' ) {
			die('Processes file is empty. Nothing to do here.');
		}

		$processes = json_decode( $content, true );

		foreach( $processes as $pid ) {
			$this->stop( $pid );
		}

		return true;

	}

	public function removeFromProcesses($pid) {

		if(! file_exists( $this->processes_file ) ){
			die('Processes file could not be found. Exiting');
		}

		$content = file_get_contents( $this->processes_file );

		if( trim($content) == '' ) {
			die('Processes file is empty. Nothing to do here.');
		}

		$processes = json_decode($content, true);
		$key = array_search($pid, $processes);

		if($key === false) {
			die('Process ID could not be find in the processes file.');
		}

		if(isset($processes[$key])){

			unset($processes[$key]);

			$to_write = json_encode($processes);
			$written = file_put_contents($this->processes_file, $to_write);
				
			return $written !== false;

		}

	}


	public function checkAll() {

		if(! file_exists($this->processes_file)){
			die('Processes file could not be found. Exiting');
		}

		$out = [];

		$content = file_get_contents($this->processes_file);
		if( empty($content) ) {
			$content = '[]';
		}
		$processes = json_decode($content);

		foreach($processes as $pid) {

			$p = new stdClass();
			$p->pid = $pid;

			if( $this->isRunning($pid) ) {
				$p->is_running = 1;
			} else {
				$p->is_running = 0;
				//Remove it from array
				$this->removeFromProcesses($pid);
			}
			$out[] = $p;
		}

		return $out;

	}
			

}

