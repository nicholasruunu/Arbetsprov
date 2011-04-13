<?php

class Spark {

    function __construct($data) {
        $this->data = $data;
        $this->name = $this->data->name;
        $this->spark_id = $this->data->id;
        $this->version = property_exists($this->data, 'version') ? $this->data->version : null;
        $this->base_location = $this->data->base_location;

        // used internally
        $this->temp_token = 'spark-' . $this->spark_id . '-' . time();
        $this->temp_path = sys_get_temp_dir() . '/' . $this->temp_token;
    }

    final function installed_path() {
        return $this->installed_path;
    }

    function location_detail() { }
    function retrieve() { }

    function install() {
        @mkdir(SPARK_PATH); // Two steps for windows
        @mkdir(SPARK_PATH . "/$this->name");
        $success = $this->copyr($this->temp_path, $this->installation_path);
        if ($success) {
			$this->installed_path = $this->installation_path;
			@unlink($this->temp_path);
		} else {
			die('Failed to move the spark into the destination folder');
		}
    }

    function verify() {
        // see if this is deactivated
        if ($this->data->is_deactivated) {
            $msg = 'Woah there - it seems the spark you want has been deactivated';
            if ($this->data->spark_home) $msg .= "\nLook for different versions at: " . $this->data->spark_home;
            throw new SparkException($msg);
        }
        // see if this is unsupported
        if ($this->data->is_unsupported) {
            SparkUtils::warning('This spark is no longer supported.');
            SparkUtils::warning('You can keep using it, or look for an alternate');
        }
        // tell the user if its already installed and throw an error
        $this->installation_path = SPARK_PATH . "/$this->name/$this->version";
        if (is_dir($this->installation_path)) {
            throw new SparkException("Already installed.  Try `php tools/spark remove $this->name`");
        }
    }

	function copyr($source, $dest)
	{
		// Simple copy for a file
		if (is_file($source)) return copy($source, $dest);

		// Make destination directory
		is_dir($dest) || @mkdir($dest);

		// Loop through the folder
		$dir = dir($source);
		while (false !== $entry = $dir->read()) {

			// Skip pointers
			if ($entry == "." || $entry == "..") {
				continue;
			}

			// Deep copy directories
			if (is_dir("$source/$entry") && ($dest !== "$source/$entry")) {
				$this->copyr("$source/$entry", "$dest/$entry");
			} else {
				copy("$source/$entry", "$dest/$entry");
			}
		}

		// Clean up
		$dir->close();
		return true;
	}
}
