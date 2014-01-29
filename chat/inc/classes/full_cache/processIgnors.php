<?php


$this->result = array();
if( $this->queryStr=="INSERT INTO {$GLOBALS['fc_config']['db']['pref']}ignors (created, userid, ignoreduserid) VALUES (NOW(), ?, ?)" )
{
	$file_name = $this->getCachFileName('Ignors');
	if( $file_name == null )
	{
		$cacheDir = $this->getCachDir();
		$cachePath = $cacheDir->path;
		$file_name = $cachePath.$GLOBALS['fc_config']['db']['pref'].'ignors_'.$GLOBALS['fc_config']['cacheFilePrefix'].'.txt';
	}
	
	$file = @fopen($file_name,'a');
	$str = date("Y-m-d H:i:s")."\t".$params[0]."\t".$params[1]."\t\n";
	@fwrite($file, $str);
	fflush($file);
	@fclose($file);
	return true;
	
	//return $this->insertIgnors( $params );
}//SELECT * FROM flashchat_ignors WHERE userid=? AND ignoreduserid=? 
elseif( $this->queryStr=="SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}ignors WHERE userid=? AND ignoreduserid=?" )
{
	$file_name = $this->getCachFileName('Ignors');
	$handle = @fopen($file_name, "r");
	$total = '';
	$allUsers = array();
	while (!feof($handle))
	{
    	$buffer = fgets($handle);
		$array = explode("\t",$buffer);
		
		if( $buffer=='' )
			continue;
			
		if( $array[1]==$params[0] && $array[2]==$params[1])
		{
			$array['userid'] = $array[1];
			$array['created'] = $array[0];
			$array['ignoreduserid'] = $array[2];
			unset($array[0]);unset($array[1]);unset($array[2]);
			$allUsers[] = $array;
		}		
	}
	fclose($handle);
	return new ResultSet1($allUsers);
}
elseif( $this->queryStr=="SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}ignors WHERE ignoreduserid=?" )
{
	$file_name = $this->getCachFileName('Ignors');
	$handle = @fopen($file_name, "r");
	$total = '';
	$allUsers = array();
	while (!feof($handle))
	{
    	$buffer = fgets($handle);
		
		if( $buffer=='' )
			continue;
		$array = explode("\t",$buffer);
		
		if( $array[2]==$params[1])
		{
			$array['userid'] = $array[1];
			$array['created'] = $array[0];
			$array['ignoreduserid'] = $array[2];
			unset($array[0]);unset($array[1]);unset($array[2]);
			$allUsers[] = $array;
		}
	}
	fclose($handle);
	return new ResultSet1($allUsers);
}
elseif( $this->queryStr=="SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}ignors ORDER BY userid" )
{
	$file_name = $this->getCachFileName('Ignors');
	$handle = @fopen($file_name, "r");
	$total = '';
	$allUsers = array();
	while (!feof($handle))
	{
    	$buffer = fgets($handle);
		$array = explode("\t",$buffer);
		
		if( $buffer=='' )
			continue;
		
		
		$array['userid'] = $array[1];
		$array['created'] = $array[0];
		$array['ignoreduserid'] = $array[2];
		unset($array[0]);unset($array[1]);unset($array[2]);
		$allUsers[] = $array;				
	}
	fclose($handle);
	return new ResultSet1($allUsers);
}
elseif( $this->queryStr=="SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}ignors WHERE userid=?" )
{
	$file_name = $this->getCachFileName('Ignors');
	$handle = @fopen($file_name, "r");
	$total = '';
	$allUsers = array();
	while (!feof($handle))
	{
    	$buffer = fgets($handle);
		
		$array = explode("\t",$buffer);
		
		if( $buffer=='' )
			continue;
		
		if( $array[1]==$params[0] )
		{
			$array['userid'] = $array[1];
			$array['created'] = $array[0];
			$array['ignoreduserid'] = $array[2];
			unset($array[0]);unset($array[1]);unset($array[2]);
			$allUsers[] = $array;
		}
		
		
	}
	fclose($handle);
	return new ResultSet1($allUsers);
}
elseif( $this->queryStr=="DELETE FROM flashchat_ignors WHERE userid=? AND ignoreduserid=?" )
{
	$file_name = $this->getCachFileName('Ignors');			
	$handle = @fopen($file_name, "r");
	$total = '';
	$allUsers = array();
	while (!feof($handle))
	{
    	$buffer = fgets($handle);
		$array = explode("\t",$buffer);
		if( $buffer=='' )
			continue;
		
		if( (int)$array[1]==(int)$params[0] && (int)$array[2]==(int)$params[1] )// && $array[2]!=$params[1] 
			continue;
		$total .= $buffer;			
	}
	@fclose($handle);
	$handle = fopen($file_name, "w");
	fwrite($handle,$total);
	fflush($handle);
	fclose($handle);
	return true;
}

?>