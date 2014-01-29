<?php
$this->result = array();
if( strpos($this->queryStr,"SELECT id FROM {$GLOBALS['fc_config']['db']['pref']}messages WHERE command='msg' AND toroomid=? ORDER BY id DESC LIMIT")!==false )
{

	$params = array( 'toroomid'=>$params[0] );
	$stat_arr = array();
	$stats_file_name = $this->getCachFileName('Stats');
	$strDesc = 'DESC LIMIT ';
	
	$first = substr($this->queryStr,strpos($this->queryStr,$strDesc) + strlen($strDesc),-1);
	$first = (int) substr($first,0,-1);
	$first++;
	
	$stats_file = @fopen($stats_file_name, 'r');
	
	if( !$stats_file) 
	{
	    $stat_value = $this->getRecordsCount("{$GLOBALS['fc_config']['db']['pref']}messages");
		//RESTORING message_stats file
		$this->saveStatsInCache('MESSAGES_COUNT', $stat_value);
		return false;
	}
	//stream_set_timeout($stats_file, 180);
	while ($stat = fgets($stats_file)) 
	{
	    //$stat = fgets($stats_file);
	    $stat_elems = explode('=', $stat);
		if($stat_elems[0] == 'MESSAGES_COUNT')
		{
			$stat_arr['MESSAGES_COUNT'] = $stat_elems[1];
		}
	}
	@fclose($stats_file);
	
	$stat_arr['MESSAGES_COUNT'] = (int) $stat_arr['MESSAGES_COUNT'];
		
	//checking all cached files if they have messages with id's: $params["id"] .. $stat_arr["COUNT"]
	// if only one ID not found in cach files(if database have spec. command with this id),
	// this function return's false and we select all messages from files.
	$cacheDir = $this->getCachDir();
	$cachePath = $cacheDir->path;
	
	$id_end = $stat_arr['MESSAGES_COUNT'];
	$id = array();
	$result = null;
			
	while (false !== ($entry = $cacheDir->read())) 
	{
		if( $this->breakFile($entry) || strpos($entry, "{$GLOBALS['fc_config']['db']['pref']}messages_")!==FALSE )
			continue;
			
			$entry_elems = explode('_', $entry);
			
			if( $entry_elems[0]!=$params['toroomid'] || $entry_elems[0] == 'pm')	
				continue;
			
			$userid = $entry_elems[1];
			$toroomid = $entry_elems[0];
			
		$count=0;
		$handle = @fopen($cachePath.$entry, 'r');
		$tempArray = array();
		
				
		fseek($handle,$entry_elems[3]);
		//stream_set_timeout($handle, 180);
		while ($line = fgets($handle))				
		{
			//$line = fgets($handle);		
			$line_elems = explode('#', $line);
			$bit_pos = $line_elems[count($line_elems)-1];
			fseek($handle,$bit_pos);
			$line = fgets($handle);
			$line_elems = explode('#', $line);
			if( $count > $first )
				break;
		
			if( $count > 50 )
				break;
			fseek($handle,$bit_pos-20);
			
			$id[] = $line_elems[0];
			$count++;
			if( $line_elems[4]==0 )
				break;
		
		}
				
		fclose($handle);
	}
			
			
	sort($id);
	$tempAr = array();
	$tempAr[0]['id'] = $id[count($id)-$first];

//return $tempAr;
	return new ResultSet1( $tempAr );
}

if( strpos($this->queryStr,"SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}messages WHERE command='msg' AND id>=? AND id<=? AND toroomid=? ORDER BY id")!==false )
{
	
	$params['toroomid'] = $params[2];
	$params['id'] = $params[0];
	$stat_arr = array();
	$stats_file_name = $this->getCachFileName('Stats');
	$find_records = 0;
	$stats_file = @fopen($stats_file_name, 'r');
	
	if( !$stats_file) 
	{
	    $stat_value = $this->getRecordsCount("{$GLOBALS['fc_config']['db']['pref']}messages");
		//RESTORING message_stats file
		$this->saveStatsInCache('MESSAGES_COUNT', $stat_value);
		return false;
	}
			
	//stream_set_timeout($stats_file, 180);
	while ($stat = fgets($stats_file)) 
	{
	    //$stat = fgets($stats_file);
	    $stat_elems = explode('=', $stat);
		if($stat_elems[0] == 'MESSAGES_COUNT')
		{
			$stat_arr['MESSAGES_COUNT'] = $stat_elems[1];
		}
	}
	@fclose($stats_file);
	$stat_arr['MESSAGES_COUNT'] = (int) $stat_arr['MESSAGES_COUNT'];
			
	//checking all cached files if they have messages with id's: $params["id"] .. $stat_arr["COUNT"]
	// if only one ID not found in cach files(if database have spec. command with this id),
	// this function return's false and we select all messages from files.

	$cacheDir = $this->getCachDir();
	$cachePath = $cacheDir->path;
			
	$id_end = $stat_arr['MESSAGES_COUNT'];
	$id = array();
	$result = null;
		
		
		
	while (false !== ($entry = $cacheDir->read())) 
	{
		if( $this->breakFile($entry) || strpos($entry, "{$GLOBALS['fc_config']['db']['pref']}messages_")!==FALSE )
			continue;
		
		$entry_elems = explode('_', $entry);
		
		if( $entry_elems[0]!=$params['toroomid'] || $entry_elems[0] == 'pm')	
			continue;
				
		$userid = $entry_elems[1];
		$toroomid = $entry_elems[0];
		
		$count=0;
		$handle = @fopen($cachePath.$entry, 'r');
		$tempArray = array();
		
		
		$this->setFilePos($handle,$params,$entry_elems[3]);
		
			
			
		if( !$is_cmd )
		{
			//stream_set_timeout($handle, 180);
			while ($line = fgets($handle))				
			{		
				//$line = fgets($handle);
				if( $line=='' )
					continue;
				$line_elems = explode('#', $line);
				$id = (int) $line_elems[0];
				$created = $line_elems[1];
				$roomid = (int) $line_elems[2];
				
				if( $id >= $params['id'] )
				{
					$txt = $line_elems[3];
					$txt = str_replace("%$$%$", "#", $txt);
					$find_records++;
					$result_elem = array('id'=>$id, 'created'=>$created, 'toroomid'=>$params['toroomid'], 'command'=>'msg','userid'=>$userid, 'roomid'=>$roomid, 'txt'=>$txt);
					$result[count($result)] = $result_elem;
				}
			}
		}
						
		fclose($handle);
	}
			
	if( count( $result ) > 0 )
	{				
		if( !function_exists("cmp") )
		{
			function cmp($elem1, $elem2)
			{
				if($elem1['id']<$elem2['id'])
					return -1;
				elseif($elem1['id']==$elem2['id'])
					return 0;
				elseif($elem1['id']>$elem2['id'])
					return 1;
			}
		}				
		usort($result, "cmp");
		//return $result;
		return new ResultSet1( $result );
	}
	else
	{
		$result = array();
		return new ResultSet1( $result );
	}	
}
if( strpos($this->queryStr,"SELECT id FROM {$GLOBALS['fc_config']['db']['pref']}messages WHERE command='msg' AND toroomid=? AND created > DATE_SUB(NOW(),")!==false )
{

	$cacheDir = $this->getCachDir();
			$cachePath = $cacheDir->path;
			$allUsers = array();
			$arrayId1 = array();
			$arrayId = array();
			$first = 'INTERVAL';
			$second = 'MINUTE';
			$tempStr = substr($this->queryStr,strpos($this->queryStr,$first) + strlen($first),strpos($this->queryStr,$second)-(strpos($this->queryStr,$first) + strlen($first)));
			
			$min = trim($tempStr);
			
			//toLog("min",$min);
			
			while (false !== ($entry = $cacheDir->read())) 
			{
				if( $this->breakFile($entry) || strpos($entry, "{$GLOBALS['fc_config']['db']['pref']}messages_")!==FALSE )
					continue;
					
					
				
				$entry_elems = explode('_', $entry);
				
				if( $entry_elems[0] == '' )//
					continue;
					
				if( $entry_elems[0] != $params[0] )
					continue;
				if( (time()-$entry_elems[4])>($min*60) )
					continue;
					
				$handle = @fopen($cachePath.$entry, 'r');
				while (!feof( $handle ))	//strtotime($str)) 			
				{
					
					$line = fgets( $handle );
					
					//toLog("line",$line);
					
					$line_elems = explode('#', $line);	
					//toLog("strtotime",strtotime($line_elems[1]));
					if( (time()-strtotime($line_elems[1]))>($min*60) )
						continue;
									
					$id = (int) $line_elems[0];					
					$arrayId[$id] = $id;
					
				}
				fclose( $handle );				
			}	
			
			sort( $arrayId );
			
			if( count($arrayId)!=0 )		
				$arrayId1[0]['id'] = $arrayId[0];				
			else
				return null;
			//return $arrayId1;

	return new ResultSet1( $arrayId1 );
}
elseif( $this->queryStr=="DELETE FROM {$GLOBALS['fc_config']['db']['pref']}messages WHERE created < DATE_SUB(NOW(),INTERVAL ? SECOND)" )
{
	return true;
}
elseif( $this->queryStr=="INSERT INTO {$GLOBALS['fc_config']['db']['pref']}messages (created, toconnid, touserid, toroomid, command, userid, roomid, txt) VALUES (?, ?, ?, ?, ?, ?, ?, ?)" )
{

	$cacheDir = $this->getCachDir();
	$cachePath = $cacheDir->path;
	if($this->queryStr=="DELETE FROM {$GLOBALS['fc_config']['db']['pref']}messages WHERE created < DATE_SUB(NOW(),INTERVAL ? SECOND)")
	{
		return true;
	}
	else//if we INSERT new messages
	{
		$isPrivate = ($params[2]!='');
		if( $params[4]!='msg' )
		{
			if( $params[4]=='lin' )
			{
				while (false !== ($entry = $cacheDir->read())) 
				{
					if( $this->breakFile($entry) || strpos($entry, "{$GLOBALS['fc_config']['db']['pref']}messages_")!==FALSE )
						continue;
						
					//$cachePath.$entry
					$element = explode("_",$entry);
					
					if( $element[1]==$params[5] || ($element[0]=="pm" && $element[1]==$params[5]) || ((time() - filemtime($cachePath.$entry))>3600))
					{
						unlink($cachePath.$entry);
					}
				}
			}
			return $this->insertCommand( $params );
		}
		$id = $this->file_insert_id( 7 );
		//toLog("queryParams",$queryParams);
		$params = array('id'=>$id, 'touserid'=>$params[2], 'toroomid'=>$params[3], 'userid'=>$params[5], 'roomid'=>$params[6], 'txt'=>$params[7]);
		/*if( $params['roomid']=='' )
			return;*/
		
		$today = date("Y-m-d G:i");
		//$today = time();
		$appended = false;
		$greate = false;
		
		$to_add = $GLOBALS['fc_config']['cacheFilePrefix'];
		while (false !== ($entry = $cacheDir->read())) 
		{
			if( $this->breakFile($entry) || strpos($entry, "{$GLOBALS['fc_config']['db']['pref']}messages_")!==FALSE )
			continue;
						
			if(!$isPrivate)
			{
				//$file = @fopen($cachePath.$params['toroomid'].'_'.$params['userid'].'_'.$to_add.'.txt', 'w');						
				if( strpos($entry,$params['toroomid'].'_'.$params['userid'].'_') !== FALSE )
				{
					$file = @fopen($cachePath.$entry, 'a');
					$greate = true;
					$file_name = $cachePath.$entry;
				}
			}
			else
			{
				if( strpos($entry,'pm_'.$params['userid'].'_'.$params['touserid'].'_') !== FALSE )
				{
					$file = @fopen($cachePath.$entry, 'a');
					$greate = true;
					$file_name = $cachePath.$entry;
				}
			}
		}
				
		if( !$greate )
		{
			if(!$isPrivate)
			{
				$file = @fopen($cachePath.$params['toroomid'].'_'.$params['userid'].'_'.$id.'_0_'.time().'_'.$to_add.'.txt', 'w');
				$file_name = $cachePath.$params['toroomid'].'_'.$params['userid'].'_'.$id.'_0_'.time().'_'.$to_add.'.txt';
			}
			else
			{
				
				$file = @fopen($cachePath.'pm_'.$params['userid'].'_'.$params['touserid'].'_'.$id.'_0_'.$to_add.'.txt', 'w');
				$file_name = $cachePath.'pm_'.$params['userid'].'_'.$params['touserid'].'_'.$id.'_0_'.$to_add.'.txt';
			}
		}
				
		$pos = filesize($file_name);
		$params['txt'] = ereg_replace("#", "%$$%$", $params['txt']);
		$_str = $id.'#'.$today.'#'.$params['roomid'].'#'.$params['txt'].'#'.$pos."\n";
				
		@fwrite($file,$_str);
		fflush($file);
		@fclose($file);				
		$cacheDir->close();
		$lastrow = filesize($file_name)-$pos;
				
		if( $greate )
		{
			if(!$isPrivate)
			{
				rename($file_name, $cachePath.$params['toroomid'].'_'.$params['userid'].'_'.$id.'_'.$pos.'_'.time().'_'.$to_add.'.txt');
			}
			else
			{
				rename($file_name, $cachePath.'pm_'.$params['userid'].'_'.$params['touserid'].'_'.$id.'_'.$pos.'_'.time().'_'.$to_add.'.txt');
			}
					
		}
		
		return $id;
	}
	//return true;
	//return $this->saveMessagesInCache( $params );
}
elseif( $this->queryStr=="SELECT userid FROM {$GLOBALS['fc_config']['db']['pref']}messages where command=? or command=? and userid is not null order by userid" )
{
	$cacheDir = $this->getCachDir();
			$cachePath = $cacheDir->path;
			
			$total = '';
			$allMsg = array();
			
			while (false !== ($entry = $cacheDir->read())) 
			{
				if( $this->breakFile($entry) )
				continue;
				
				
				if( strpos($entry, $GLOBALS['fc_config']['db']['pref']."messages")!==FALSE  )
				{
					$handle = @fopen($cachePath.$entry, "r");
					while (!feof($handle))
					{
    					$buffer = fgets($handle);
						$array = explode("\t",$buffer);
						if( 
							(
								$array[5] != $params[0] &&
								$array[5] != $params[1]
							)
							&& 
							$array[6] != ''
						  )
						{
					
							$array['userid'] = $array[6];
					
							unset($array[0]);unset($array[1]);unset($array[2]);unset($array[3]);unset($array[4]);unset($array[5]);unset($array[6]);unset($array[7]);unset($array[8]);unset($array[9]);
							$allMsg[] = $array;
						}
					}
					@fclose($handle);
				}
			}
			//return $allMsg;
			
	
	return new ResultSet1( $allMsg );
}
elseif( $this->queryStr=="SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}messages WHERE toconnid=? AND id>=? ORDER BY id" )
{
	$cacheDir = $this->getCachDir();
	$cachePath = $cacheDir->path;
			
	$total = '';
	$allMsg = array();
			
	while (false !== ($entry = $cacheDir->read())) 
	{
		if( $this->breakFile($entry) )
		continue;
				
				
		if( strpos($entry, $GLOBALS['fc_config']['db']['pref']."messages")!==FALSE  )
		{
			$handle = @fopen($cachePath.$entry, "r");
			$elem = explode("_",$entry);
			$params['id'] = $params[1];
			$handle = $this->setFileMsgPos($handle,$params,$elem[2]);
			
			//stream_set_timeout($handle, 180);	
			while ($buffer = fgets($handle))
			{
    			//$buffer = fgets($handle);						
				$array = explode("\t",$buffer);
				if( $array[2]==$params[0] && $array[0]>=$params[1] )// && ''!=trim($array[3])
				{
					$array['id'] = $array[0];
					$array['created'] = $array[1];
					$array['toconnid'] = $array[2];
					$array['touserid'] = $array[3];
					$array['toroomid'] = $array[4];
					$array['command'] = $array[5];
					$array['userid'] = $array[6];
					$array['roomid'] = $array[7];
					$array['txt'] = $array[8];
					$array['chatid'] = $array[9];
					unset($array[0]);unset($array[1]);unset($array[2]);unset($array[3]);unset($array[4]);unset($array[5]);unset($array[6]);
					unset($array[7]);unset($array[8]);unset($array[9]);
					$allMsg[] = $array;
				}
			}
			
			@fclose($handle);
		}
	}
			
	//return $allMsg;
	return new ResultSet1( $allMsg );
}
elseif( strpos($this->queryStr,"SELECT msgs.*, DATE_FORMAT(DATE_ADD")!==false )
{
	$params = array('toconnid'=>$params[0], 'touserid'=>$params[1], 'toroomid'=>$params[2], 'id'=>$params[3]);
	$params['id'] = (int) $params['id'];
			
	$cacheDir = $this->getCachDir();
	$cachePath = $cacheDir->path;
	$result = array();
	$find_records = 0;
				
	while (false !== ($entry = $cacheDir->read())) 
	{
		if( $this->breakFile($entry) )
			continue;
		
		$entry_elems = explode('_', $entry);
		if($entry_elems[0] == 'pm')
		{
			$is_private = true;
			$userid = (int) $entry_elems[1];
			$touserid = (int) $entry_elems[2];
		}
		else
		{
			$is_private = false;
			$userid = $entry_elems[1];
			$toroomid = $entry_elems[0];
		}
			
		$str = $this->queryStr;
	
		if($_REQUEST['roomid'])
		{
			if( $toroomid!=$_REQUEST['roomid'] )
				continue;
		}
		
		if($_REQUEST['userid']) 
		{
			if( $userid!=$_REQUEST['userid'] && $touserid!=$_REQUEST['userid'] )
				continue;
		}
				
		$handle = @fopen($cachePath.$entry, 'r');
		$tempArray = array();
		
		
		//stream_set_timeout($handle, 180);				
		while ($line = fgets($handle))				
		{
			//$line = fgets($handle);
			
			if( $line=='' )
				continue;
				
			$find_records++;	
			$line_elems = explode('#', $line);
					
			$tempArray['id'] = $line_elems[0];
			$tempArray['created'] = $line_elems[1];
			$tempArray['toconnid'] = '';
				
			if($_REQUEST['days']) 
			{
				if( strtotime($tempArray['created']) <= strtotime($_REQUEST['days']) )
					continue;
			}
			if($_REQUEST['from']) 
			{
				if( strtotime($tempArray['created']) <= strtotime($_REQUEST['from']) )
					continue;
			}
				
			if($_REQUEST['to']) 
			{
				if( strtotime($tempArray['created']) >= strtotime($_REQUEST['to']) )
					continue;
			}
				
			if( $is_private )
				$tempArray['touserid'] = $entry_elems[2];
			else
				$tempArray['touserid'] = '';
				
			$tempArray['toroomid'] = $toroomid;
			$tempArray['command'] = 'msg';
			$tempArray['userid'] = $entry_elems[1];
			$tempArray['roomid'] = $entry_elems[0];
			if($_REQUEST['keyword']) 
		 	{
				if( strpos($line_elems[3],$_REQUEST['keyword'])!==true )
					continue;
			}
			$tempArray['txt'] = $line_elems[3];
			$tempArray['chatid'] = 1;
			$tempArray['sent'] = date("F j, Y, g:i a",strtotime($line_elems[1]));
			
			$file_name = $this->getCachFileName('Rooms');
			//$arrayRoom = file( $file_name );
			
			$i = 0;
			while( !($arrayRoom = file($file_name)) )
			{
				usleep(1000);//for linux
				//toLog("buffer$i",$buffer);
				$i++;
				if( $i>1000  )
					break;
			}
			
			$toRoomStr = '';
			$fromRoomStr = '';
			
				
			foreach( $arrayRoom as $key=>$val )
			{
				$room_elems = explode("\t", $val);
					
				if( $room_elems[0]==$entry_elems[0] )
				{
					$toRoomStr = $room_elems[3];
					$fromRoomStr = $room_elems[3];	
					break;				
				}
			}
				
			$tempArray['toroom'] = $toRoomStr;
			$tempArray['fromroom'] = $fromRoomStr;
					
			$result[] = $tempArray;
		}
		fclose( $handle );
	}
			
	if( $find_records>0 )
	{
		if( !function_exists("cmp11") )
		{
			function cmp11($elem1, $elem2)
			{
				if($elem1['id']<$elem2['id'])
					return -1;
				elseif($elem1['id']==$elem2['id'])
					return 0;
				elseif($elem1['id']>$elem2['id'])
					return 1;
			}
		}
		usort($result, "cmp11");
				//return $result;
		return new ResultSet1( $result );			
	}
	else
	{
		$result = array();
		return new ResultSet1( $result );
	}		
}
else
{
	if( ($rows=$this->messageIsCached($params)) !== FALSE )
	{
		return new ResultSet1($rows);
	}
	else
	{
		
		//toLog("cacheDir",'begin');
		$cacheDir = $this->getCachDir();
			$cachePath = $cacheDir->path;
			
			$total = '';
			$allMsg = array();
					
			while (false !== ($entry = $cacheDir->read())) 
			{
				if(  $this->breakFile($entry) || strpos($entry, "{$GLOBALS['fc_config']['db']['pref']}messages_")!==FALSE )
				continue;
				
				
				if( strpos($entry, $GLOBALS['fc_config']['db']['pref']."messages")!==FALSE  )
				{
					$handle = @fopen($cachePath.$entry, "r");
					$elem = explode("_",$entry);
					
					$params['id'] = $params[3];
					$this->setFileMsgPos($handle,$params,$elem[2]);
					
					//stream_set_timeout($handle, 180);
					while ($buffer = fgets($handle))
					{
    					//$buffer = fgets($handle);
						$array = explode("\t",$buffer);
						if( 
							(
								$array[2]!='msg' &&
								$array[5]!='lout' &&
								$array[2]!='msgu'
							)
							&&
							(
								$array[2]==$params[0] ||
								$array[3]==$params[1] ||
								$array[4]==$params[2] ||
								(
									$array[2]=='' &&
									$array[3]=='' &&
									$array[4]==''
								)
							)
							&&
								$array[0]>=$params[3]
					
						)
						{
							$array['id'] = $array[0];
							$array['created'] = $array[1];
							$array['toconnid'] = $array[2];
							$array['touserid'] = $array[3];
							$array['toroomid'] = $array[4];
							$array['command'] = $array[5];
							$array['userid'] = $array[6];
							$array['roomid'] = $array[7];
							$array['txt'] = $array[8];
							$array['chatid'] = $array[9];
							unset($array[0]);unset($array[1]);unset($array[2]);unset($array[3]);unset($array[4]);unset($array[5]);unset($array[6]);unset($array[7]);unset($array[8]);unset($array[9]);
							$allMsg[] = $array;
						}
					}
					@fclose($handle);	
			
				}
			}
			
		return new ResultSet1( $allMsg );
	}

}
?>