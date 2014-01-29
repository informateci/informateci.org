<?php

$this->result = array();
$file_name = $this->getCachFileName('Bans');


if( $this->queryStr=="SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}bans WHERE (banneduserid=? OR ip=?) AND roomid IS NULL" )
{
	
	$handle = @fopen($file_name, "r");
	$total = '';
	$allUsers = array();
	while (!feof($handle))
	{
    	$buffer = fgets($handle);
		$array = explode("\t",$buffer);
		
		if( ($array[2]==$params[0] || $array[4]==$params[1]) && $array[3]==$params[2] )
		{
			$array['created'] = $array[0];
			$array['userid'] = $array[1];
			$array['banneduserid'] = $array[2];
			$array['roomid'] = $array[3];
			$array['ip'] = $array[4];
			unset($array[0]);
			unset($array[1]);
			unset($array[2]);
			unset($array[3]);
			unset($array[4]);
			$allUsers[] = $array;
		}
	}
	@fclose($handle);			
	//return $allUsers;
	return new ResultSet1( $allUsers );
}
elseif( strpos($this->queryStr, 'INSERT INTO')!==FALSE )
{
	
	if( $file_name == null )
	{
		$cacheDir = $this->getCachDir();
		$cachePath = $cacheDir->path;
		$file_name = $cachePath.$GLOBALS['fc_config']['db']['pref'].'bans_'.$GLOBALS['fc_config']['cacheFilePrefix'].'.txt';
	}
	$file = @fopen($file_name,'a');
	$str = date("Y-m-d H:i:s")."\t".$params[0]."\t".$params[1]."\t".$params[2]."\t".$params[3]."\t".$params[4]."\t\n";
	
	@fwrite($file, $str);
	fflush($file);
	@fclose($file);
	$this->result = array();
	return true;
}
elseif( $this->queryStr=="SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}bans WHERE banneduserid=? AND roomid=?" )
{
	
	$handle = @fopen($file_name, "r");
	$total = '';
	$allUsers = array();
	while (!feof($handle))
	{
    	$buffer = fgets($handle);
		if( $buffer=='' )
			continue;
		
		$array = explode("\t",$buffer);
		
		
		if( $array[2]==$params[0] && $array[3]==$params[1] )
		{
			$array['created'] = $array[0];
			$array['userid'] = $array[1];
			$array['banneduserid'] = $array[2];
			$array['roomid'] = $array[3];
			$array['ip'] = $array[4];
		
			unset($array[0]);
			unset($array[1]);
			unset($array[2]);
			unset($array[3]);
			unset($array[4]);
		
			$allUsers[] = $array;
		}
		
			
	}
		
	@fclose($handle);
	return new ResultSet1( $allUsers );
}
elseif( $this->queryStr=="SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}bans ORDER BY userid" )
{
	
	$handle = @fopen($file_name, "r");
	$total = '';
	$allUsers = array();
	while (!feof($handle))
	{
    	$buffer = fgets($handle);
		if( $buffer=='' )
			continue;
			
		$array = explode("\t",$buffer);
		$array['created'] = $array[0];
		$array['userid'] = $array[1];
		$array['banneduserid'] = $array[2];
		$array['roomid'] = $array[3];
		$array['ip'] = $array[4];				
		unset($array[0]);unset($array[1]);unset($array[2]);	unset($array[3]);unset($array[4]);		
		$allUsers[] = $array;				
	}
		
	@fclose($handle);
	return new ResultSet1($allUsers);
}
elseif( $this->queryStr=="SELECT roomid FROM {$GLOBALS['fc_config']['db']['pref']}bans WHERE banneduserid=?" )
{
	$handle = @fopen($file_name, "r");
	$total = '';
	$allUsers = array();
	while (!feof($handle))
	{
    	$buffer = fgets($handle);
		$array = explode("\t",$buffer);
		
		if( $array[2]==$params[0] )
		{
			$array['roomid'] = $array[3];
			unset($array[0]);
			unset($array[1]);
			unset($array[2]);
			unset($array[3]);
			unset($array[4]);
			$allUsers[] = $array;
		}
	}
	@fclose($handle);
	return new ResultSet1( $allUsers );
}//
elseif( $this->queryStr=="DELETE FROM {$GLOBALS['fc_config']['db']['pref']}bans WHERE banneduserid=? AND roomid IS NOT NULL" )
{
	//$file_name = $this->getCachFileName('Ignors');			
	$handle = @fopen($file_name, "r");
	$total = '';
	$allUsers = array();
	while (!feof($handle))
	{
    	$buffer = fgets($handle);
		$array = explode("\t",$buffer);
		if( $buffer=='' )
			continue;
		
		if( $array[3]!='' && (int)$array[2]==(int)$params[0] )// && $array[2]!=$params[1] 
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
elseif( $this->queryStr=="SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}bans WHERE banneduserid=?" )
{
	$handle = @fopen($file_name, "r");
	$total = '';
	$allUsers = array();
	while (!feof($handle))
	{
    	$buffer = fgets($handle);
		if( $buffer=='' )
			continue;
		
		$array = explode("\t",$buffer);
		
		if( $array[2]==$params[0] )	
		{
			$array = explode("\t",$buffer);
			$array['created'] = $array[0];
			$array['userid'] = $array[1];
			$array['banneduserid'] = $array[2];
			$array['roomid'] = $array[3];
			$array['ip'] = $array[4];				
			unset($array[0]);unset($array[1]);unset($array[2]);	unset($array[3]);unset($array[4]);		
			$allUsers[] = $array;				
		}
	}
		
	@fclose($handle);
	return new ResultSet1($allUsers);
}
?>