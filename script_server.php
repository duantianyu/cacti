#!/usr/bin/php -q
<?php
/*
 +-------------------------------------------------------------------------+
 | Copyright (C) 2004 Ian Berry                                            |
 |                                                                         |
 | This program is free software; you can redistribute it and/or           |
 | modify it under the terms of the GNU General Public License             |
 | as published by the Free Software Foundation; either version 2          |
 | of the License, or (at your option) any later version.                  |
 |                                                                         |
 | This program is distributed in the hope that it will be useful,         |
 | but WITHOUT ANY WARRANTY; without even the implied warranty of          |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the           |
 | GNU General Public License for more details.                            |
 +-------------------------------------------------------------------------+
 | cacti: a php-based graphing solution                                    |
 +-------------------------------------------------------------------------+
 | Most of this code has been designed, written and is maintained by       |
 | Ian Berry. See about.php for specific developer credit. Any questions   |
 | or comments regarding this code should be directed to:                  |
 | - iberry@raxnet.net                                                     |
 +-------------------------------------------------------------------------+
 | - raXnet - http://www.raxnet.net/                                       |
 +-------------------------------------------------------------------------+
*/
$no_http_headers = true;

/* used for includes */
include_once(dirname(__FILE__) . "/include/config.php");

if (read_config_option("log_perror") == "on") {
	log_data("PHPSERVER: Script Server has Started\n");
}
fputs(STDOUT, "PHP Script Server Started\n");

// process waits for input and then calls functions as required
while (1) {
	$in_string = fgets(STDIN,255);
	$in_string = rtrim(strtr(strtr($in_string,'\r',''),'\n',''));
	if (strlen($in_string)>0) {
		if (read_config_option("log_perror") == "on") {
			log_data("PHPSERVER: CMD:" . $in_string . "\n");
		}

		if (($in_string != "quit") && ($in_string != "")) {
			// get file to be included
			$inc = substr($in_string,0,strpos($in_string," "));
			$remainder = substr($in_string,strpos($in_string," ")+1);

			// parse function from command
			$cmd = substr($remainder,0,strpos($remainder," "));

			// parse parameters from remainder of command
			$preparm = substr($remainder,strpos($remainder," ")+1);
			$parm = explode(" ",$preparm);

			// check for existance of function.  If exists call it
			if ($cmd != "") {
				if (!function_exists($cmd)) {
					if (file_exists($inc)) {
				log_data($inc . "\n");
						include_once($inc);
				log_data("hello\n");
					} elseif (read_config_option("log_perror") == "on") {
						log_data("ERROR: PHP Script File to be included, does not exist\n");
					}
				}
			} elseif (read_config_option("log_perror") == "on") {
				log_data("ERROR: PHP Script Server encountered errors parsing the command\n");
			}
				log_data("hello\n");
			if (function_exists($cmd)) {

				$result = call_user_func_array($cmd, $parm);
				log_data("The result was " . $result . "\n");
				if (strpos($result,"\n") != 0) {
					fputs(STDOUT, $result);
				} else {
					fputs(STDOUT, $result . "\n");
				}
			} else {
				log_data("ERROR: Function does not exist\n");
				fputs(STDOUT, "ERROR: Function does not exist\n");
			}
		}elseif ($in_string == "quit") {
			fputs(STDOUT, "PHP Script Server Shutdown request received, exiting\n");
			log_data("PHP Script Server Shutdown request received, exiting\n");
			break;
		}else {
			fputs(STDOUT, "ERROR: Problems with input\n");
		}
	}else {
		fputs(STDOUT, "ERROR: Input expected\n");
	}
}
?>