<?php
	define('STATEMENT_SELECT', 'select');
	define('STATEMENT_INSERT', 'insert');
	define('STATEMENT_UPDATE', 'update');
	define('STATEMENT_DELETE', 'delete');

	class Statement {
		var $queryArray;
		var $queryStr;
		var $type = STATEMENT_SELECT;
		var $conn = null;

		function Statement( $queryStr, $dosplit=true ) {
			$this->queryArray = $dosplit ? split('\?', $queryStr) : array($queryStr);
			$this->type = strtolower(substr($queryStr, 0, 6));
			$this->queryStr = $queryStr;
			/*
				Check to see if $queryStr is cached. If not
				cached, then create DB connection.
			*/
		}
		
		//Return max ID value from table
		function getRecordsCount($table_name)
		{
			/*$selResource = mysql_query("SELECT MAX(id) FROM $table_name", $this->conn);
			$selResult = mysql_fetch_array($selResource);
			$maxId = (int) $selResult[0];*/
			return $maxId;
		}
		
		//If admin logged, this function return :"./../".$GLOBALS['fc_config']['cachePath'];
		//If user, this function return :$GLOBALS['fc_config']['cachePath'];
		function getCachDir()
		{
			$dir = @dir($GLOBALS['fc_config']['cachePath']);
			if(strpos($dir->handle, 'Resource')!==FALSE)
				return $dir;
			else
				return dir('./../'.$GLOBALS['fc_config']['cachePath']);
		}
		
		//input: Rooms, Stats, Ignors
		//output: path to cach file
		function getCachFileName($input)
		{
			if( $input == '' ) return null;
			$cacheDir = $this->getCachDir();
			$cachePath = $cacheDir->path;
			
			$fileName = '';
			
			//while (false !== ($entry = $cacheDir->read())) 
			//{
				switch($input)
				{
					case 'Stats':
						$fileName = $cachePath.'messages_stats_'.$GLOBALS['fc_config']['cacheFilePrefix'].'.txt';
						break;
					case 'Rooms':
						$fileName = $cachePath.$GLOBALS['fc_config']['db']['pref'].'rooms_'.$GLOBALS['fc_config']['cacheFilePrefix'].'.txt';
						break;
					case 'Connections':
						$fileName = $cachePath.$GLOBALS['fc_config']['db']['pref'].'connections_'.$GLOBALS['fc_config']['cacheFilePrefix'].'.txt';					
						break;
					case 'Users':
						$fileName = $cachePath.$GLOBALS['fc_config']['db']['pref'].'users_'.$GLOBALS['fc_config']['cacheFilePrefix'].'.txt';					
						break;
					case 'Ignors':
						$fileName = $cachePath.$GLOBALS['fc_config']['db']['pref'].'ignors_'.$GLOBALS['fc_config']['cacheFilePrefix'].'.txt';					
						break;
					case 'Bans':
						$fileName = $cachePath.$GLOBALS['fc_config']['db']['pref'].'bans_'.$GLOBALS['fc_config']['cacheFilePrefix'].'.txt';					
						break;
					case 'Messages':
						return $cachePath.$GLOBALS['fc_config']['db']['pref'].'messages_'.$GLOBALS['fc_config']['cacheFilePrefix'].'.txt';					
						break;
					case 'MessagesCommand':
						return $cachePath.$GLOBALS['fc_config']['db']['pref'].'messagescmd_'.$GLOBALS['fc_config']['cacheFilePrefix'].'.txt';					
						break;
				}
				if( file_exists($fileName) )
				{
					return $fileName;
				}
				else
				{
					return null;
				}
		}
		//columns: id or *
		//condition: string AFTER WHERE
		// queryParams: params that passed into this->process(...)
		function roomsIsCached($columns, $condition, $queryParams )
		{
			$result = array();
			$roomsFileName = $this->getCachFileName('Rooms');
			if($roomsFileName!=null)
			{
				$rooms = file($roomsFileName);
				for($i=0;$i<count($rooms);$i++)
				{
					$roomsElems = explode("\t", $rooms[$i]);
					
					if($condition=='id=?')
					{
						if((int) $roomsElems[0] == (int) $queryParams[0])
						{
							if($columns=='*')
							{
								$result_elem = array('id'=>$roomsElems[0], 'updated'=>$roomsElems[1], 'created'=>$roomsElems[2], 'name'=>$roomsElems[3],'password'=>$roomsElems[4], 'ispublic'=>$roomsElems[5], 'ispermanent'=>$roomsElems[6]);
								$result[count($result)] = $result_elem;
							}
							elseif($columns=='id')
							{
								$result_elem = array('id'=>$roomsElems[0]);
								$result[count($result)] = $result_elem;
							}
						}
					}
					elseif($condition=='ispermanent IS NULL AND updated < DATE_SUB(NOW(),INTERVAL ? SECOND)')
					{
						$dateToSub = strtotime($roomsElems[1]);//some bug ???
						$today = getdate();
						$subDate = $today[0]-(int)$queryParams[0];

						if( strpos((string)$roomsElems[6], 'NULL')!==FALSE && $dateToSub<$subDate)
						{
							if($columns=='*')
							{
								$result_elem = array('id'=>$roomsElems[0], 'updated'=>$roomsElems[1], 'created'=>$roomsElems[2], 'name'=>$roomsElems[3], 'password'=>$roomsElems[4], 'ispublic'=>$roomsElems[5], 'ispermanent'=>$roomsElems[6]);
								$result[count($result)] = $result_elem;
							}
							elseif($columns=='id')
							{
								$result_elem = array('id'=>$roomsElems[0]);
								$result[count($result)] = $result_elem;
							}
							
						}
					}
				}
				
				return $result;
			}
			else
			{
				//IF rooms file not found,
				//RESTORING ROOMS in cache
				$this->saveRoomsInCache();
				return false;
			}
		}
		//check if this is file return true
		function is_this_file($file,$params)
		{
			$entry_elems = explode('_', $file);						
			if( 
				(
					(
						$entry_elems[0] == $params['toroomid'] ||
						$entry_elems[0] == ''
					)
					&&
					$entry_elems[2] >= $params['id'] &&
					$entry_elems[0] != 'pm'
				)
				||
				(
					$entry_elems[0] == 'pm' &&
					$entry_elems[3] >= $params['id'] //&&
					//$entry_elems[2] == $params['touserid']
				)
			)
			{
				return true;
			}
			else
				return false;
		}
		//set file position start 
		function setFilePos($point,$params,$seek)
		{
			fseek($point,$seek);
			while (!feof($point))				
			{
				$line = fgets($point);				
				
				$line_elems = explode('#', $line);
				$bit_pos = $line_elems[count($line_elems)-1];
				fseek($point,$bit_pos);
				$line = fgets($point);
				$line_elems = explode('#', $line);
				if( $line_elems[0] < $params['id'] )
					break;
				
				if( $line_elems[4]<=0 )
					break;
				fseek($point,$bit_pos-20);
					
				
			}
			
			if( $bit_pos == '' || $bit_pos == null )
				fseek($point,0);	
			else
				fseek($point,$bit_pos);	
	
		}
		function setFileMsgPos($point,$params,$seek)
		{
			fseek($point,$seek);
			
			while (!feof($point))				
			{
				$line = fgets($point);				
				$line_elems = explode("\t", $line);
				$bit_pos = $line_elems[count($line_elems)-2];
				fseek($point,$bit_pos);
				$line = fgets($point);
				$line_elems = explode("\t", $line);
				if( $line_elems[0] < $params['id'] )
					break;
				
				if( $line_elems[count($line_elems)-2]<=0 )
					break;
				fseek($point,$bit_pos-20);
					
				
			}
			
			if( $bit_pos == '' || $bit_pos == null )
				fseek($point,0);	
			else
				fseek($point,$bit_pos);	
			
			return $point;
	
		}
		function getCommand( $point, $params,$id_start,$id_end )
		{
			$result = array();
			//return $result;
			$find_records = 0;
			while (!feof( $point ))				
				{
					$line = fgets($point);
					if( $line=='' )
						continue;
						
					$line_elems = explode("\t", $line);
					$id = (int) $line_elems[0];
					$created = $line_elems[1];
					$roomid = (int) $line_elems[2];
					if($id_start<=$id && $id<=$id_end)
					{
						$find_records++;
												
						$txt = $line_elems[3];
						$result_elem = array('id'=>$line_elems[0], 'created'=>$line_elems[1], 'toconnid'=>$line_elems[2], 'touserid'=>$line_elems[3], 'toroomid'=>$line_elems[4], 'command'=>$line_elems[5],'userid'=>$line_elems[6], 'roomid'=>$line_elems[7], 'txt'=>$line_elems[8]);
												
						$result[count($result)] = $result_elem;
						//break;
					}
				}
				fclose($point);
			
			if( $find_records>0 )
				return $result[0];
			else
				return false;
		}
		//checking if last messages is cached
		//input: params that passed in this->process(...)
		function messageIsCached( $queryParams ) 
		{
			//creating params array("param name"=>"param value", ...);
			$params = array('toconnid'=>$queryParams[0], 'touserid'=>$queryParams[1], 
			        					 'toroomid'=>$queryParams[2], 'id'=>$queryParams[3]);
			$stat_arr = array();
			$stats_file_name = $this->getCachFileName('Stats');
			
			$stats_file = @fopen($stats_file_name, 'r');
			
			if( !$stats_file) 
			{
			    $stat_value = $this->getRecordsCount("{$GLOBALS['fc_config']['db']['pref']}messages");
				//RESTORING message_stats file
				$this->saveStatsInCache('MESSAGES_COUNT', $stat_value);
				return false;
			}
			
			while (!feof($stats_file)) 
			{
			    $stat = fgets($stats_file);
			    $stat_elems = explode('=', $stat);
				if($stat_elems[0] == 'MESSAGES_COUNT')
				{
					$stat_arr['MESSAGES_COUNT'] = $stat_elems[1];
				}
			}
			@fclose($stats_file);
			$params['id'] = (int) $params['id'];
			$stat_arr['MESSAGES_COUNT'] = (int) $stat_arr['MESSAGES_COUNT'];
			
			//checking all cached files if they have messages with id's: $params["id"] .. $stat_arr["COUNT"]
			// if only one ID not found in cach files(if database have spec. command with this id),
			// this function return's false and we select all messages from files.

			$cacheDir = $this->getCachDir();
			$cachePath = $cacheDir->path;
			$id_start = $params['id'];
			$id_end = $stat_arr['MESSAGES_COUNT'];
			$find_records = 0;
			$result = array();
			
			
			if((int) $id_start > (int) $id_end)
			{
				return $result;
			}
			
			
			while (false !== ($entry = $cacheDir->read())) 
			{
				if(strpos($entry, 'messages_stats_')!==FALSE ||
				   strpos($entry, "{$GLOBALS['fc_config']['db']['pref']}rooms_")!==FALSE ||
			       strpos($entry, "{$GLOBALS['fc_config']['db']['pref']}connections_")!==FALSE ||
				   strpos($entry, "{$GLOBALS['fc_config']['db']['pref']}messages_")!==FALSE ||
				   strpos($entry, "{$GLOBALS['fc_config']['db']['pref']}users_")!==FALSE ||
				   strpos($entry, 'index')!==FALSE ||
				   strpos($entry, 'tables_id')!==FALSE ||
				   strpos($entry, 'update')!==FALSE
				   )
					continue;
				
				$entry_elems = explode('_', $entry);
				
				if( !$this->is_this_file($entry,$params))// &&  strpos($entry, 'messagescmd_')===false
					continue;
				
				if( (strpos($this->queryStr,"ORDER BY id DESC LIMIT")!==false || strpos($this->queryStr,"SELECT count(*) AS numb")!==false) &&  $entry_elems[0] == '')//
					continue;
					
				
				$is_cmd = false;
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
				
				if( strpos($entry, 'messagescmd_')!==false )
				{
					$is_cmd = true;
				}
				$handle = @fopen($cachePath.$entry, 'r');
				$tempArray = array();
				if( !$is_cmd )
					$this->setFilePos($handle,$params,$entry_elems[3]);
				else
				{
					//$result[count($result)] = $this->getCommand( $handle,$params,$id_start,$id_end );
					/*$res = $this->getCommand( $handle,$params,$id_start,$id_end );
					
					if( $res )
					{
						$result[count($result)] = $res;
						$find_records = count( $result );
					}
					
					continue;*/
				}
				
				while (!feof($handle))				
				{
					
					$line = fgets($handle);
					
					$line_elems = explode('#', $line);
					
					$id = (int) $line_elems[0];
					$created = $line_elems[1];
					$roomid = (int) $line_elems[2];
					
					
					
					if($id_start<=$id && $id<=$id_end)
					{
						//$userid						
						if($is_private)
						{
							if( $touserid != $params['touserid'] && $userid!=$params['touserid'] )//
								continue;							
						}
						else
						{
							if( $toroomid != $params['toroomid'] )
							{
								if( $toroomid == '' && $userid==$params['touserid'])
								{
								}
								else
									continue;
							}
						}
						
						$txt = $line_elems[3];
						$txt = str_replace("%$$%$", "#", $txt);
												
						$find_records++;
						if($is_private)
							$result_elem = array('id'=>$id, 'created'=>$created, 'touserid'=>$touserid, 'command'=>'msg','userid'=>$userid, 'roomid'=>$roomid, 'txt'=>$txt);
						else
							$result_elem = array('id'=>$id, 'created'=>$created, 'toroomid'=>$params['toroomid'], 'command'=>'msg','userid'=>$userid, 'roomid'=>$roomid, 'txt'=>$txt);
												
						$result[count($result)] = $result_elem;
						//break;
					}
				}
				fclose($handle);
			}
			
			//See explain at the top			
			//if( $stat_arr['MESSAGES_COUNT']-$params['id']+1 == $find_records )
			if( $find_records>0 )
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
				usort($result, cmp);
				
				toLog("result",$result);
				
				return $result;
				
						
				
			}
			else
				return false;

		}
		
		//check if connections is cached
		function connectionsIsCached($columns, $condition, $queryParams)
		{
			$result = array();
			$connectionsFileName = $this->getCachFileName('Connections');
			if($connectionsFileName!=null)
			{
				$connections = file($connectionsFileName);
				for($i=0;$i<count($connections);$i++)
				{
					$connectionsElems = explode("\t", $connections[$i]);
					if($condition=='userid IS NOT NULL AND updated < DATE_SUB(NOW(),INTERVAL ? SECOND) AND ip <> ?')
					{
						$dateToSub = strtotime($connectionsElems[1]);//???
						$today = getdate();
						$subDate = $today[0]-(int)$queryParams[0];
						if($connectionsElems[3]!='NULL' && $dateToSub<$subDate && $connectionsElems[9]!=$queryParams[1])
							if($columns=='ip')
							{
								$result_elem = array('id'=>$connectionsElems[0]);
								$result[count($result)] = $result_elem;
							}
					}
				}
				return $result;
			}
			else
			{
				//RESTORING connections
				$params = array();
				$this->saveConnectionsInCache($params);
				return false;
			}
		}
		function insertCommand( $params )
		{
			$cacheDir = $this->getCachDir();
			$cachePath = $cacheDir->path;
			
			while (false !== ($entry = $cacheDir->read())) 
			{
				if(
					strpos($entry, 'messages_stats')!==FALSE ||
					strpos($entry, $GLOBALS['fc_config']['db']['pref']."rooms")!==FALSE ||
					strpos($entry, $GLOBALS['fc_config']['db']['pref']."ignors")!==FALSE ||
					strpos($entry, $GLOBALS['fc_config']['db']['pref']."connections")!==FALSE ||
					strpos($entry, $GLOBALS['fc_config']['db']['pref']."users")!==FALSE ||
					strpos($entry, $GLOBALS['fc_config']['db']['pref']."bans")!==FALSE ||
					strpos($entry, '.htaccess')!==FALSE ||
					strpos($entry, 'tables_id')!==FALSE ||
					strpos($entry, 'update')!==FALSE ||
					$entry == '.' ||
					$entry == '..' ||
					$entry=='index.html'
				  )
				continue;
				
				
				if( strpos($entry, $GLOBALS['fc_config']['db']['pref']."messages")!==FALSE  )
				{
					$file = @fopen($cachePath.$entry, 'a');
					$params[0] = date("Y-m-d H:i:s");
					$str = implode( "\t",$params );
					$id = $this->file_insert_id(7);
			
					$pos = filesize( $cachePath.$entry );
			
					@fwrite($file,"$id\t".$str."\t1\t$pos\t\n");			
					@fclose($file);
				
					rename($cachePath.$entry,$cachePath.$GLOBALS['fc_config']['db']['pref'].'messages_'.$pos.'_'.$GLOBALS['fc_config']['cacheFilePrefix'].'.txt');

					return $id;
				}
			}
			return null;
			
		}
		//queryParams: params, passed to this->process function
		function saveMessagesInCache($queryParams)
		{
			$cacheDir = $this->getCachDir();
			$cachePath = $cacheDir->path;
			if($this->queryStr=="DELETE FROM {$GLOBALS['fc_config']['db']['pref']}messages WHERE created < DATE_SUB(NOW(),INTERVAL ? SECOND)")
			{
			}
			else//if we INSERT new messages
			{
				$isPrivate = ($queryParams[2]!='');
				if( $queryParams[4]!='msg' )
				{
					/*if( $queryParams[4]=='mvu' || $queryParams[4]=='banu')// || $queryParams[4]=='lout'
					{
						return $this->insertCommandAll( $queryParams );
					}
					else*/
						return $this->insertCommand( $queryParams );
				}
				$id = $this->file_insert_id( 7 );
				//toLog("queryParams",$queryParams);
				$params = array('id'=>$id, 'touserid'=>$queryParams[2], 'toroomid'=>$queryParams[3], 'userid'=>$queryParams[5], 'roomid'=>$queryParams[6], 'txt'=>$queryParams[7]);
				/*if( $params['roomid']=='' )
					return;*/
				
				$today = date("Y-m-d G:i");
				//$today = time();
				$appended = false;
				$greate = false;
				
				$to_add = $GLOBALS['fc_config']['cacheFilePrefix'];
				while (false !== ($entry = $cacheDir->read())) 
				{
					if(
						strpos($entry, 'messages_stats')!==FALSE ||
						strpos($entry, $GLOBALS['fc_config']['db']['pref']."rooms")!==FALSE ||
						strpos($entry, $GLOBALS['fc_config']['db']['pref']."ignors")!==FALSE ||
						strpos($entry, $GLOBALS['fc_config']['db']['pref']."connections")!==FALSE ||
						strpos($entry, $GLOBALS['fc_config']['db']['pref']."messages")!==FALSE ||
						strpos($entry, $GLOBALS['fc_config']['db']['pref']."users")!==FALSE ||
						strpos($entry, '.htaccess')!==FALSE ||
						strpos($entry, 'tables_id')!==FALSE ||
						strpos($entry, 'update')!==FALSE ||
						$entry == '.' ||
						$entry == '..' ||
						strpos($entry, 'index.htm')!==FALSE
					  )
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
		}
		
		function saveRoomsInCache( $queryParams )
		{
			$params = $queryParams;
			if($this->queryStr == "UPDATE {$GLOBALS['fc_config']['db']['pref']}rooms,{$GLOBALS['fc_config']['db']['pref']}connections SET {$GLOBALS['fc_config']['db']['pref']}rooms.updated=NOW() WHERE {$GLOBALS['fc_config']['db']['pref']}rooms.id = {$GLOBALS['fc_config']['db']['pref']}connections.roomid")
			{
				$rooms_file_name = $this->getCachFileName('Rooms');
				$connections_file_name = $this->getCachFileName('Connections');
				
				$rooms_file = file($rooms_file_name);
				$connections_file = file($connections_file_name);
				$records_to_update = array();
				if($rooms_file!=FALSE && $connections_file!=FALSE)
				{
					for($i=0;$i<count($rooms_file);$i++)
					{
						$rooms_elem = explode("\t", $rooms_file[$i]);
						for($j=0;$j<count($connections_file);$j++)
						{
							$connections_elem = explode("\t", $connections_file[$j]);
							if($rooms_elem[0] == $connections_elem[4])
							{
								array_push($records_to_update, $i);
								break;
							}
						}
					}
					for($i=0; $i<count($records_to_update); $i++)
					{
						$rooms_elem = explode("\t", $rooms_file[$records_to_update[$i]]);

						$rooms_elem[1] = date("Y-m-d H:i:s");//some bug ???
						
						$rooms_file[$records_to_update[$i]] = implode("\t", $rooms_elem);
					}
					$file = fopen($rooms_file_name, "w");
					for($i=0;$i<count($rooms_file);$i++)
					{
						fwrite($file, $rooms_file[$i]);
					}
					fclose($file);
				}
			}
			else
			{
				//RESTORING flashchat_rooms_ .. txt file
				if( $this->queryStr!="UPDATE {$GLOBALS['fc_config']['db']['pref']}rooms SET updated=NOW() WHERE id=?" )
				{				
					if(($file_name = $this->getCachFileName('Rooms')) != null)
						$file = @fopen($file_name, 'a');
					else
					{
						$today = getdate();//???
						$cacheDir = $this->getCachDir();
						$cachePath = $cacheDir->path;
						$file = @fopen($cachePath.$GLOBALS['fc_config']['db']['pref'].'rooms_'.$GLOBALS['fc_config']['cacheFilePrefix'].'.txt', 'a');
					}
					if(!$file) return;
					
					$id = $this->file_insert_id(8);
					fwrite($file, $id."\t".date("Y-m-d H:i:s")."\t".date("Y-m-d H:i:s")."\t".$params[0]."\t".$params[1]."\t".$params[2]."\t"."\t"."\n");
					fclose($file);
					
					$filename = $GLOBALS['fc_config']['cachePath'].'updroom_'.$id.'_'.$GLOBALS['fc_config']['cacheFilePrefix'].'_.txt';
					$file = fopen($filename, "w");
					fwrite($file, time());
					fclose($file);
					
					return $id;
				}
			}
		}
		
		//queryParams: params, passed to this->process function
		function saveConnectionsInCache($queryParams)
		{
			$file_name = $this->getCachFileName('Connections');
			if(($file_name = $this->getCachFileName('Connections')) != null)
				$file = @file($file_name);
			else
			{
				$today = getdate();//???
				$file = array();
				$cacheDir = $this->getCachDir();
				$cachePath = $cacheDir->path;
				$file_name = $cachePath.$GLOBALS['fc_config']['db']['pref'].'connections_'.$GLOBALS['fc_config']['cacheFilePrefix'].'.txt';
			}
			
			$fileRecordsCount = count($file);
			
			if($this->queryStr=="UPDATE {$GLOBALS['fc_config']['db']['pref']}connections SET updated=NOW() WHERE id=?")
			{
				for($i=0; $i<$fileRecordsCount; $i++)
				{
					$fileRecord = explode("\t", $file[$i]);
					
					if($fileRecord[0]==$queryParams[0])
					{
						$today = date("Y-m-d G:i:s");
						$fileRecord[1] = $today;
						$file[$i] = implode("\t", $fileRecord);
						break;
					}
				}
			}
			elseif(strpos($this->queryStr, 'DELETE')!==FALSE)
			{
				$fileRecordsCount = count($file);
				$deletedElements = array();
				for($i=0; $i<$fileRecordsCount; $i++)
				{
					$fileRecord = explode("\t", $file[$i]);
					if($this->queryStr=="DELETE FROM {$GLOBALS['fc_config']['db']['pref']}connections WHERE id = ?")
					{
						if($fileRecord[0] == $queryParams[0])
						{
							unset($file[$i]);
							break;
						}
					}
					elseif($this->queryStr=="DELETE FROM {$GLOBALS['fc_config']['db']['pref']}connections WHERE updated < DATE_SUB(NOW(),INTERVAL ? SECOND)")
					{
						$dateToSub = strtotime($fileRecord[1]);
						$today = getdate();//???
						$subDate = $today[0]-(int)$queryParams[0];
						if($dateToSub<$subDate)
							array_push($deletedElements, $i);
					}
				}
				for($i=0; $i<count($deletedElements); $i++)
				{
					unset($file[$deletedElements[$i]]);
				}
			}elseif(strpos($this->queryStr, 'INSERT')!==FALSE)
			{
				
			}
			
			$writeFile = @fopen($file_name, 'w');
			for($i=0; $i<count($file); $i++)
			{
				@fwrite($writeFile, $file[$i]);
			}
			@fclose($writeFile);
		}
		
		function saveStatsInCache($stat_name, $stat_value)
		{
			$fileName = $this->getCachFileName('Stats');
	
			if($fileName!=null)
			{
				$file = @fopen($fileName, 'r');
				$file_created = false;
			}
			else
			{
				$today = getdate();
				$cacheDir = $this->getCachDir();
				$cachePath = $cacheDir->path;
				$fileName = $cachePath.'messages_stats_'.$GLOBALS['fc_config']['cacheFilePrefix'].'.txt';
				$file = @fopen($fileName, 'w');
				$file_created = true;
			}
			if(!$file) return;
			//$lines = file($fileName);
			$replaced = false;
			$newLines = array();
			if(!$file_created)
			while(!feof($file))
			{
				$line = fgets($file);
				if($line=="")
					continue;
				
				$lineElems = explode('=', $line);
				if($lineElems[0] == $stat_name)
				{
					array_push($newLines, $lineElems[0].'='.$stat_value."\n");
					$replaced = true;
				}
				else
					array_push($newLines, $lineElems[0].'='.$lineElems[1]);
			}
			fclose($file);
			if(!$replaced)
				array_push($newLines, $stat_name.'='.$stat_value."\n");

			$file = @fopen($fileName, 'w');
			if($file)
			{
				for($i=0; $i<count($newLines); $i++)
					@fwrite($file, $newLines[$i]);
				@fclose($file);
			}
		}
		//insert virtual id to file
		//0-bans;1-config;2-config_chats;3-config_instances;4-config_value;5-connections;6-ignors;7-messages;8-rooms;9-users
		function file_insert_id( $table,$view = '' )
		{
			//$cacheDir = $this->getCachDir();
			//$cachePath = $cacheDir->path;
			$fname = $GLOBALS['fc_config']['cachePath'].'tables_id.txt';
			if( !file_exists( $fname ) )
			{
				$fp = @fopen($fname,"w+");
				@fwrite($fp, '0#0#0#0#0#0#0#0#4#0');
				@fclose( $fp );
			}
			
			$i = 0;
			while( !($buffer = file($fname)) )
			{
				usleep(1000);//for linux
				//toLog("buffer$i",$buffer);
				$i++;
				if( $i>1000  )
					break;
			}
			
			$array = explode( '#',$buffer[0] );
			
			if( $view=='' )
			{
				$array[$table]++;
				$count = $array[$table];
				$str = implode('#',$array);
			
				$fp = @fopen($fname,"w+");
				@fwrite( $fp , $str );
				@fclose( $fp );
				if( $table==7 )
				$this->saveStatsInCache('MESSAGES_COUNT', $count);
			
			}
			else
			{
				$count = $array[$table];
			}
			
				
				
			return $count;
		}
		//insert row into connection
		function insertConn( $queryParams )
		{
			$file_name = $this->getCachFileName('Connections');
			if(($file_name = $this->getCachFileName('Connections')) != null)
				$file = @fopen($file_name,'a');
			else
			{
				$cacheDir = $this->getCachDir();
				$cachePath = $cacheDir->path;
				$file_name = $cachePath.$GLOBALS['fc_config']['db']['pref'].'connections_'.$GLOBALS['fc_config']['cacheFilePrefix'].'.txt';
			}
			
			$params = $queryParams;
			
			if( $params[1]!='' )
			{
				$file = @fopen( $GLOBALS['fc_config']['cachePath'].'update_'.$params[0].'_.txt','w' );
				@fclose($file);
			}
			
			$today = date("Y-m-d H:i:s");//???
			$file = @fopen($file_name,'a');
			$fileRecordsCount = count($file);
							
			
			
			$str = $params[0]."\t"."$today"."\t"."$today"."\t".$params[1]."\t".$params[2]."\t".$params[3]."\t".$params[4]."\t".$params[5]."\t".$params[6]."\t".$params[7]."\t\t1\n";
			
			@fwrite($file, $str);
			@fclose($file);
		}
		//update row connections
		function updateConn( $whot='*',$queryParams )//update file connection
		{
			$file_name = $this->getCachFileName('Connections');
			$handle = fopen($file_name, "r");
			$params = $queryParams;
			$total = '';
			
			
			while (!feof($handle))
			{
    			$buffer = fgets($handle);
				
				if( trim($buffer)=='' )
					continue;
					
				$array = explode("\t",$buffer);
				$today = date("Y-m-d H:i:s");//
						
				if( $whot=='*' )
				{
					if( strpos($buffer,$params[8])!==false )
					{
						$total = $total.$params[8]."\t"."$today"."\t".$array[2]."\t".$params[0]."\t".$params[1]."\t".$params[2]."\t".$params[3]."\t".$params[4]."\t".$params[5]."\t".$params[6]."\t".$params[7]."\t1\n";
					}
					else
						$total = $total.$buffer;
				}
			}
			
			@fclose($handle);
			if( $total!='')
			{
				$file = @fopen($file_name,'w');
				@fwrite($file , $total);
				@fclose($file);
			}
			return $params[8];
			
		}
		function updateConn1( $params )
		{
			$fname = $GLOBALS['fc_config']['cachePath'].'update_'.$params[0].'_.txt';
			if( file_exists( $fname ) )
			{
				
				$fp = @fopen($fname,"w");
				@fwrite($fp,time());//"Y-m-d H:i:s"
				@fclose( $fp );
				return $params[0];
			}
		}
		function selectIfConn(  )
		{
			$cacheDir = $this->getCachDir();
			$cachePath = $cacheDir->path;
			$fname = $cachePath.$GLOBALS['fc_config']['db']['pref'].'connections_'.$GLOBALS['fc_config']['cacheFilePrefix'].'.txt';
			if( file_exists( $fname ) )
			{
				
				return true;
			}
			else
			{
				
				return null;
			}
			
		}
		function selectConnUser( $params )
		{
			
			$file_name = $this->getCachFileName('Connections');
			$handle = fopen($file_name, "r");
			$total = '';
			$allUsers = array();
			while (!feof($handle))
			{
    			$buffer = fgets( $handle );
				
				if( $buffer=='' )
					continue;
					
					
				$array = explode("\t",$buffer);
				if( $array[3]!=$params[0] && $array[3]!='' && $array[4]==$params[1] )
				{
					$array['userid'] = 	$array[3];
					$array['roomid'] = 	$array[4];
					$array['state'] = 	$array[5];
					$array['color'] = 	$array[6];
					$array['lang'] = 	$array[8];
					
					unset($array[0]);unset($array[1]);unset($array[2]);unset($array[3]);unset($array[4]);unset($array[5]);unset($array[6]);
					unset($array[7]);unset($array[8]);unset($array[9]);unset($array[10]);
					$allUsers[] = $array;
					break;
				}
			}
			@fclose($handle);		
			return $allUsers;
		}
		function selectConnAll()
		{
			$file_name = $this->getCachFileName('Connections');
			$handle = fopen($file_name, "r");
			$total = '';
			$allUsers = array();
			while (!feof($handle))
			{
    			$buffer = fgets( $handle );
				
				if( $buffer=='' )
					continue;
					
					
				$array = explode("\t",$buffer);
				$array['id'] = 	$array[0];
				$array['updated'] = 	$array[1];
				$array['created'] = 	$array[2];
				
				$array['userid'] = 	$array[3];
				$array['roomid'] = 	$array[4];
				$array['state'] = 	$array[5];
				$array['color'] = 	$array[6];
				$array['start'] = 	$array[7];
				$array['lang'] = 	$array[8];
				$array['ip'] = 	$array[9];
				$array['tzoffset'] = 	$array[10];
				
				unset($array[0]);unset($array[1]);unset($array[2]);unset($array[3]);unset($array[4]);unset($array[5]);unset($array[6]);
				unset($array[7]);unset($array[8]);unset($array[9]);unset($array[10]);
				$allUsers[] = $array;
					
				
			}
			
			@fclose($handle);		
			return $allUsers;
		}
		function selectLang( $params )
		{
			$file_name = $this->getCachFileName('Connections');
			$handle = fopen($file_name, "r");
			$total = '';
			$allUsers = array();
			while (!feof($handle))
			{
    			$buffer = fgets( $handle );
				
				if( $buffer=='' )
					continue;
					
				if( $array[0]!=$params[0] )	
					continue;
				$array = explode("\t",$buffer);
				
				$array['lang'] = $array[8];				
				unset($array[0]);unset($array[1]);unset($array[2]);unset($array[3]);unset($array[4]);unset($array[5]);unset($array[6]);
				unset($array[7]);unset($array[8]);unset($array[9]);unset($array[10]);
				$allUsers[] = $array;
					
				
			}
			
			@fclose($handle);		
			return $allUsers;
		}
		function processConnect( $queryParams )
		{
			switch($this->queryStr)// 
			{
				case "SELECT lang FROM {$GLOBALS['fc_config']['db']['pref']}connections WHERE id=?":
					return new ResultSet1( $this->selectLang( $queryParams ) );
					return null;
					break;
				case "UPDATE {$GLOBALS['fc_config']['db']['pref']}connections SET updated=NOW() WHERE id=?":
					$this->updateConn1( $queryParams );
					return null;
					break;
				case "UPDATE {$GLOBALS['fc_config']['db']['pref']}connections SET updated=NOW(), userid=?, roomid=?, color=?, state=?, start=?, lang=?, ip=?, tzoffset=? WHERE id=?":
					$this->updateConn('*',$queryParams);
					return null;
					break;
				case "INSERT INTO {$GLOBALS['fc_config']['db']['pref']}connections (id, updated, created, userid, roomid, color, state, start, lang, ip) VALUES (?, NOW(), NOW(), ?, ?, ?, ?, ?, ?, ?)":
					$this->insertConn($queryParams);
					return null;
					break;
				case "SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}connections WHERE id=? LIMIT 1":	
					return null;
					break;
				case "SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}connections LIMIT 1":	
					return $this->selectIfConn();
					break;
				case "SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}connections":	
					return new ResultSet1($this->selectConnAll());
					break;
				case "SELECT id FROM {$GLOBALS['fc_config']['db']['pref']}connections WHERE userid IS NOT NULL AND updated < DATE_SUB(NOW(),INTERVAL ? SECOND) AND ip <> ?":
					return new ResultSet1($this->selectConnId( "id","",$queryParams ));
					break;
				case "SELECT userid, state, color, lang, roomid FROM {$GLOBALS['fc_config']['db']['pref']}connections WHERE userid IS NOT NULL AND userid <> ? AND roomid=?":
					return new ResultSet1($this->selectConnUser( $queryParams ));
					break;
				case "SELECT count(*) as msgnumb FROM {$GLOBALS['fc_config']['db']['pref']}connections WHERE userid IS NOT NULL":
					return null;
					break;
				case "SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}connections WHERE userid IS NOT NULL ORDER BY roomid":
					return new ResultSet1($this->selectConnUserAll( $queryParams ));
					break;
				case "SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}connections WHERE roomid<>? AND userid IS NOT NULL":
					return null;
					break;
				case "SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}connections WHERE roomid=? AND userid IS NOT NULL":
					return null;
					break;
				case "SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}connections WHERE userid IS NOT NULL":
					return new ResultSet1($this->selectConnUserComm( $queryParams ));
					break;
				case "SELECT COUNT(*) AS CNT FROM {$GLOBALS['fc_config']['db']['pref']}connections WHERE roomid=? AND userid IS NOT NULL":
					return null;
					break;
				case "SELECT id FROM {$GLOBALS['fc_config']['db']['pref']}connections WHERE userid=?  LIMIT 1":
					return new ResultSet1($this->selectConn( "id","userid",$queryParams ));
					break;
				case "SELECT id, ip FROM {$GLOBALS['fc_config']['db']['pref']}connections WHERE userid=? AND id<>?":
					return null;
					break;
				case "SELECT COUNT(*) as cnt FROM {$GLOBALS['fc_config']['db']['pref']}connections WHERE ip=? AND userid IS NOT NULL":
					return null;
					break;
				case "SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}connections WHERE id<>? AND userid IS NOT NULL":
					return new ResultSet1($this->selectConnLogin( $queryParams ));
					break;
				case "DELETE FROM {$GLOBALS['fc_config']['db']['pref']}connections WHERE updated < DATE_SUB(NOW(),INTERVAL ? SECOND)":
					//return $this->deleteConn( $queryParams );
					return null;
					break;
				case "DELETE FROM {$GLOBALS['fc_config']['db']['pref']}connections WHERE id = ?":
					return $this->deleteConn( $queryParams );
					break;
			}
			
			if( strpos($this->queryStr, 'SELECT roomid')!==FALSE )
				return new ResultSet1($this->selectConnRoomId( "id","userid",$queryParams ));
			
		}
		function selectConnUserComm( $params )
		{
			$file_name = $this->getCachFileName('Connections');
			$handle = fopen($file_name, "r");
			$total = '';
			$allUsers = array();
			while (!feof($handle))
			{
    			$buffer = fgets( $handle );
				
				if( $buffer=='' )
					continue;
				
				$array = explode("\t",$buffer);
				if( $array[3]!='' )
				{
					
					$array['userid'] = 	$array[3];
					$array['id'] = 	$array[0];
					$array['updated'] = 	$array[1];
					$array['created'] = 	$array[2];
					$array['roomid'] = 	$array[4];
					$array['state'] = 	$array[5];
					$array['color'] = 	$array[6];
					$array['start'] = 	$array[7];
					$array['ip'] = 	$array[9];
					$array['ip'] = 	$array[9];
					$array['tzoffset'] = 	$array[10];
					
					unset($array[0]);unset($array[1]);unset($array[2]);unset($array[3]);unset($array[4]);unset($array[5]);unset($array[6]);
					unset($array[7]);unset($array[8]);unset($array[9]);unset($array[10]);unset($array[11]);
					$allUsers[] = $array;
					
				}
			}
			//toLog("allUsers",$allUsers);
			@fclose($handle);		
			return $allUsers;
		}
		function selectConnUserAll( $params )
		{
			$file_name = $this->getCachFileName('Connections');
			$handle = fopen($file_name, "r");
			$total = '';
			$allUsers = array();
			while (!feof($handle))
			{
    			$buffer = fgets( $handle );
				if( $buffer=='' )
					continue;
					
				$array = explode("\t",$buffer);
				if( $array[3]!='' )
				{
					
					$array['userid'] = $array[3];
					$array['id'] = $array[0];
					$array['updated'] = $array[1];
					$array['created'] = $array[2];
					$array['roomid'] = $array[4];
					$array['state'] = $array[5];
					$array['color'] = $array[6];
					$array['start'] = $array[7];
					$array['ip'] = $array[9];
					$array['ip'] = $array[9];
					$array['tzoffset'] = $array[10];
					
					unset($array[0]);unset($array[1]);unset($array[2]);unset($array[3]);unset($array[4]);unset($array[5]);unset($array[6]);
					unset($array[7]);unset($array[8]);unset($array[9]);unset($array[10]);unset($array[11]);
					$allUsers[] = $array;
					break;
				}
			}
			
			@fclose($handle);		
			return $allUsers;
		}
		function selectConnId( $out ,$in, $params )
		{
			//return null;
			
			$cacheDir = $this->getCachDir();
			$cachePath = $cacheDir->path;
			$allUsers = array();
			while (false !== ($entry = $cacheDir->read())) 
			{
				if(
					strpos($entry, 'messages_stats')!==FALSE ||
					strpos($entry, $GLOBALS['fc_config']['db']['pref']."rooms")!==FALSE ||
					strpos($entry, $GLOBALS['fc_config']['db']['pref']."ignors")!==FALSE ||
					strpos($entry, $GLOBALS['fc_config']['db']['pref']."connections")!==FALSE ||
					strpos($entry, $GLOBALS['fc_config']['db']['pref']."messages")!==FALSE ||
					strpos($entry, $GLOBALS['fc_config']['db']['pref']."users")!==FALSE ||
					strpos($entry, '.htaccess')!==FALSE ||
					strpos($entry, 'tables_id')!==FALSE ||
					$entry == '.' ||
					$entry == '..' ||
					strpos($entry, 'pm')!==FALSE ||
					strpos($entry, 'index.htm')!==FALSE
					)
					continue;
					
					if( strpos($entry, 'update')!==FALSE )
					{
						$arr = explode("_",$entry);
						$handle = fopen($cachePath.$entry, "r");
						$buffer = fgets( $handle );
						
						if( $buffer=='' )
							continue;
					
						fclose($handle);
						if( time()-$buffer>$params[0] )
						{
							$allUsers[]['id'] = $arr[1];
							$bool = unlink($cachePath.$entry);
							
						}
					}
					
				
			}			
			return $allUsers;
		}
		function selectConnRoomId( $params )
		{
			$file_name = $this->getCachFileName('Connections');
			$handle = fopen($file_name, "r");
			$total = '';
			$allUsers = array();
			while (!feof($handle))
			{
    			$buffer = fgets( $handle );
				
				if( $buffer=='' )
					continue;
					
					
				$array = explode("\t",$buffer);
				if( $array[0]==$params[0] && $array[3]!='' )
				{
					$array['roomid'] = 	$array[4];
					unset($array[0]);unset($array[1]);unset($array[2]);unset($array[3]);unset($array[4]);unset($array[5]);unset($array[6]);
					unset($array[7]);unset($array[8]);unset($array[9]);unset($array[10]);
					$allUsers[] = $array;
					break;
				}
			}
			@fclose($handle);		
			return $allUsers;
		}
		function deleteConn( $params )
		{
			$file_name = $this->getCachFileName('Connections');
			$handle = fopen($file_name, "r");
			$total = '';
			$allUsers = array();
			$buffer = '';
			while (!feof($handle))
			{
    			$buffer = fgets( $handle );
				$array = explode("\t",$buffer);
				if( $array[0]!=$params[0] )
				{
					$total .= $buffer;
				}
			}
			@fclose($handle);
			$handle = fopen($file_name, "w");
			@fwrite($handle , $total);
			@fclose($handle);		
			return null;
		}
		function selectMsgId( $params )
		{
			$cacheDir = $this->getCachDir();
			$cachePath = $cacheDir->path;
			$allUsers = array();
			$arrayId1 = array();
			$arrayId = array();
			while (false !== ($entry = $cacheDir->read())) 
			{
				if(
					strpos($entry, 'messages_stats')!==FALSE ||
					strpos($entry, $GLOBALS['fc_config']['db']['pref']."rooms")!==FALSE ||
					strpos($entry, $GLOBALS['fc_config']['db']['pref']."ignors")!==FALSE ||
					strpos($entry, $GLOBALS['fc_config']['db']['pref']."connections")!==FALSE ||
					strpos($entry, $GLOBALS['fc_config']['db']['pref']."messages")!==FALSE ||
					strpos($entry, $GLOBALS['fc_config']['db']['pref']."users")!==FALSE ||
					strpos($entry, $GLOBALS['fc_config']['db']['pref']."bans")!==FALSE ||
					strpos($entry, '.htaccess')!==FALSE ||
					strpos($entry, 'tables_id')!==FALSE ||
					$entry == '.' ||
					$entry == '..' ||
					strpos($entry, 'pm')!==FALSE ||
					strpos($entry, 'index.htm')!==FALSE
					)
					continue;
					
					
				
				$entry_elems = explode('_', $entry);
				
				if( $entry_elems[0] == '' )//
					continue;
					
				if( $entry_elems[0] != $params[0] )
					continue;
				
				if( (time()-$entry_elems[4])>(5*60) )
					continue;
					
				
				$handle = @fopen($cachePath.$entry, 'r');
				while (!feof( $handle ))				
				{
					
					$line = fgets( $handle );
					$line_elems = explode('#', $line);					
					$id = (int) $line_elems[0];					
					$arrayId[$id] = $id;
					
				}
				fclose( $handle );				
			}	
			
			sort( $arrayId );		
			$arrayId1[0]['id'] = $arrayId[1];				
			return $arrayId1;
		}
		function selectMsgAdmin2( $params )
		{
			$params = array('toconnid'=>$queryParams[0], 'touserid'=>$queryParams[1], 
			        					 'toroomid'=>$queryParams[2], 'id'=>$queryParams[3]);
			$params['id'] = (int) $params['id'];
			
			$cacheDir = $this->getCachDir();
			$cachePath = $cacheDir->path;

			$result = array();
			$find_records = 0;
						
			while (false !== ($entry = $cacheDir->read())) 
			{
				if(strpos($entry, 'messages_stats_')!==FALSE ||
				   strpos($entry, "{$GLOBALS['fc_config']['db']['pref']}rooms_")!==FALSE ||
			       strpos($entry, "{$GLOBALS['fc_config']['db']['pref']}connections_")!==FALSE ||
				   strpos($entry, "{$GLOBALS['fc_config']['db']['pref']}messages_")!==FALSE ||
				   strpos($entry, "{$GLOBALS['fc_config']['db']['pref']}users_")!==FALSE ||
				   strpos($entry, "{$GLOBALS['fc_config']['db']['pref']}ignors_")!==FALSE ||
				   strpos($entry, "{$GLOBALS['fc_config']['db']['pref']}bans_")!==FALSE ||
				   strpos($entry, 'index')!==FALSE ||
				   strpos($entry, 'tables_id')!==FALSE ||
				   strpos($entry, 'update')!==FALSE ||
				   $entry=='.' ||
				   $entry=='..' || 
				   $entry=='.htaccess'				   
				   )
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
								
				while (!feof($handle))				
				{
					$line = fgets($handle);
					
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
					$arrayRoom = file( $file_name );
					
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
				function cmp($elem1, $elem2)
				{
					if($elem1['id']<$elem2['id'])
						return -1;
					elseif($elem1['id']==$elem2['id'])
						return 0;
					elseif($elem1['id']>$elem2['id'])
						return 1;
				}
				usort($result, cmp);
				return $result;				
			}
			else
				return false;
			
		}
		function selectMsgAdmin1( $params )
		{
			//$file_name = $this->getCachFileName('Messages');
			
			$cacheDir = $this->getCachDir();
			$cachePath = $cacheDir->path;
			
			$total = '';
			$allMsg = array();
			
			while (false !== ($entry = $cacheDir->read())) 
			{
				if(
					strpos($entry, 'messages_stats')!==FALSE ||
					strpos($entry, $GLOBALS['fc_config']['db']['pref']."rooms")!==FALSE ||
					strpos($entry, $GLOBALS['fc_config']['db']['pref']."ignors")!==FALSE ||
					strpos($entry, $GLOBALS['fc_config']['db']['pref']."connections")!==FALSE ||
					strpos($entry, $GLOBALS['fc_config']['db']['pref']."users")!==FALSE ||
					strpos($entry, $GLOBALS['fc_config']['db']['pref']."bans")!==FALSE ||
					strpos($entry, '.htaccess')!==FALSE ||
					strpos($entry, 'tables_id')!==FALSE ||
					strpos($entry, 'update')!==FALSE ||
					$entry == '.' ||
					$entry == '..' ||
					$entry=='index.html'
				  )
				continue;
				
				
				if( strpos($entry, $GLOBALS['fc_config']['db']['pref']."messages")!==FALSE  )
				{
					$handle = fopen($cachePath.$entry, "r");
					//$elem = explode("_",$entry);
					
					//toLog("params fdjo gijdfo gijdfo gijdfij",$params);
					//$params['id'] = $params[3];
					//$this->setFilePos($handle,$params,$elem[2]);
					
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
			return $allMsg;
			
		}
		function processMessages( $params )
		{
			switch($this->queryStr)//
			{
				case "DELETE FROM {$GLOBALS['fc_config']['db']['pref']}messages WHERE created < DATE_SUB(NOW(),INTERVAL ? SECOND)": 
					return null;
					break;
				case "INSERT INTO {$GLOBALS['fc_config']['db']['pref']}messages (created, toconnid, touserid, toroomid, command, userid, roomid, txt) VALUES (?, ?, ?, ?, ?, ?, ?, ?)":
					return $this->saveMessagesInCache( $params );	
					//return null;
					break;
				case "SELECT userid FROM {$GLOBALS['fc_config']['db']['pref']}messages where command=? or command=? and userid is not null order by userid":
					return new ResultSet1($this->selectMsgAdmin1($params));
					break;
				case "SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}messages WHERE toconnid=? AND id>=? ORDER BY id":
					return new ResultSet1($this->selectMsg($params));
					break;
				case "SELECT id FROM {$GLOBALS['fc_config']['db']['pref']}messages WHERE command='msg' AND toroomid=? AND created > DATE_SUB(NOW(),INTERVAL 5 MINUTE) ORDER BY id LIMIT 1":
					return new ResultSet1($this->selectMsgId($params));
					break;
				default://
					if( strpos($this->queryStr,"SELECT msgs.*, DATE_FORMAT(DATE_ADD")!==false )
					{
						return new ResultSet1($this->selectMsgAdmin2($params));					
					}
					else
					{
						if( ($rows=$this->messageIsCached($params)) !== FALSE )
						{
							return new CachedResultSet($rows);
						}
						else
						{
							return new ResultSet1($this->selectMsgLogin($params));
						}
						break;
					}
			}
			return null;
			
		}
		function selectConn( $out='', $in='',$params)
		{
			$file_name = $this->getCachFileName('Connections');
			$handle = fopen($file_name, "r");
			$total = '';
			$allUsers = array();		
			
			while (!feof($handle))
			{
    			$buffer = fgets( $handle );
				$array = explode("\t",$buffer);
				if( $array[3]==$params[0] )
				{
					$array['id'] = 	$array[0];
					unset($array[0]);unset($array[1]);unset($array[2]);unset($array[3]);unset($array[4]);unset($array[5]);unset($array[6]);
					unset($array[7]);unset($array[8]);unset($array[9]);unset($array[10]);
					$allUsers[] = $array;
					break;
				}
			}
			@fclose($handle);		
			return $allUsers;
		}		
		function selectMsgLogin( $params )
		{
			//$file_name = $this->getCachFileName('Messages');
			
			$cacheDir = $this->getCachDir();
			$cachePath = $cacheDir->path;
			
			$total = '';
			$allMsg = array();
					
			while (false !== ($entry = $cacheDir->read())) 
			{
				if(
					strpos($entry, 'messages_stats')!==FALSE ||
					strpos($entry, $GLOBALS['fc_config']['db']['pref']."rooms")!==FALSE ||
					strpos($entry, $GLOBALS['fc_config']['db']['pref']."ignors")!==FALSE ||
					strpos($entry, $GLOBALS['fc_config']['db']['pref']."connections")!==FALSE ||
					strpos($entry, $GLOBALS['fc_config']['db']['pref']."users")!==FALSE ||
					strpos($entry, $GLOBALS['fc_config']['db']['pref']."bans")!==FALSE ||
					strpos($entry, '.htaccess')!==FALSE ||
					strpos($entry, 'tables_id')!==FALSE ||
					strpos($entry, 'update')!==FALSE ||
					$entry == '.' ||
					$entry == '..' ||
					$entry=='index.html'
				  )
				continue;
				
				
				if( strpos($entry, $GLOBALS['fc_config']['db']['pref']."messages")!==FALSE  )
				{
					$handle = fopen($cachePath.$entry, "r");
					$elem = explode("_",$entry);
					
					$params['id'] = $params[3];
					$this->setFileMsgPos($handle,$params,$elem[2]);
					
					
					while (!feof($handle))
					{
    					$buffer = fgets($handle);
						
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
			
			return $allMsg;
		}
		function selectMsg( $params )
		{
			//$file_name = $this->getCachFileName('Messages');
			
			
			$cacheDir = $this->getCachDir();
			$cachePath = $cacheDir->path;
			
			$total = '';
			$allMsg = array();
					
			while (false !== ($entry = $cacheDir->read())) 
			{
				if(
					strpos($entry, 'messages_stats')!==FALSE ||
					strpos($entry, $GLOBALS['fc_config']['db']['pref']."rooms")!==FALSE ||
					strpos($entry, $GLOBALS['fc_config']['db']['pref']."ignors")!==FALSE ||
					strpos($entry, $GLOBALS['fc_config']['db']['pref']."connections")!==FALSE ||
					strpos($entry, $GLOBALS['fc_config']['db']['pref']."users")!==FALSE ||
					strpos($entry, $GLOBALS['fc_config']['db']['pref']."bans")!==FALSE ||
					strpos($entry, '.htaccess')!==FALSE ||
					strpos($entry, 'tables_id')!==FALSE ||
					strpos($entry, 'update')!==FALSE ||
					$entry == '.' ||
					$entry == '..' ||
					$entry=='index.html'
				  )
				continue;
				
				
				if( strpos($entry, $GLOBALS['fc_config']['db']['pref']."messages")!==FALSE  )
				{
					$handle = fopen($cachePath.$entry, "r");
					
					
					$elem = explode("_",$entry);
					
					$params['id'] = $params[1];
					$handle = $this->setFileMsgPos($handle,$params,$elem[2]);
				
					while (!feof($handle))
					{
    					$buffer = fgets($handle);
						
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
			
			return $allMsg;
		}
		function selectConnLogin( $params )
		{
			$file_name = $this->getCachFileName('Connections');
			$handle = fopen($file_name, "r");
			$total = '';
			$allUsers = array();
			while (!feof($handle))
			{
    			$buffer = fgets( $handle );
				$array = explode("\t",$buffer);
				if( $array[0]!=$params[0] && ''!=trim($array[3]) )
				{
					$array['id'] = $array[0];				
					$array['updated'] = $array[1];
					$array['created'] = $array[2];
					$array['userid'] = $array[3];
					$array['roomid'] = $array[4];
					$array['color'] = $array[5];
					$array['state'] = $array[6];
					$array['start'] = $array[7];
					$array['lang'] = $array[8];
					$array['ip'] = 	$array[9];
					$array['tzset'] = $array[10];
					unset($array[0]);unset($array[1]);unset($array[2]);unset($array[3]);unset($array[4]);unset($array[5]);unset($array[6]);
					unset($array[7]);unset($array[8]);unset($array[9]);unset($array[10]);
					$allUsers[] = $array;
				}
			}
			@fclose($handle);		
			return $allUsers;
		}
		
		function readFromDB( $queryParams )
		{
			return null;
		}
		
		function getRooms( $str )
		{
			$file_name = $this->getCachFileName('Rooms');
			$handle = fopen($file_name, "r");
			
			$total = '';
			$allRooms = array();
			while (!feof($handle))
			{
    			$buffer = fgets($handle);
				
				if( $buffer=='' )
					continue;
				
				$array = explode("\t",$buffer);
				if( $array[0]=='' )
					continue;
				$array['id'] = 	$array[0];				
				$array['ispermanent'] = $array[6];
					
				unset($array[0]);unset($array[1]);unset($array[2]);unset($array[3]);unset($array[4]);unset($array[5]);unset($array[6]);
					unset($array[7]);unset($array[8]);unset($array[9]);unset($array[10]);
				$allRooms[] = $array;
				
			}
			@fclose($handle);
			return $allRooms;
		} 
		function getRoomsPerm()
		{
			$file_name = $this->getCachFileName('Rooms');
			$handle = fopen($file_name, "r");
			
			$total = '';
			$allRooms = array();
			while (!feof($handle))
			{
    			$buffer = fgets($handle);
				
				if( $buffer=='' )
					continue;
				
				$array = explode("\t",$buffer);
				if( $array[0]=='' )
					continue;
								
				$array['ispermanent'] = $array[6];
					
				unset($array[0]);unset($array[1]);unset($array[2]);unset($array[3]);unset($array[4]);unset($array[5]);unset($array[6]);
					unset($array[7]);unset($array[8]);unset($array[9]);unset($array[10]);
				$allRooms[] = $array;
				
			}
			@fclose($handle);
			return $allRooms;
		}
		function processRoomsAll1( $output='' , $input='' , $params=array() )
		{
			$file_name = $this->getCachFileName('Rooms');
			$handle = fopen($file_name, "r");
			$total = '';
			$allRooms = array();
			while (!feof($handle))
			{
    			$buffer = fgets($handle);
				
				if( $buffer=='' )
					continue;
				
				$array = explode("\t",$buffer);
				
				if( $input=='' )
				{
					if( $array[5]='' && $array[6]=='')
						continue;
				}elseif( $input=='id' )
				{
					if( $array[0]==$params[0])
					{
						$array['id'] = $array[0];				
						$array['updated'] = $array[1];
						$array['created'] = $array[2];				
						$array['name'] = $array[3];
						$array['password'] = $array[4];
						$array['ispublic'] = $array[5];					
						$array['ispermanent'] = $array[6];
				
						unset($array[0]);unset($array[1]);unset($array[2]);unset($array[3]);unset($array[4]);unset($array[5]);unset($array[6]);
					
						$allRooms[] = $array;
						break;
					}
					continue;
				}
				
				$array['id'] = $array[0];				
				$array['updated'] = $array[1];
				$array['created'] = $array[2];				
				$array['name'] = $array[3];
				$array['password'] = $array[4];
				$array['ispublic'] = $array[5];					
				$array['ispermanent'] = $array[6];
				
				unset($array[0]);unset($array[1]);unset($array[2]);unset($array[3]);unset($array[4]);unset($array[5]);unset($array[6]);unset($array[7]);
					
				$allRooms[] = $array;
				
			}
			@fclose($handle);
			
			return $allRooms;
		}
		function processRoomsAll( $output='' , $input='' , $params=array() )
		{
			$file_name = $this->getCachFileName('Rooms');
			$handle = fopen($file_name, "r");
			$total = '';
			$allRooms = array();
			while (!feof($handle))
			{
    			$buffer = fgets($handle);
				if( $buffer=='' )
					continue;
				$array = explode("\t",$buffer);
				
				if( $input=='' )
				{
					if( $array[5]='' && $array[6]=='')
						continue;
				}elseif( $input=='id' )
				{
					if( $array[0]==$params[0])
					{
						$array['id'] = $array[0];				
						$array['updated'] = $array[1];
						$array['created'] = $array[2];				
						$array['name'] = $array[3];
						$array['password'] = $array[4];
						$array['ispublic'] = $array[5];					
						$array['ispermanent'] = $array[6];
				
						unset($array[0]);unset($array[1]);unset($array[2]);unset($array[3]);unset($array[4]);unset($array[5]);unset($array[6]);
					
						$allRooms[] = $array;
						break;
					}
					continue;
				}
				
				$array['id'] = $array[0];				
				$array['updated'] = $array[1];
				$array['created'] = $array[2];				
				$array['name'] = $array[3];
				$array['password'] = $array[4];
				$array['ispublic'] = $array[5];					
				$array['ispermanent'] = $array[6];
				
				unset($array[0]);unset($array[1]);unset($array[2]);unset($array[3]);unset($array[4]);unset($array[5]);unset($array[6]);unset($array[7]);
					
				$allRooms[] = $array;
				
			}
			@fclose($handle);

			return $allRooms;
		}
		function processRmsAll(  )
		{
			$file_name = $this->getCachFileName('Rooms');
			$handle = fopen($file_name, "r");
			$total = '';
			$allRooms = array();
			while (!feof($handle))
			{
    			$buffer = fgets($handle);
				
				if( $buffer=='' )
					continue;
				
				$array = explode("\t",$buffer);				
				
				$array['id'] = $array[0];				
				$array['updated'] = $array[1];
				$array['created'] = $array[2];				
				$array['name'] = $array[3];
				$array['password'] = $array[4];
				$array['ispublic'] = $array[5];					
				$array['ispermanent'] = $array[6];
				
				unset($array[0]);unset($array[1]);unset($array[2]);unset($array[3]);unset($array[4]);unset($array[5]);unset($array[6]);unset($array[7]);
					
				$allRooms[] = $array;
				
			}
			@fclose($handle);			
			return $allRooms;
		}
		function processInsertUser( $params )
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
			@fclose($file);
			return $id;
		}
		function processProfile( $params )
		{
			$file_name = $this->getCachFileName('Users');
			$handle = fopen($file_name, "r");
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
			
			return $allUsers;
		}
		function processUpdateProf( $params )
		{
			$file_name = $this->getCachFileName('Users');
			$handle = fopen($file_name, "r");
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
			@fclose($file);
			return true;
		}
		function processUsers( $params )//
		{
			
			if( $this->queryStr == "SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}users WHERE login=? LIMIT 1" )
			{
				return new ResultSet1($this->processUser('*','login',$params));
			}
			elseif( $this->queryStr == "SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}users WHERE id=? LIMIT 1" )
			{
				return new ResultSet1($this->processUser('*','id',$params));
			}//
			elseif( $this->queryStr == "SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}users WHERE id=?" )
			{
				return new ResultSet1($this->processUser('+','id',$params));
			}
			elseif( strpos($this->queryStr,"profile <> '' ORDER BY login") )
			{
				return new ResultSet1($this->processUserProfAll($params));
			}
			elseif( $this->queryStr == "SELECT profile FROM {$GLOBALS['fc_config']['db']['pref']}users WHERE id=?" )
			{
				return new ResultSet1($this->processProfile( $params ));
			}
			elseif( $this->queryStr == "SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}users ORDER BY login" )
			{
				return new ResultSet1($this->processUserLogin());
			}
			elseif( $this->queryStr == "INSERT INTO {$GLOBALS['fc_config']['db']['pref']}users (login, roles) VALUES(?, ?)" )
			{
				return $this->processInsertUser($params);
			}//
			elseif( strpos($this->queryStr,"INSERT INTO ")!==false && strpos($this->queryStr,"password")!==false  )
			{
				return $this->processInsertUserPass($params);
			}
			elseif( strpos($this->queryStr,"SELECT count(*) users_amount")!==false )
			{
				return new ResultSet1($this->processUserCount( 'users_amount',$params ));
			}
			elseif( $this->queryStr == "UPDATE {$GLOBALS['fc_config']['db']['pref']}users SET profile=? WHERE id=?" )
			{
				return $this->processUpdateProf($params);
			}
			elseif( $this->queryStr == "DELETE FROM {$GLOBALS['fc_config']['db']['pref']}users WHERE id=?" )
			{
				return $this->processDelUser($params);
			}
			else
			{
				return null;
			}
		}
		function processDelUser( $params )
		{
			$file_name = $this->getCachFileName('Users');
			$file = @fopen($file_name,'r');
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
			@fclose($file);
			$file = @fopen($file_name,'w');
			@fwrite($file, $str);
			@fclose($file);
			return true;
		}
		function processInsertUserPass( $params )
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
			@fclose($file);
			return $id;
		}
		function processUserProfAll( $params )
		{
			$file_name = $this->getCachFileName('Users');
			$handle = fopen($file_name, "r");
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
			
			return $allUsers;
		}
		function processUserLogin()
		{
			$file_name = $this->getCachFileName('Users');
			$handle = fopen($file_name, "r");
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
			
			return $allUsers;
		}
		function processUserCount( $out='',$params )
		{
			$file_name = $this->getCachFileName('Users');
			$handle = fopen($file_name, "r");
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
			
			$allUsers[0][$out] = $count;
			
			return $allUsers;
		}
		function processUser( $output='' , $input='' , $params = array() )
		{
			$file_name = $this->getCachFileName('Users');
			$handle = fopen($file_name, "r");
			$total = '';
			$allUsers = array();
			while (!feof($handle))
			{
    			$buffer = fgets($handle);
				$array = explode("\t",$buffer);
				if( $output=='*' )
				{
					if( $input=='login' )
					{
						if( $array[1]==$params[0] )
						{
							$allUsers[0]['id'] = $array[0];
							$allUsers[0]['login'] = $array[1];
							$allUsers[0]['password'] = $array[2];
							$allUsers[0]['roles'] = $array[3];
							$allUsers[0]['profile'] = $array[4];
							break;
						}
					}
					if( $input=='id' )
					{
						if( $array[0]==$params[0] )
						{
							$allUsers[0]['id'] = $array[0];
							$allUsers[0]['login'] = $array[1];
							$allUsers[0]['password'] = $array[2];
							$allUsers[0]['roles'] = $array[3];
							$allUsers[0]['profile'] = $array[4];
							break;
						}
					}
				}
				else
				{
					if( $array[0]==$params[0] )
						{
							$array['id'] = $array[0];
							$array['login'] = $array[1];
							$array['password'] = $array[2];
							$array['roles'] = $array[3];
							$array['profile'] = $array[4];
							$allUsers[] = $array;
						}
				}
				
			}
			
			return $allUsers;
		}
		function processIgnors( $params )//
		{
			if( $this->queryStr=="INSERT INTO {$GLOBALS['fc_config']['db']['pref']}ignors (created, userid, ignoreduserid) VALUES (NOW(), ?, ?)" )
			{
				return $this->insertIgnors( $params );
			}
			elseif( $this->queryStr=="SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}ignors WHERE userid=? AND ignoreduserid=?" )
			{
				return new ResultSet1($this->getIgnors( $params ));
			}
			elseif( $this->queryStr=="SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}ignors WHERE ignoreduserid=?" )
			{
				return new ResultSet1($this->getIgnorsComm( $params ));
			}
			elseif( $this->queryStr=="SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}ignors ORDER BY userid" )
			{
				return new ResultSet1($this->getIgnorsUser( $params ));
			}
			elseif( $this->queryStr=="SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}ignors WHERE userid=?" )
			{
				return new ResultSet1($this->getIgnorsCommUser( $params ));
			}
			elseif( $this->queryStr=="DELETE FROM flashchat_ignors WHERE userid=? AND ignoreduserid=?" )
			{
				return new ResultSet1($this->deleteIgnorsUser( $params ));
			}
		}
		function deleteIgnorsUser( $params )
		{
			$file_name = $this->getCachFileName('Ignors');
			
			
			$handle = fopen($file_name, "r");
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
			fclose($handle);
			$handle = fopen($file_name, "w");
			fwrite($handle,$total);
			fclose($handle);
			return true;
		}
		function getIgnorsUser()
		{
			$file_name = $this->getCachFileName('Ignors');
			$handle = fopen($file_name, "r");
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
					unset($array[0]);
					unset($array[1]);
					unset($array[2]);
					$allUsers[] = $array;				
			}
			
			return $allUsers;
		}
		function getIgnorsCommUser( $params )
		{
			$file_name = $this->getCachFileName('Ignors');
			$handle = fopen($file_name, "r");
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
					unset($array[0]);
					unset($array[1]);
					unset($array[2]);
					$allUsers[] = $array;
				}
				
				
			}
			
			return $allUsers;
		}
		function getIgnorsComm( $params )
		{
			$file_name = $this->getCachFileName('Ignors');
			$handle = fopen($file_name, "r");
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
					unset($array[0]);
					unset($array[1]);
					unset($array[2]);
					$allUsers[] = $array;
				}
				
				
			}
			
			return $allUsers;
		}
		function getIgnors( $params )
		{
			$file_name = $this->getCachFileName('Ignors');
			$handle = fopen($file_name, "r");
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
					unset($array[0]);
					unset($array[1]);
					unset($array[2]);
					$allUsers[] = $array;
				}
				
				
			}
			
			return $allUsers;
		}
		function insertIgnors( $params )
		{
			$file_name = $this->getCachFileName('Ignors');
			if( $file_name == null )
			{
				$cacheDir = $this->getCachDir();
				$cachePath = $cacheDir->path;
				$file_name = $cachePath.$GLOBALS['fc_config']['db']['pref'].'ignors_'.$GLOBALS['fc_config']['cacheFilePrefix'].'.txt';
			}
			
			$file = @fopen($file_name,'a');
			//$id = $this->file_insert_id(6);
			$str = date("Y-m-d H:i:s")."\t".$params[0]."\t".$params[1]."\t\n";
			@fwrite($file, $str);
			@fclose($file);
			return true;
		}
		function processBans( $params )//
		{
			
			if( $this->queryStr=="SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}bans WHERE (banneduserid=? OR ip=?) AND roomid IS NULL" )
			{
				return new ResultSet1($this->getBans( $params ));
			}
			elseif( strpos($this->queryStr, 'INSERT INTO')!==FALSE )
			{
				return $this->insertBans( $params );
			}
			elseif( $this->queryStr=="SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}bans WHERE banneduserid=? AND roomid=?" )
			{
				return new ResultSet1($this->getBansRoom( $params ));
			}
			elseif( $this->queryStr=="SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}bans ORDER BY userid" )
			{
				return new ResultSet1($this->getBansUser( $params ));
			}
			elseif( $this->queryStr=="SELECT roomid FROM {$GLOBALS['fc_config']['db']['pref']}bans WHERE banneduserid=?" )
			{
				return new ResultSet1($this->getBansRoomId( $params ));
			}
		}
		function getBansUser()
		{
			$file_name = $this->getCachFileName('Bans');
			$handle = fopen($file_name, "r");
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
			return $allUsers;
		}
		function getBansRoom( $params )
		{
			$file_name = $this->getCachFileName('Bans');
			$handle = fopen($file_name, "r");
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
			return $allUsers;
		}
		function getBansRoomId( $params )
		{
			$file_name = $this->getCachFileName('Bans');
			$handle = fopen($file_name, "r");
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
			return $allUsers;
		}
		function getBans( $params )
		{
			$file_name = $this->getCachFileName('Bans');
			$handle = fopen($file_name, "r");
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
			return $allUsers;
		}
		function insertBans( $params )
		{
			$file_name = $this->getCachFileName('Bans');
			if( $file_name == null )
			{
				$cacheDir = $this->getCachDir();
				$cachePath = $cacheDir->path;
				$file_name = $cachePath.$GLOBALS['fc_config']['db']['pref'].'bans_'.$GLOBALS['fc_config']['cacheFilePrefix'].'.txt';
			}
			$file = @fopen($file_name,'a');
			$str = date("Y-m-d H:i:s")."\t".$params[0]."\t".$params[1]."\t".$params[2]."\t".$params[3]."\t".$params[4]."\t\n";
			
			@fwrite($file, $str);
			@fclose($file);
			return true;
		}
		function getNumCount( $params )
		{
			$rooms_file_name = $this->getCachFileName('Rooms');
			$connections_file_name = $this->getCachFileName('Connections');
			$count = 0;
			$rooms_file = file($rooms_file_name);
			$connections_file = file($connections_file_name);
			
			$records_to_update = array();
			if( $rooms_file != FALSE && $connections_file != FALSE )
			{
				if( !isset($params[1]) )
				{
					foreach( $connections_file as $key=>$val )
					{
						$conn = explode( "\t" , $val );
						if( $conn[3]!='' && $conn[3]!=$params[0] )
						{
							foreach( $rooms_file as $k=>$v )
							{
								$room = explode( "\t" , $v );
								if( $room[0] == $conn[4] && $room[5]!='' )
								{
									$count++;
								}
							}
						}
					}
				}
				else
				{
					foreach( $connections_file as $key=>$val )
					{
						$conn = explode( "\t" , $val );
						if( $conn[4]==$params[1] && $conn[3]!=$params[0] )
						{
							$count++;
						}
					}
				}
			}
			
			$result = array();
			$result[]['numb'] = $count;
			
			//toLog("result",$result);
			
			return $result;
		}
		function updateRoom()
		{
			$connections_file_name = $this->getCachFileName('Connections');
			$rooms_file_name = $this->getCachFileName('Rooms');
			$rooms_file = file($rooms_file_name);
			$connections_file = file($connections_file_name);
			$room = array();
			if( $connections_file != FALSE )
			{
				foreach( $connections_file as $k=>$v )
				{
					$t = explode("\t",$v);
					$bool = false;
					foreach( $rooms_file as $k1=>$v1  )
					{
						$t1 = explode("\t",$v1);
						if( $t1[0]==$t[4] && $t1[6]=='')
							$bool = true;
					}
					if( $bool )
						$room[$t[4]] = $t[4];
				}
				foreach( $room as $k=>$v )
				{
					$filename = $GLOBALS['fc_config']['cachePath'].'updroom_'.$v.'_'.$GLOBALS['fc_config']['cacheFilePrefix'].'_.txt';
					$file = fopen($filename, "w");
					fwrite($file, time());
					fclose($file);
				}
			}
		}
		function processRoomsOrder()
		{
			$file_name = $this->getCachFileName('Rooms');
			$handle = fopen($file_name, "r");
			$total = '';
			$allRooms = array();
			while (!feof($handle))
			{
    			$buffer = fgets($handle);
				if( $buffer=='' )
					continue;
				$array = explode("\t",$buffer);
				
				$array['id'] = $array[0];				
				$array['updated'] = $array[1];
				$array['created'] = $array[2];				
				$array['name'] = $array[3];
				$array['password'] = $array[4];
				$array['ispublic'] = $array[5];					
				$array['ispermanent'] = $array[6];
				
				unset($array[0]);unset($array[1]);unset($array[2]);unset($array[3]);unset($array[4]);unset($array[5]);unset($array[6]);unset($array[7]);
				$allRooms[] = $array;
			}
			
			return $allRooms;
		}
		
		function processRoomsOrderPerman()
		{
			$file_name = $this->getCachFileName('Rooms');
			$handle = fopen($file_name, "r");
			$total = '';
			$allRooms = array();
			while (!feof($handle))
			{
    			$buffer = fgets($handle);
				if( $buffer=='' )
					continue;
				$array = explode("\t",$buffer);
				
				//if( $array[6]!='' )
				//{
					$array['id'] = $array[0];				
					$array['updated'] = $array[1];
					$array['created'] = $array[2];				
					$array['name'] = $array[3];
					$array['password'] = $array[4];
					$array['ispublic'] = $array[5];					
					$array['ispermanent'] = $array[6];
				//}
				
				unset($array[0]);unset($array[1]);unset($array[2]);unset($array[3]);unset($array[4]);unset($array[5]);unset($array[6]);unset($array[7]);
				$allRooms[] = $array;
			}
	
			function cmp($a, $b) 
			{
    		
				if ($a['ispermanent'] == $b['ispermanent']) {
        			return 0;
    			}
    			return ($b['ispermanent'] == '') ? -1 : 1;
			}
			usort($allRooms, "cmp");

			return $allRooms;
		}
		
		function selFromAllBase()
		{
			//$file_name = $this->getCachFileName('Messages');
			
			$cacheDir = $this->getCachDir();
			$cachePath = $cacheDir->path;
			
			$total = '';
			$allMsg = array();
			while (false !== ($entry = $cacheDir->read())) 
			{
				if(
					strpos($entry, 'messages_stats')!==FALSE ||
					strpos($entry, $GLOBALS['fc_config']['db']['pref']."rooms")!==FALSE ||
					strpos($entry, $GLOBALS['fc_config']['db']['pref']."ignors")!==FALSE ||
					strpos($entry, $GLOBALS['fc_config']['db']['pref']."connections")!==FALSE ||
					strpos($entry, $GLOBALS['fc_config']['db']['pref']."users")!==FALSE ||
					strpos($entry, $GLOBALS['fc_config']['db']['pref']."bans")!==FALSE ||
					strpos($entry, '.htaccess')!==FALSE ||
					strpos($entry, 'tables_id')!==FALSE ||
					strpos($entry, 'update')!==FALSE ||
					$entry == '.' ||
					$entry == '..' ||
					$entry=='index.html'
				  )
				continue;
				
				
				if( strpos($entry, $GLOBALS['fc_config']['db']['pref']."messages")!==FALSE  )
				{
					$handle = fopen($cachePath.$entry, "r");
			
					//$total = '';
					//$allMsg = array();
					while (!feof($handle))
					{
    					$buffer = fgets($handle);
						if( $buffer=='' )
							continue;
						$array = explode("\t",$buffer);
				
						if(	$array[5] == 'adu' || $array[5] == 'rmu' || $array[5] == 'mvu' )
						{					
							$array['created'] = $array[1];					
							$array['command'] = $array[5];
							$array['userid'] = $array[6];
							$array['roomid'] = $array[7];
					
					
					
							$file_name = $this->getCachFileName('Rooms');
							$arrayRoom = file( $file_name );
							
							$toRoomStr = '';
							$fromRoomStr = '';
							
							
							foreach( $arrayRoom as $key=>$val )
							{
								$room_elems = explode("\t", $val);
								
								if( $room_elems[0]==$array['roomid'] )
								{
									$toRoomStr = $room_elems[3];	
									break;				
								}
							}
							
							$array['name'] = $toRoomStr;
							
							$file_name = $this->getCachFileName('Users');
							$arrayRoom = file( $file_name );
							
							$login = '';
							$roles = '';
					
					
							foreach( $arrayRoom as $key=>$val )
							{
								$room_elems = explode("\t", $val);
								
								if( $room_elems[0]==$array['userid'] )
								{
									$login = $room_elems[1];
									$roles = $room_elems[3];	
									break;				
								}
							}
							
							$array['login'] = $login;
							$array['roles'] = $roles;
							
							unset($array[0]);unset($array[1]);unset($array[2]);unset($array[3]);unset($array[4]);unset($array[5]);unset($array[6]);unset($array[7]);unset($array[8]);unset($array[9]);
							$allMsg[] = $array;
						}
					}
					@fclose($handle);	
				}
			}
			
			
			return $allMsg;			
		}
		function updateRoomsInCache( $params )
		{
			//if( isset($params) )
				//return true;
			//$arr = explode("=",$this->queryStr);
			//toLog("arr",$arr);
			$first_str = substr($this->queryStr,strpos($this->queryStr,"name="),strpos($this->queryStr," WHERE")-strpos($this->queryStr,"name="));
			$arr = explode(",",$first_str);
			
			
			foreach( $arr as $k=>$v )
			{
				$res = explode("=",$v);
				
				$res[1] = str_replace("'","",$res[1]);
				$res[1] = trim($res[1]);
				$res[0] = trim($res[0]);
				if( $res[1]=='null' || $res[1]=='NULL' )
					$res[1]='';
					
					
				switch( $res[0] )
				{
					case 'name': $name = $res[1];
						break;
					case 'password': $password = $res[1];
						break;
					case 'ispublic': $ispublic = $res[1];
						break;
					case 'ispermanent': $ispermanent = $res[1];
						break;
				}
				
			}
			$id = substr($this->queryStr,strpos($this->queryStr,"id=")+3);
			
			$file_name = $this->getCachFileName('Rooms');
			$handle = fopen($file_name, "r");
			$total = '';
			$allRooms = array();
			while (!feof($handle))
			{
    			$buffer = fgets($handle);
				if( $buffer=='' )
					continue;
				$array = explode("\t",$buffer);
				
				if( $array[0]==$id )
				{
								
					$array['updated'] = $array[1];
					$array['created'] = $array[2];				
					
					unset($array[0]);unset($array[3]);unset($array[4]);unset($array[5]);unset($array[6]);unset($array[7]);
					
					$total .= $id."\t".$array[1]."\t".$array[2]."\t".$name."\t".$password."\t".$ispublic."\t".$ispermanent."\t\n";				
					
				}
				else
					$total .= $buffer;
					
			}		
			@fclose($handle);
			$handle = fopen($file_name, "w");
			fwrite($handle,$total);
			@fclose($handle);
			return true;
		}
		function getRoomsIdMax()
		{
			$id = $this->file_insert_id(8,'1')+1;
			$arr = array();
			return $arr[]['newid'] = $id;
		}
		function processRoomsCount( $str = '' )
		{
			$file_name = $this->getCachFileName('Rooms');
			$arrayRoom = file( $file_name );
					
			$count = 0;					
			$result = array();
			foreach( $arrayRoom as $key=>$val )
			{
				$room_elems = explode("\t", $val);
				if( $str=='maxnumb' )
				{
					if( $room_elems[6]!='' )
					{
						$count++;
					}
				}
				elseif( $str=='maxnumb' )
				{
					if( $room_elems[0]>0 )
					{
						$count++;
					}
				}
			}
			$result[][$str] = $count;
			return $result;
		}
		function process(/*...*/) 
		{
			if(func_num_args() > 0) 
			{
				$params = func_get_args();
			} else 
			{
				$params = array();
			}

			$GLOBALS["query_count"]++;
			if(strpos($this->queryStr, "SELECT")!==FALSE)
			{
				$GLOBALS["select_count"]++;
			}
			else
			{
			}
						
			if( strpos($this->queryStr, "FROM flashchat_messages LEFT JOIN flashchat_users ON (flashchat_messages.userid = flashchat_users")!==FALSE )
			{
				return new ResultSet1($this->selFromAllBase( $params ));			
			}
			if( strpos($this->queryStr, "connections")!==FALSE )
			{
				if( strpos($this->queryStr, "SELECT COUNT(*) AS numb")!==FALSE )
				{
					return new ResultSet1($this->getNumCount( $params ));
				}
				elseif( strpos($this->queryStr, "UPDATE {$GLOBALS['fc_config']['db']['pref']}rooms,{$GLOBALS['fc_config']['db']['pref']}connections")!==FALSE )
				{
					$this->updateRoom();
				}
				else
					return $this->processConnect($params);
			}
			
			if( strpos($this->queryStr, "bans")!==FALSE )
			{
				return $this->processBans($params);
			}
			
			if( strpos($this->queryStr, "messages")!==FALSE )
			{			
				return $this->processMessages($params);			
			}
			if( strpos($this->queryStr, "ignors")!==FALSE )
			{
				return $this->processIgnors($params);
			}
			//
			if( strpos($this->queryStr, "rooms")!==FALSE )//
			{			
				if($this->queryStr == "SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}rooms ORDER BY id")
				{
					return new ResultSet1($this->processRoomsOrder());
				}
				elseif( strtoupper($this->queryStr) == strtoupper("SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}rooms order by ispermanent"))
				{
					return new ResultSet1($this->processRoomsOrderPerman());
				}
				elseif( strtoupper($this->queryStr) == strtoupper("SELECT id FROM {$GLOBALS['fc_config']['db']['pref']}rooms WHERE ispermanent IS NOT NULL ORDER BY ispermanent"))
				{
					return new ResultSet1($this->processRoomsOrderPerman1());
				}//
				elseif( strtoupper($this->queryStr) == strtoupper("SELECT count(*) as maxnumb FROM {$GLOBALS['fc_config']['db']['pref']}rooms WHERE ispermanent IS NOT NULL"))
				{
					return new ResultSet1($this->processRoomsCount('maxnumb'));
				}
				elseif( strtoupper($this->queryStr) == strtoupper("SELECT count(*) as rowcount FROM {$GLOBALS['fc_config']['db']['pref']}rooms WHERE id > 0"))
				{
					return new ResultSet1($this->processRoomsCount('rowcount'));
				}
				elseif($this->queryStr == "SELECT * {$GLOBALS['fc_config']['db']['pref']}rooms")
				{
					return new ResultSet1($this->processRoomsAll());
				}				
				elseif($this->queryStr == "SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}rooms")
				{
					return new ResultSet1($this->processRoomsAll());
				}
				elseif( strpos($this->queryStr, "INSERT INTO")!==FALSE )
				{
					return $this->saveRoomsInCache( $params );
				}
				elseif( strpos($this->queryStr, "UPDATE {$GLOBALS['fc_config']['db']['pref']}rooms")!==FALSE)
				{
					return $this->updateRoomsInCache( $params );
				}
				elseif($this->queryStr == "SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}rooms WHERE ispublic IS NOT NULL AND ispermanent IS NOT NULL ORDER BY ispermanent")
				{
					return new ResultSet1($this->processRoomsAll());
				}
				elseif($this->queryStr == "SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}rooms WHERE ispublic IS NOT NULL order by ispermanent")
				{
					return new ResultSet1($this->processRoomsAll1());
				}
				elseif($this->queryStr == "SELECT id FROM {$GLOBALS['fc_config']['db']['pref']}rooms WHERE ispermanent IS NULL AND updated < DATE_SUB(NOW(),INTERVAL ? SECOND)")
				{
					return new ResultSet1($this->processRoomsSel( $params ));
				}
				elseif($this->queryStr == "SELECT id, ispermanent FROM {$GLOBALS['fc_config']['db']['pref']}rooms")
				{
					return new ResultSet1($this->getRooms("id,ispermanent"));
				}
				elseif($this->queryStr == "SELECT ispermanent FROM {$GLOBALS['fc_config']['db']['pref']}rooms ORDER BY ispermanent")
				{
					return new ResultSet1($this->getRoomsPerm());
				}//
				elseif($this->queryStr == "SELECT id FROM {$GLOBALS['fc_config']['db']['pref']}rooms WHERE password=''")
				{
					return new ResultSet1($this->getRoomsIdPass());
				}
				elseif($this->queryStr == "SELECT MAX(id)+1 AS newid FROM {$GLOBALS['fc_config']['db']['pref']}rooms")
				{
					return new ResultSet1($this->getRoomsIdMax());
				}
				elseif( strpos($this->queryStr,"DELETE")!==false &&  strpos($this->queryStr,"?")!==true  )
				{
					$this->deleteRoomById();
					return true;
				}
				elseif($this->queryStr == "SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}rooms WHERE id=?")
				{
					if( ($rows=$this->roomsIsCached("*", "id=?", $params)) !== false)
					{
						return new CachedResultSet($rows);
					}
					else
					{
						return new ResultSet1($this->processRoomsAll("*","id",$params));
					}
				}
			}
			elseif( strpos($this->queryStr, "users")!==FALSE )
			{
				return $this->processUsers($params);
			}
		}
		function deleteRoomById()
		{
			$id = substr($this->queryStr,strpos($this->queryStr,"id=")+3);
			
			$file_name = $this->getCachFileName('Rooms');
			$handle = fopen($file_name, "r");
			$total = '';
			$allRooms = array();
			while (!feof($handle))
			{
    			$buffer = fgets($handle);
				$array = explode("\t",$buffer);
				if( $buffer=='' )
					continue;
					
				if( $array[0]!=$id )
					$total .= $buffer;
					
			}
		
			fclose($handle);
			$handle = fopen($file_name, "w");
			fwrite($handle,$total);
			fclose($handle);
		}
		function processRoomsOrderPerman1()
		{
			$file_name = $this->getCachFileName('Rooms');
			$handle = fopen($file_name, "r");
			$total = '';
			$allRooms = array();
			while (!feof($handle))
			{
    			$buffer = fgets($handle);
				if( $buffer=='' )
					continue;
				$array = explode("\t",$buffer);
				
				
					if( $array[6]!='')
					{
				
						$array['id'] = $array[0];				
						$array['updated'] = $array[1];
						$array['created'] = $array[2];				
						$array['name'] = $array[3];
						$array['password'] = $array[4];
						$array['ispublic'] = $array[5];					
						$array['ispermanent'] = $array[6];
				
						unset($array[0]);unset($array[1]);unset($array[2]);unset($array[3]);unset($array[4]);unset($array[5]);unset($array[6]);
					
						$allRooms[] = $array;
						
					}
					
				
				
				$array['id'] = $array[0];				
				$array['updated'] = $array[1];
				$array['created'] = $array[2];				
				$array['name'] = $array[3];
				$array['password'] = $array[4];
				$array['ispublic'] = $array[5];					
				$array['ispermanent'] = $array[6];
				
				unset($array[0]);unset($array[1]);unset($array[2]);unset($array[3]);unset($array[4]);unset($array[5]);unset($array[6]);unset($array[7]);
					
				$allRooms[] = $array;
				
			}
			@fclose($handle);

			return $allRooms;
		}
		function processRoomsSel( $params )
		{
			$cacheDir = $this->getCachDir();
			$cachePath = $cacheDir->path;
			$allRooms = array();
			$all = array();
			while (false !== ($entry = $cacheDir->read())) 
			{
				if(
					strpos($entry, 'messages_stats')!==FALSE ||
					strpos($entry, $GLOBALS['fc_config']['db']['pref']."rooms")!==FALSE ||
					strpos($entry, $GLOBALS['fc_config']['db']['pref']."ignors")!==FALSE ||
					strpos($entry, $GLOBALS['fc_config']['db']['pref']."connections")!==FALSE ||
					strpos($entry, $GLOBALS['fc_config']['db']['pref']."messages")!==FALSE ||
					strpos($entry, $GLOBALS['fc_config']['db']['pref']."users")!==FALSE ||
					strpos($entry, '.htaccess')!==FALSE ||
					strpos($entry, 'tables_id')!==FALSE ||
					strpos($entry, 'update')!==FALSE ||
					$entry == '.' ||
					$entry == '..' ||
					strpos($entry, 'index.html')!==FALSE
				  )
					continue;
					
				if( strpos($entry, 'updroom')!==FALSE )
				{
					$array = file($cachePath.$entry);
					$id = explode("_",$entry);
					if( time()-$array[0]>$params[0] )
					{
						$allRooms[]['id'] = $id[1];
						$all[] = $id[1];
						unlink($cachePath.$entry);
						
						
						//$handle = fopen($file_name, "r");
						
				
				
					}
				}
			}
			
			$file_name = $this->getCachFileName('Rooms');
			$array = file($file_name);
			$total = '';
					
			foreach( $array as $k2=>$v2 )
			{
				$ar = explode( "\t",$v2 );
				
				if( !in_array($ar[0], $all))
				{
					$total.=$v2;
				}
			}
			
			
			if( $total!='' )
			{
				$handle = fopen($file_name, "w");
				fwrite($handle, $total);
				fclose($handle);
			}
			return $allRooms;
		}
		function getRoomsIdPass()
		{
			$file_name = $this->getCachFileName('Rooms');
			$handle = fopen($file_name, "r");
			
			$total = '';
			$allRooms = array();
			while (!feof($handle))
			{
    			$buffer = fgets($handle);
				if( $buffer=='' )
					continue;
				$array = explode("\t",$buffer);
			
				if( $array[4]=='')
				{
					$array['id'] = $array[0];
				
					unset($array[0]);unset($array[1]);unset($array[2]);unset($array[3]);unset($array[4]);unset($array[5]);unset($array[6]);
			
					$allRooms[] = $array;
			
				}
			
			}
			@fclose($handle);
			return $allRooms;
		}
	}
	
	class CachedResultSet 
	{
		var $result;
		var $numRows = 0;
		var $currRow = 0;

		function CachedResultSet( $result = null ) 
		{
			$GLOBALS["cached_select_count"]++;
			$this->result = $result;
			
			if ( $result ) 
			{
				// determine $this->numRows from cached result
				$this->numRows = count($result);
			}
		}
		
		function hasNext()
		{
			return ($this->result && $this->numRows > $this->currRow);
		}
		
		function next() 
		{
			if($this->hasNext()) 
			{
				$this->currRow++;
				// return cached result?
				return $this->result[$this->currRow-1];
			} else {
				return null;
			}
		}
	}
	class ResultSet1 {
		var $result;
		var $numRows = 0;
		var $currRow = 0;

		function ResultSet1($result = null) 
		{

			$this->result = $result;
			if($result) $this->numRows = count($result);
		}

		function hasNext() 
		{
			return ($this->result && $this->numRows > $this->currRow);
		}

		function next() 
		{
			if($this->hasNext()) 
			{
				$this->currRow++;
				$array = $this->result[$this->currRow-1];
				return $array;
			} 
			else 
			{
				return null;
			}
		}
	}
?>