<?php

function getHostInfo(array $extra = NULL)
{
    if (!file_exists("host.ini")) {
	die("Error: host.ini file is missing.");
    }

    $machine = parse_ini_file("host.ini",$process_sections=true);
    if ($extra != NULL)
    {
        foreach ($extra as $ini)
        {
		if (file_exists($ini)) {

	            $parsed = parse_ini_file($ini,true);
        	    if ($parsed)
            {
                $machine = array_merge($machine,$parsed);
            }
         }
      }
    } 

    return $machine;
}

?>
