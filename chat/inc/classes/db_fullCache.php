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

		function Statement( $queryStr, $dosplit=true )
		{
			$this->queryArray = $dosplit ? split('\?', $queryStr) : array($queryStr);
			$this->type = strtolower(substr($queryStr, 0, 6));
			$this->queryStr = $queryStr;
			$this->result = array();
		}

		//Return max ID value from table
		function getRecordsCount($table_name)
		{
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
				$i = 0;
				while( !($rooms = file($roomsFileName)) )
				{
					//usleep(1000);//for linux
					$i++;
					if( $i>1000  )
						break;
				}
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
			if( strpos($file, "{$GLOBALS['fc_config']['db']['pref']}messages_")!==FALSE && $entry_elems[2] >= $params['id'])
				return true;

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
					$entry_elems[3] >= $params['id']
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
			//stream_set_timeout($point, 180);
			while ($line = fgets($point))
			{
				//$line = fgets($point);
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
			//stream_set_timeout($point, 180);
			while ($line = fgets($point))
			{
				//$line = fgets($point);
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
			$find_records = 0;
			//stream_set_timeout($point, 180);
			while ($line = fgets($point))
			{
				//$line = fgets($point);
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
			$tempParams = $queryParams;
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
			$boolean = false;
			if((int) $id_start > (int) $id_end)
			{
				return $result;
			}

			while (false !== ($entry = $cacheDir->read()))
			{
				if( $this->breakFile($entry) )
					continue;


				$entry_elems = explode('_', $entry);
				if( strpos($this->queryStr,"SELECT count(*) AS numb FROM {$GLOBALS['fc_config']['db']['pref']}messages WHERE command='msg'")!==false )
				{
					$is_cmd = false;
					$handle = @fopen($cachePath.$entry, 'r');
					while (!feof($handle))
					{

						$line = fgets($handle);
						if( $entry_elems[0]==$tempParams[0] )
							$find_records++;
					}
					continue;
				}

				if( !$this->is_this_file($entry,$params))// &&  strpos($entry, 'messagescmd_')===false
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

				if( strpos($entry, 'messages_')!==false )
				{
					$is_cmd = true;
				}
				$handle = @fopen($cachePath.$entry, 'r');
				$tempArray = array();

				if( !$is_cmd )
					$this->setFilePos($handle,$params,$entry_elems[3]);
				else
				{
					$this->setFileMsgPos($handle,$params,$entry_elems[3]);
				}
				if( !$is_cmd )
				{
					//stream_set_timeout($handle, 180);
					while ($line = fgets($handle))
					{
						if( $line=='' )
							continue;
						$line_elems = explode('#', $line);
						$id = (int) $line_elems[0];
						$created = $line_elems[1];
						$roomid = (int) $line_elems[2];
						if($id_start<=$id && $id<=$id_end)
						{
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
				}
				else
				{
					//stream_set_timeout($handle, 180);
					while ($line = fgets($handle))
					{
						if( $line=='' )
							continue;
						$line_elems = explode("\t", $line);

						if( $line_elems[5]=='lout' && $params['toconnid']!=$line_elems[2])//
							continue;

						if( ($params['toconnid']==$line_elems[2] || $params['touserid']==$line_elems[3] || $params['toroomid']==$line_elems[4]) || ($line_elems[2]=="" && $line_elems[3]=="" && $line_elems[4]==''))
						{
							$id = (int) $line_elems[0];
							$created = $line_elems[1];
							$roomid = (int) $line_elems[7];
							if($id_start<=$id && $id<=$id_end)
							{
								$find_records++;
								//toLog("find_records",$find_records);
								$result_elem = array('id'=>$id, 'created'=>$created, 'touserid'=>$line_elems[3], 'command'=>$line_elems[5],'userid'=>$line_elems[6], 'roomid'=>$line_elems[7], 'txt'=>$line_elems[8]);
								$result[count($result)] = $result_elem;
							}
						}
						else
							continue;
					}
				}
				fclose($handle);
			}

			if( strpos($this->queryStr,"SELECT count(*) AS numb")!==false )
			{

				$tempAr = array();
				$result = array();
				$tempAr[0]['numb'] = $find_records;

				$result = $tempAr;

				return $result;
			}



			if( $find_records>0 )
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

				return $result;
			}
			else
			{

				return false;
			}

		}

		//check if connections is cached
		function connectionsIsCached($columns, $condition, $queryParams)
		{
			$result = array();
			$connectionsFileName = $this->getCachFileName('Connections');
			if($connectionsFileName!=null)
			{
				$i = 0;
				while( !($connections = file($connectionsFileName)) )
				{
					usleep(1000);//for linux
					$i++;
					if( $i>1000  )
						break;
				}


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
					//$str = implode( "\t",$params );
					$str = $params[0]."\t".$params[1]."\t".$params[2]."\t".$params[3]."\t".$params[4]."\t".$params[5]."\t".$params[6]."\t".$params[7];
					$id = $this->file_insert_id(7);

					$pos = filesize( $cachePath.$entry );

					@fwrite($file,"$id\t".$str."\t1\t$pos\t\n");
					fflush($file);
					@fclose($file);

					rename($cachePath.$entry,$cachePath.$GLOBALS['fc_config']['db']['pref'].'messages_'.$id.'_'.$pos.'_'.$GLOBALS['fc_config']['cacheFilePrefix'].'.txt');

					return $id;
				}
			}
			return null;

		}

		function saveRoomsInCache( $queryParams )
		{
			$params = $queryParams;
			if($this->queryStr == "UPDATE {$GLOBALS['fc_config']['db']['pref']}rooms,{$GLOBALS['fc_config']['db']['pref']}connections SET {$GLOBALS['fc_config']['db']['pref']}rooms.updated=NOW() WHERE {$GLOBALS['fc_config']['db']['pref']}rooms.id = {$GLOBALS['fc_config']['db']['pref']}connections.roomid")
			{
				$rooms_file_name = $this->getCachFileName('Rooms');
				$connections_file_name = $this->getCachFileName('Connections');

				$i = 0;
				while( !($rooms_file = file($rooms_file_name)) )
				{
					//usleep(1000);//for linux
					$i++;
					if( $i>1000  )
						break;
				}

				$i = 0;
				while( !($connections_file = file($connections_file_name)) )
				{
					//usleep(1000);//for linux
					$i++;
					if( $i>1000  )
					break;
				}
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
					$file = @fopen($rooms_file_name, "w");
					for($i=0;$i<count($rooms_file);$i++)
					{
						@fwrite($file, $rooms_file[$i]);
					}
					fflush($file);
					fclose($file);
				}
			}
			else
			{
				//RESTORING flashchat_rooms_ .. txt file
				if( $this->queryStr!="UPDATE {$GLOBALS['fc_config']['db']['pref']}rooms SET updated=NOW() WHERE id=?" )
				{

					if($this->queryStr=="INSERT INTO {$GLOBALS['fc_config']['db']['pref']}rooms (created, name, password, ispublic, ispermanent) VALUES (NOW(), ?, ?, ?, ?)")
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
						if( $params[3]==1 )
							$params[3] = $id;
						else
							$params[3] = '';


						fwrite($file, $id."\t".date("Y-m-d H:i:s")."\t".date("Y-m-d H:i:s")."\t".$params[0]."\t".$params[1]."\t".$params[2]."\t".$params[3]."\t"."\n");
						fflush($file);
						fclose($file);

						/*$filename = $GLOBALS['fc_config']['cachePath'].'updroom_'.$id.'_'.$GLOBALS['fc_config']['cacheFilePrefix'].'_.txt';
						$file = @fopen($filename, "w");
						fwrite($file, time());
						fflush($file);
						fclose($file);*/

						return $id;
					}
					else
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
						fflush($file);
						fclose($file);

						$filename = $GLOBALS['fc_config']['cachePath'].'updroom_'.$id.'_'.$GLOBALS['fc_config']['cacheFilePrefix'].'_.txt';
						$file = @fopen($filename, "w");
						fwrite($file, time());
						fflush($file);
						fclose($file);

						return $id;
					}
				}
			}
		}

		//queryParams: params, passed to this->process function
		function saveConnectionsInCache($queryParams)
		{
			$file_name = $this->getCachFileName('Connections');
			if(($file_name = $this->getCachFileName('Connections')) != null)
			{
				$i = 0;
				while( !($file = file($file_name)) )
				{
					//usleep(1000);//for linux
					$i++;
					if( $i>1000  )
						break;
				}
			}
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
			}

			$writeFile = @fopen($file_name, 'w');
			for($i=0; $i<count($file); $i++)
			{
				@fwrite($writeFile, $file[$i]);
			}
			fflush($writeFile);
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
			fflush($file);
			fclose($file);
			if(!$replaced)
				array_push($newLines, $stat_name.'='.$stat_value."\n");

			$file = @fopen($fileName, 'w');
			if($file)
			{
				for($i=0; $i<count($newLines); $i++)
					@fwrite($file, $newLines[$i]);
				fflush($file);
				@fclose($file);
			}
		}
		//insert virtual id to file
		//0-bans;1-config;2-config_chats;3-config_instances;4-config_value;5-connections;6-ignors;7-messages;8-rooms;9-users
		function file_insert_id( $table,$view = '' )
		{
			$fname = $GLOBALS['fc_config']['cachePath'].'tables_id.txt';
			if( !file_exists( $fname ) )
			{
				$fp = @fopen($fname,"w+");
				@fwrite($fp, '0#0#0#0#0#0#0#0#4#0');
				fflush($fp);
				@fclose( $fp );
			}

			$i = 0;
			while( !($buffer = file($fname)) )
			{
				//usleep(1000);//for linux
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
				fflush($fp);
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
		function selectIfConn(  )
		{
			$cacheDir = $this->getCachDir();
			$cachePath = $cacheDir->path;
			$fname = $cachePath.$GLOBALS['fc_config']['db']['pref'].'connections_'.$GLOBALS['fc_config']['cacheFilePrefix'].'.txt';
			if( file_exists( $fname ) )
				return true;
			else
				return null;

		}
		function breakFile( $entry )
		{
			if(
			strpos($entry, 'messages_stats_')!==FALSE ||
	   		strpos($entry, "{$GLOBALS['fc_config']['db']['pref']}rooms_")!==FALSE ||
       		strpos($entry, "{$GLOBALS['fc_config']['db']['pref']}connections_")!==FALSE ||
	   		strpos($entry, "{$GLOBALS['fc_config']['db']['pref']}bans_")!==FALSE ||
	   		strpos($entry, "{$GLOBALS['fc_config']['db']['pref']}ignors_")!==FALSE ||
	   		strpos($entry, "{$GLOBALS['fc_config']['db']['pref']}users_")!==FALSE ||
	   		strpos($entry, 'index')!==FALSE ||
	   		strpos($entry, 'tables_id')!==FALSE ||
	   		strpos($entry, 'update')!==FALSE ||
	   		strpos($entry, 'updroom')!==FALSE ||
	   		$entry=='.htaccess' ||
	   		$entry=='.' ||
	   		$entry=='..'
		   )
				return true;
			else
				return false;
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
			fflush($handle);
			@fclose($handle);

			return $allRooms;
		}
		function processUser( $output='' , $input='' , $params = array() )
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
				if( $output=='*' )
				{
					if( $input=='login,id' )
					{
						if( $array[1]==$params[0] && $array[0]!=$params[1] )
						{
							$allUsers[0]['id'] = $array[0];
							$allUsers[0]['login'] = $array[1];
							$allUsers[0]['password'] = $array[2];
							$allUsers[0]['roles'] = $array[3];
							$allUsers[0]['profile'] = $array[4];
							break;
						}
					}
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
					if( $input=='' )
					{

							$all['id'] = $array[0];
							$all['login'] = $array[1];
							$all['password'] = $array[2];
							$all['roles'] = $array[3];
							$all['profile'] = $array[4];
							$allUsers[] = $all;

					}
				}
				else
				{
					if( $output=='*1' )
					{
						if( $array[1]==$params[0] )
							{
								$array['id'] = $array[0];
								$array['login'] = $array[1];
								$array['password'] = $array[2];
								$array['roles'] = $array[3];
								$array['profile'] = $array[4];
								$allUsers[] = $array;
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

			}
			fflush($handle);
			fclose($handle);

			return $allUsers;
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

			$i = 0;
			while( !($arrayRoom = file($file_name)) )
			{
				//usleep(1000);//for linux
				$i++;
				if( $i>1000  )
					break;
			}

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


			if( strpos($this->queryStr, "FROM {$GLOBALS['fc_config']['db']['pref']}messages LEFT JOIN {$GLOBALS['fc_config']['db']['pref']}users ON ({$GLOBALS['fc_config']['db']['pref']}messages.userid = {$GLOBALS['fc_config']['db']['pref']}users")!==FALSE )
			{
				return ( include(INC_DIR . 'classes/full_cache/selFromAllBase.php') );
			}
			if( strpos($this->queryStr, "connections")!==FALSE )
			{
				return ( include(INC_DIR . 'classes/full_cache/processConnect.php') );
			}
			if( strpos($this->queryStr, "bans")!==FALSE )
			{
				return ( include(INC_DIR . 'classes/full_cache/processBans.php') );
			}

			if( strpos($this->queryStr, "messages")!==FALSE )
			{
				return ( include(INC_DIR . 'classes/full_cache/processMessages.php') );
			}

			if( strpos($this->queryStr, "ignors")!==FALSE )
			{
				return ( include(INC_DIR . 'classes/full_cache/processIgnors.php') );
			}

			if( strpos($this->queryStr, "rooms")!==FALSE )
			{
				return (include(INC_DIR . 'classes/full_cache/processRoom.php'));
			}
			elseif( strpos($this->queryStr, "users")!==FALSE )
			{
				return (include(INC_DIR . 'classes/full_cache/processUsers.php'));
			}
		}
		function deleteRoomById()
		{
			$id = substr($this->queryStr,strpos($this->queryStr,"id=")+3);

			$file_name = $this->getCachFileName('Rooms');
			//$handle = fopen($file_name, "r");
			$i = 0;
	while( !($array = file($file_name)) )
	{
		//usleep(1000);//for linux
		$i++;
		if( $i>1000  )
			break;
	}

			$total = '';
			$allRooms = array();
toLog("array 1183 delete by id",$array);
			foreach($array as $k=>$v  )
			{
				$buffer = $v;
				$array = explode("\t",$buffer);
				if( $buffer=='' )
					continue;

				if( $array[0]!=$id )
					$total .= $buffer;

			}

			/*while (!feof($handle))
			{


			}*/
toLog("total 1201 delete by id",$total);
			//fclose($handle);
			$handle = @fopen($file_name, "w");
			fwrite($handle,$total);
			fflush($handle);
			fclose($handle);
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