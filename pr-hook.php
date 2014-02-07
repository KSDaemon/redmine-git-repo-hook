<?php

	$ini_array = parse_ini_file("config.ini", true);

	if($ini_array === FALSE)
		die('Cannot parse config file!');

	if(!isset($ini_array['base']) || !isset($ini_array['commands'])) {
		die('Not all required sections found in config file!');
	}

	try {

		if($ini_array['base']['log_script'] && $ini_array['base']['log_file']) {
			$logfile = fopen($ini_array['base']['log_file'], 'a');

			if ($logfile === FALSE) {
				throw new Exception("Cannot open log file: " . $ini_array['base']['log_file']);
			}
		}

		if($data = json_decode(file_get_contents('php://input'), true)) {
			$repo = $data['repository']['name'];

			if (array_key_exists($repo, $ini_array)) {	// there is specific configuration for this repo
				$cmd = 'cd ' . $ini_array[$repo]['git_repos_dir'] ;
			}
			else {	// use default configuration
				$cmd = 'cd ' . $ini_array['base']['git_repos_dir'] . '/' . $repo;
			}

			array_unshift($ini_array['commands']['cmds'], $cmd);

			if($ini_array['base']['log_script']) {
				$res_cmd = "/bin/sh -c 'exec 2>&1 && " . implode(" && ", $ini_array['commands']['cmds']) . "'";
				$output = array();

				exec($res_cmd, $output, $res);

				fwrite($logfile, date('Y-m-d H:i:s') . " CMD: " . $res_cmd . "\n");
				fwrite($logfile, date('Y-m-d H:i:s') . " OUTPUT: " . implode("\n", $output) . "\n");
			} else {
				$res_cmd = "/bin/sh -c 'setsid /bin/sh -c \"" . implode(" && ", $ini_array['commands']['cmds']) . "\" < /dev/null 2>/dev/null >/dev/null &'";

				exec($res_cmd);
			}
		}
		else {
			throw new Exception("Cannot json_decode the HTTP_RAW_POST_DATA");
		}
	} catch (Exception $e) {

		if($ini_array['base']['log_script']) {
			fwrite($logfile, date('Y-m-d H:i:s') . ": " . $e . "\n");
		}

		error_log( sprintf( "%s: %s", date('Y-m-d H:i:s'), $e ) );
	}

	if ($logfile) {
		fclose($logfile);
	}

?>
