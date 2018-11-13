<?php
if(!empty($_REQUEST['path']))
{
	$dir = "../../mhc/{$_REQUEST['path']}";
	if(file_exists($dir))
	{
		// echo '<script type="text/javascript">';
		echo file_get_contents($dir);
		// echo '</script>';
	}
}
