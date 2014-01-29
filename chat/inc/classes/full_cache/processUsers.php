<?php


$this->result = array();			
if( $this->queryStr == "SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}users WHERE login=? LIMIT 1" )
{
	return new ResultSet1($this->processUser('*','login',$params));
}
elseif( $this->queryStr == "SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}users WHERE login=?" )
{
	return new ResultSet1($this->processUser('*1','login',$params));
}
elseif( $this->queryStr == "SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}users WHERE id=? LIMIT 1" )
{
	return new ResultSet1($this->processUser('*','id',$params));
}//
elseif( $this->queryStr == "SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}users WHERE login=? AND id<>? LIMIT 1" )
{
	return new ResultSet1($this->processUser('*','login,id',$params));
}//SELECT * FROM flashchat_users LIMIT 1 
elseif( $this->queryStr == "SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}users" )
{
	return new ResultSet1($this->processUser('*','',$params));
}//
elseif( $this->queryStr == "SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}users LIMIT 1" )
{
	return new ResultSet1($this->processUser('*','',$params));
}
elseif( $this->queryStr == "SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}users WHERE id=?" )
{
	return new ResultSet1($this->processUser('+','id',$params));
}
elseif( strpos($this->queryStr,"profile <> ''") )
{
	$file_name = $this->getCachFileName('Users');
	$handle = @fopen($file_name, "r");
	$total = '';
	$allUsers = array();
	$tempArray = array();
	while (!feof($handle))
	{
    	$buffer = fgets($handle);
		
		if( $buffer=='' )
			continue;
		
		$array = explode("\t",$buffer);
		if( $array[4]=='' )
			continue;
		$tempArray['id'] = $array[0];
		$tempArray['login'] = $array[1];
		$tempArray['password'] = $array[2];
		$tempArray['roles'] = $array[3];
		$tempArray['profile'] = $array[4];
		
		$allUsers[] = $tempArray;
	}
	fclose($handle);
	//return $allUsers;
	
	
	return new ResultSet1( $allUsers );
}
elseif( $this->queryStr == "SELECT profile FROM {$GLOBALS['fc_config']['db']['pref']}users WHERE id=?" )
{
	$file_name = $this->getCachFileName('Users');
	$handle = @fopen($file_name, "r");
	$total = '';
	$allUsers = array();
	while (!feof($handle))
	{
    	$buffer = fgets($handle);
		$array = explode("\t",$buffer);
		
		if( $array[0]==$params[0] )
		{
			$allUsers[0]['profile'] = $array[4];
		}
	}
	fclose($handle);
	//return $allUsers;	
	
	return new ResultSet1( $allUsers );
}
elseif( $this->queryStr == "SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}users ORDER BY login" )
{
	$file_name = $this->getCachFileName('Users');
	$handle = @fopen($file_name, "r");
	$total = '';
	$allUsers = array();
	$tempArray = array();
	while (!feof($handle))
	{
    	$buffer = fgets($handle);
				
		if( $buffer=='' )
			continue;
		
		$array = explode("\t",$buffer);
		
		$tempArray['id'] = $array[0];
		$tempArray['login'] = $array[1];
		$tempArray['password'] = $array[2];
		$tempArray['roles'] = $array[3];
		$tempArray['profile'] = $array[4];
		
		$allUsers[] = $tempArray;
	}
	fclose($handle);
	//return $allUsers;
	
	return new ResultSet1( $allUsers );
}
elseif( $this->queryStr == "INSERT INTO {$GLOBALS['fc_config']['db']['pref']}users (login, roles) VALUES(?, ?)" )
{
	$file_name = $this->getCachFileName('Users');
	if(($file_name = $this->getCachFileName('Users')) == null)
	{
		$cacheDir = $this->getCachDir();
		$cachePath = $cacheDir->path;
		$file_name = $cachePath.$GLOBALS['fc_config']['db']['pref'].'users_'.$GLOBALS['fc_config']['cacheFilePrefix'].'.txt';
	}
	
	$file = @fopen($file_name,'a');
	$id = $this->file_insert_id(9);
	$str = $id."\t".$params[0]."\t\t".$params[1]."\t\t\n";
	
	@fwrite($file, $str);
	fflush($file);
	@fclose($file);
	return $id;
	
	//return $this->processInsertUser($params);
}//
elseif( strpos($this->queryStr,"INSERT INTO ")!==false && strpos($this->queryStr,"password")!==false  )
{
	$file_name = $this->getCachFileName('Users');
			if(($file_name = $this->getCachFileName('Users')) == null)
			{
				$cacheDir = $this->getCachDir();
				$cachePath = $cacheDir->path;
				$file_name = $cachePath.$GLOBALS['fc_config']['db']['pref'].'users_'.$GLOBALS['fc_config']['cacheFilePrefix'].'.txt';
			}
			
			$file = @fopen($file_name,'a');
			$id = $this->file_insert_id(9);
			$str = $id."\t".$params[0]."\t".$params[1]."\t".$params[2]."\t\t\n";
			
			@fwrite($file, $str);
			fflush($file);
			@fclose($file);
			return $id;
	
	//return $this->processInsertUserPass($params);
}
elseif( strpos($this->queryStr,"SELECT count(*) users_amount")!==false )
{
	$file_name = $this->getCachFileName('Users');
			$handle = @fopen($file_name, "r");
			$total = '';
			$allUsers = array();
			$tempArray = array();
			$count = 0;
			while (!feof($handle))
			{
    			$buffer = fgets($handle);
				
				if( $buffer=='' )
					continue;
				
				$array = explode("\t",$buffer);
				if( $array[4]!='' )
					$count++;
				
			}
			fclose($handle);
			$allUsers[0]['users_amount'] = $count;
			
			return $allUsers;
	
	return new ResultSet1( $allUsers );
}//UPDATE flashchat_users SET `password`=MD5(?) WHERE login=? LIMIT 1 
elseif( $this->queryStr == "UPDATE {$GLOBALS['fc_config']['db']['pref']}users SET `password`=MD5(?) WHERE login=? LIMIT 1" )
{
	$file_name = $this->getCachFileName('Users');
			$handle = @fopen($file_name, "r");
			$total = '';
			$allUsers = array();
			while (!feof($handle))
			{
    			$buffer = fgets($handle);
				
				if( $buffer=='' )
					continue;
				
				$array = explode("\t",$buffer);
				
				if( $array[1]==$params[1] )
				{
					$total .= $array[0]."\t".$array[1]."\t".md5($params[0])."\t".$array[3]."\t".$array[4]."\t\n";
				}
				else
					$total .= $buffer;
						
				
			}
			
			@fclose($handle);
			$file = fopen($file_name, "w");
			@fwrite($file, $total);
			fflush($file);
			@fclose($file);
			return true;
	
	//return $this->processUpdateProf11($params);
}
elseif( $this->queryStr == "UPDATE {$GLOBALS['fc_config']['db']['pref']}users SET login=?, roles=? WHERE id=?" )
{
	$file_name = $this->getCachFileName('Users');
			$handle = @fopen($file_name, "r");
			$total = '';
			$allUsers = array();
			while (!feof($handle))
			{
    			$buffer = fgets($handle);
				
				if( $buffer=='' )
					continue;
				
				$array = explode("\t",$buffer);
				
				if( $array[0]==$params[2] )
				{
					$total .= $array[0]."\t".$params[0]."\t".$array[2]."\t".$params[1]."\t".$array[4]."\t\n";
				}
				else
					$total .= $buffer;
						
				
			}
			
			@fclose($handle);
			$file = fopen($file_name, "w");
			@fwrite($file, $total);
			fflush($file);
			@fclose($file);
			return true;
	
	//return $this->processUpdateProf11($params);
}
elseif( $this->queryStr == "UPDATE {$GLOBALS['fc_config']['db']['pref']}users SET login=?, password=?, roles=? WHERE id=?" )
{
	$file_name = $this->getCachFileName('Users');
			$handle = @fopen($file_name, "r");
			$total = '';
			$allUsers = array();
			while (!feof($handle))
			{
    			$buffer = fgets($handle);
				
				if( $buffer=='' )
					continue;
				
				$array = explode("\t",$buffer);
				
				if( $array[0]==$params[3] )
				{
					$total .= $array[0]."\t".$params[0]."\t".$params[1]."\t".$params[2]."\t".$array[4]."\t\n";
				}
				else
					$total .= $buffer;
						
				
			}
			
			@fclose($handle);
			$file = fopen($file_name, "w");
			@fwrite($file, $total);
			fflush($file);
			@fclose($file);
			return true;
	
	//return $this->processUpdateProfAll($params);
}
elseif( $this->queryStr == "UPDATE {$GLOBALS['fc_config']['db']['pref']}users SET profile=? WHERE id=?" )
{
	$file_name = $this->getCachFileName('Users');
			$handle = @fopen($file_name, "r");
			$total = '';
			$allUsers = array();
			while (!feof($handle))
			{
    			$buffer = fgets($handle);
				$array = explode("\t",$buffer);
				
				if( $array[0]==$params[1] )
				{
					$total .= $array[0]."\t".$array[1]."\t".$array[2]."\t".$array[3]."\t".$params[0]."\t\n";
				}
				else
					$total .= $buffer;
						
				
			}
			
			@fclose($handle);
			$file = fopen($file_name, "w");
			@fwrite($file, $total);
			fflush($file);
			@fclose($file);
			return true;
	
	//return $this->processUpdateProf($params);
}
elseif( $this->queryStr == "DELETE FROM {$GLOBALS['fc_config']['db']['pref']}users WHERE id=?" )
{
	$file_name = $this->getCachFileName('Users');
			$handle = @fopen($file_name,'r');
			$str = "";
			while (!feof($handle))
			{
    			$buffer = fgets($handle);
				if( $buffer=='' )
					continue;
				$array = explode("\t",$buffer);
				if( $array[0]!=$params[0] )
					$str .= $buffer;
			}
			@fclose($handle);
			$file = @fopen($file_name,'w');
			@fwrite($file, $str);
			@fflush($file);
			@fclose($file);
			return true;
	
	//return $this->processDelUser($params);
}
else
{
	return null;
}
		

?>