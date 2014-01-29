<?php
$this->result = array();
$cacheDir = $this->getCachDir();
$cachePath = $cacheDir->path;
if( strpos($this->queryStr, "SELECT COUNT(*) as cnt FROM {$GLOBALS['fc_config']['db']['pref']}connections WHERE ip=? AND userid IS NOT NULL")!==FALSE )
{
	$file_name = $this->getCachFileName('Connections');
			$handle = @fopen($file_name, "r");
			$total = 0;
			$allUsers = array();
			while (!feof($handle))
			{
    			$buffer = fgets( $handle );
				$array = explode("\t",$buffer);
				if( $array[9]==$params[0] && ''!=trim($array[3]) )
				{
					$total++;
				}
			}
			$allUsers[0]['cnt'] = $total;

			@fclose($handle);
			//return $allUsers;
	return new ResultSet1( $allUsers );
}
elseif( strpos($this->queryStr, "SELECT COUNT(*) AS numb FROM {$GLOBALS['fc_config']['db']['pref']}connections WHERE userid IS NOT NULL AND userid <> ? AND roomid=?")!==FALSE )
{
	$connections_file_name = $this->getCachFileName('Connections');
	$count = 0;
	$i = 0;

	$connections_file = file($connections_file_name);



	$records_to_update = array();
	if( $connections_file != FALSE )
	{
		foreach( $connections_file as $key=>$val )
		{
			$conn = explode( "\t" , $val );
			if( $conn[4]==$params[1] && $conn[3]!=$params[0] && $conn[3]!='' )
			{
				$count++;
			}
		}
	}

	$result = array();
	$result[]['numb'] = $count;


			//return $result;
	return new ResultSet1($result);
}
elseif( strpos($this->queryStr, "SELECT COUNT(*) AS numb")!==FALSE )
{
	$rooms_file_name = $this->getCachFileName('Rooms');
	$connections_file_name = $this->getCachFileName('Connections');
	$count = 0;
	//$rooms_file = file($rooms_file_name);
	$i = 0;
	/*while( !($rooms_file = file($rooms_file_name)) )
	{
		usleep(1000);//for linux
		$i++;
		if( $i>1000  )
			break;
	}*/
	$rooms_file = file($rooms_file_name);
	$connections_file = file($connections_file_name);
	/*$i = 0;
	while( !($connections_file = file($connections_file_name)) )
	{
		usleep(1000);//for linux
		$i++;
		if( $i>1000  )
			break;
	}*/



	$records_to_update = array();

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

			if( $conn[4]==$params[1] && $conn[3]!=$params[0] && $conn[3]!='' )
			{
				$count++;
			}
		}
	}


	$result = array();
	$result[]['numb'] = $count;


			//return $result;
	return new ResultSet1($result);
}
elseif( strpos($this->queryStr, "UPDATE {$GLOBALS['fc_config']['db']['pref']}rooms,{$GLOBALS['fc_config']['db']['pref']}connections")!==FALSE )
{
	$connections_file_name = $this->getCachFileName('Connections');
	$rooms_file_name = $this->getCachFileName('Rooms');
	$rooms_file = file($rooms_file_name);

	/*$i = 0;
	while( !($rooms_file = file($rooms_file_name)) )
	{
		usleep(1000);//for linux
		$i++;
		if( $i>1000  )
			break;
	}*/

	$connections_file = file($connections_file_name);

	/*$i = 0;
	while( !($connections_file = file($connections_file_name)) )
	{
		usleep(1000);//for linux
		$i++;
		if( $i>1000  )
			break;
	}*/

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

			toLog("2222222222222222    filename",$filename);

			$file = @fopen($filename, "w");
			fwrite($file, time());
			fflush($file);
			fclose($file);
		}
	}
	return null;
	//$this->updateRoom();
}
elseif( $this->queryStr=="SELECT lang FROM {$GLOBALS['fc_config']['db']['pref']}connections WHERE id=?" )
{
	$file_name = $this->getCachFileName('Connections');
			$handle = @fopen($file_name, "r");
			$total = '';
			$allUsers = array();
			while (!feof($handle))
			{
    			$buffer = fgets( $handle );


				if( $buffer=='' )
					continue;

				$array = explode("\t",$buffer);

				if( $array[0]!=$params[0] )
					continue;


				$array['lang'] = $array[8];
				unset($array[0]);unset($array[1]);unset($array[2]);unset($array[3]);unset($array[4]);unset($array[5]);unset($array[6]);
				unset($array[7]);unset($array[8]);unset($array[9]);unset($array[10]);
				$allUsers[] = $array;


			}

			@fclose($handle);
			//return $allUsers;
	return new ResultSet1( $allUsers );
}
elseif( $this->queryStr=="UPDATE {$GLOBALS['fc_config']['db']['pref']}connections SET updated=NOW() WHERE id=?" )
{
	$fname = $GLOBALS['fc_config']['cachePath'].'update_'.$params[0].'_.txt';

					if( file_exists( $fname ) )
					{

						$fp = @fopen($fname,"w");
						@fwrite($fp,time());
						fflush($fp);
						@fclose( $fp );

						return $params[0];
					}

					$fp = @fopen($fname,"a");
					@fwrite($fp,time());
					fflush($fp);
					@fclose( $fp );


					return true;
	//$this->updateConn1( $queryParams );
}
elseif( $this->queryStr=="SELECT id, ip FROM {$GLOBALS['fc_config']['db']['pref']}connections WHERE userid=? AND id<>?" )
{
	$file_name = $this->getCachFileName('Connections');
			$handle = @fopen($file_name, "r");
			$total = '';
			$allUsers = array();
			while (!feof($handle))
			{
    			$buffer = fgets( $handle );

				if( $buffer=='' )
					continue;


				$array = explode("\t",$buffer);
				if( $array[3] == $params[0] && $array[0] != $params[1] )
				{
					$array['userid'] = 	$array[3];
					$array['id'] = 	$array[0];


					unset($array[0]);unset($array[1]);unset($array[2]);unset($array[3]);unset($array[4]);unset($array[5]);unset($array[6]);
					unset($array[7]);unset($array[8]);unset($array[9]);unset($array[10]);unset($array[11]);
					$allUsers[] = $array;

				}
			}
			@fclose($handle);

			//return $allUsers;
	return new ResultSet1( $allUsers );
}
elseif( $this->queryStr=="SELECT userid, state, color, lang, roomid FROM {$GLOBALS['fc_config']['db']['pref']}connections WHERE userid IS NOT NULL AND roomid=?" )
{
	$file_name = $this->getCachFileName('Connections');
			$handle = @fopen($file_name, "r");
			$total = '';
			$allUsers = array();
			while (!feof($handle))
			{
    			$buffer = fgets( $handle );

				if( $buffer=='' )
					continue;


				$array = explode("\t",$buffer);
				if( $array[3]!='' && $array[4]==$params[0] )
				{
					$array['userid'] = 	$array[3];
					$array['roomid'] = 	$array[4];
					$array['state'] = 	$array[5];
					$array['color'] = 	$array[6];
					$array['lang'] = 	$array[8];

					unset($array[0]);unset($array[1]);unset($array[2]);unset($array[3]);unset($array[4]);unset($array[5]);unset($array[6]);
					unset($array[7]);unset($array[8]);unset($array[9]);unset($array[10]);unset($array[11]);
					$allUsers[] = $array;

				}
			}
			@fclose($handle);

			//return $allUsers;
	return new ResultSet1( $allUsers );
}
elseif( $this->queryStr=="SELECT userid, state, color, lang, roomid FROM {$GLOBALS['fc_config']['db']['pref']}connections WHERE userid IS NOT NULL" )
{
	$file_name = $this->getCachFileName('Connections');
			$handle = @fopen($file_name, "r");
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
					$array['roomid'] = 	$array[4];
					$array['state'] = 	$array[5];
					$array['color'] = 	$array[6];
					$array['lang'] = 	$array[8];

					unset($array[0]);unset($array[1]);unset($array[2]);unset($array[3]);unset($array[4]);unset($array[5]);unset($array[6]);
					unset($array[7]);unset($array[8]);unset($array[9]);unset($array[10]);unset($array[11]);
					$allUsers[] = $array;

				}
			}
			@fclose($handle);

			//return $allUsers;
	return new ResultSet1($allUsers);
}

elseif( $this->queryStr=="INSERT INTO {$GLOBALS['fc_config']['db']['pref']}connections (id, updated, created, userid, roomid, color, state, start, lang, ip) VALUES (?, NOW(), NOW(), ?, ?, ?, ?, ?, ?, ?)" )
{
	$file_name = $this->getCachFileName('Connections');
	if(($file_name = $this->getCachFileName('Connections')) != null)
	{
		$file = @fopen($file_name,'a');
		fclose($file);
	}
	else
	{
		$cacheDir = $this->getCachDir();
		$cachePath = $cacheDir->path;
		$file_name = $cachePath.$GLOBALS['fc_config']['db']['pref'].'connections_'.$GLOBALS['fc_config']['cacheFilePrefix'].'.txt';
	}


	if( $params[1]!='' )
	{
		$file = @fopen( $GLOBALS['fc_config']['cachePath'].'update_'.$params[0].'_.txt','w' );
		@fwrite($file, time());
		@fclose($file);
	}

	$today = date("Y-m-d H:i:s");//???
	$file = @fopen($file_name,'a');
	$fileRecordsCount = count($file);



	$str = $params[0]."\t"."$today"."\t"."$today"."\t".$params[1]."\t".$params[2]."\t".$params[3]."\t".$params[4]."\t".$params[5]."\t".$params[6]."\t".$params[7]."\t\t1\n";

	@fwrite($file, $str);
	fflush($file);
	@fclose($file);
	$this->result = array();
	return $params[0];
	//$this->insertConn($queryParams);
}
elseif( $this->queryStr=="SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}connections WHERE id=? LIMIT 1" )
{

	$file_name = $this->getCachFileName('Connections');

	$handle = @fopen($file_name, "r");
	$total = '';
	$allUsers = array();
	while ($buffer = fgets($handle))
	{
    	//$buffer = fgets( $handle );


		//toLog("buffer",$buffer);
		if( $buffer=='' )
			continue;

		$array = explode("\t",$buffer);

		if( $params[0]==$array[0] )
		{

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
			break;
		}


	}

	@fclose($handle);
	//return $allUsers;
	return new ResultSet1($allUsers);
}
elseif( $this->queryStr=="SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}connections LIMIT 1" )
{
	return $this->selectIfConn();
}
elseif( $this->queryStr=="SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}connections" )
{
	$file_name = $this->getCachFileName('Connections');
			$handle = @fopen($file_name, "r");
			$total = '';
			$allUsers = array();
			while ($buffer = fgets($handle))
			{
    			//$buffer = fgets( $handle );

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
			//return $allUsers;


	return new ResultSet1($allUsers);
}
/*elseif( $this->queryStr=="SELECT id FROM {$GLOBALS['fc_config']['db']['pref']}connections WHERE userid IS NOT NULL AND updated < DATE_SUB(NOW(),INTERVAL ? SECOND) AND ip <> ?" )
{
	//return null;

			$cacheDir = $this->getCachDir();
			$cachePath = $cacheDir->path;
			$allUsers = array();

			while (false !== ($entry = $cacheDir->read()))
			{
				//if( $this->breakFile($entry) || strpos($entry, $GLOBALS['fc_config']['db']['pref']."messages")!==FALSE )
					//continue;

					if( strpos($entry, 'update')!==FALSE )
					{
						$arr = explode("_",$entry);
						$handle = @fopen($cachePath.$entry, "r");
						$buffer = fgets( $handle );

						if( $buffer=='' )
							continue;

						fclose($handle);
						if( time()-$buffer>$params[0] )
						{
							$allUsers[]['id'] = $arr[1];
							unlink($cachePath.$entry);

						}
					}


			}

			//return $allUsers;

	return new ResultSet1($allUsers);
}*/
elseif( $this->queryStr=="SELECT userid, state, color, lang, roomid FROM {$GLOBALS['fc_config']['db']['pref']}connections WHERE userid IS NOT NULL AND userid <> ? AND roomid=?" )
{
	$file_name = $this->getCachFileName('Connections');
			$handle = @fopen($file_name, "r");
			$total = '';
			$allUsers = array();
			while ($buffer = fgets($handle))
			{
    			//$buffer = fgets( $handle );

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
					unset($array[7]);unset($array[8]);unset($array[9]);unset($array[10]);unset($array[11]);
					$allUsers[] = $array;

				}
			}
			@fclose($handle);

			//return $allUsers;

	return new ResultSet1( $allUsers  );
}
elseif( $this->queryStr=="SELECT count(*) as msgnumb FROM {$GLOBALS['fc_config']['db']['pref']}connections WHERE userid IS NOT NULL" )
{
	return null;
}
elseif( $this->queryStr=="SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}connections WHERE userid IS NOT NULL ORDER BY roomid" )
{

	$file_name = $this->getCachFileName('Connections');
			$handle = @fopen($file_name, "r");
			$total = '';
			$allUsers = array();
			while ($buffer = fgets($handle))
			{
    			//$buffer = fgets( $handle );
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
			//return $allUsers;
	return new ResultSet1($allUsers);
}
elseif( $this->queryStr=="SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}connections WHERE roomid<>? AND userid IS NOT NULL" )
{
	return null;
}
elseif( $this->queryStr=="SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}connections WHERE roomid=? AND userid IS NOT NULL" )
{
	return null;
}
elseif( $this->queryStr=="SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}connections WHERE userid IS NOT NULL" )
{
	$file_name = $this->getCachFileName('Connections');
			$handle = @fopen($file_name, "r");
			$total = '';
			$allUsers = array();
			while ($buffer = fgets($handle))
			{
    			//$buffer = fgets( $handle );

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
			//return $allUsers;
	return new ResultSet1( $allUsers );
}
elseif( $this->queryStr=="SELECT COUNT(*) AS CNT FROM {$GLOBALS['fc_config']['db']['pref']}connections WHERE roomid=? AND userid IS NOT NULL" )
{
	$file_name = $this->getCachFileName('Connections');
			$handle = @fopen($file_name, "r");
			$total = 0;
			$allUsers = array();
			while ($buffer = fgets($handle))
			{
    			//$buffer = fgets( $handle );


				if( $buffer=='' )
					continue;

				$array = explode("\t",$buffer);
				if( $array[3]!='' && $array[4]!=$params[0] )
					$total++;

			}


			$allUsers[0]['CNT'] = $total;

			fclose( $handle );
			//return $allUsers;
	return new ResultSet1($allUsers);
}
elseif( $this->queryStr=="SELECT id FROM {$GLOBALS['fc_config']['db']['pref']}connections WHERE userid=?  LIMIT 1" )
{
	$file_name = $this->getCachFileName('Connections');
	$handle = @fopen($file_name, "r");
	$total = '';
	$allUsers = array();

	while ($buffer = fgets( $handle ))
	{
    	//$buffer = fgets( $handle );
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
	//return ;
	return new ResultSet1( $allUsers );
}
elseif( $this->queryStr=="SELECT COUNT(*) as cnt FROM {$GLOBALS['fc_config']['db']['pref']}connections WHERE ip=? AND userid IS NOT NULL" )
{
	return null;
}
elseif( $this->queryStr=="SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}connections WHERE id<>? AND userid IS NOT NULL" )
{
	$file_name = $this->getCachFileName('Connections');
	$handle = @fopen($file_name, "r");
	$total = '';
	$allUsers = array();
	while ($buffer = fgets( $handle ))
	{
    	//$buffer = fgets( $handle );
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
			//return $allUsers;
	return new ResultSet1( $allUsers );
}
elseif( $this->queryStr=="UPDATE {$GLOBALS['fc_config']['db']['pref']}connections SET updated=NOW(), userid=?, roomid=?, color=?, state=?, start=?, lang=?, ip=?, tzoffset=? WHERE id=?" )
{
	$file_name = $this->getCachFileName('Connections');
	$handle = @fopen($file_name, "r");
	$total = '';
	$whot='*';

	while ($buffer = fgets($handle))
	{
    	if( trim($buffer)=='' )
			continue;

		$array = explode("\t",$buffer);
		$today = date("Y-m-d H:i:s");//

		//toLog("array",$array[0]);


		if( $array[0]==$params[8] )
		{
			$userID = $array[3];
			$total = $total.$params[8]."\t"."$today"."\t".$array[2]."\t".$params[0]."\t".$params[1]."\t".$params[2]."\t".$params[3]."\t".$params[4]."\t".$params[5]."\t".$params[6]."\t".$params[7]."\t1\n";
		}
		else
			$total = $total.$buffer;

	}

	@fclose($handle);


	$file = @fopen($file_name,'w');
	@fwrite($file , $total);
	fflush($file);
	@fclose($file);

	if( $params[0]!='' )
	{
		$f = $GLOBALS['fc_config']['cachePath'].'update_'.$params[8].'_.txt';
		$fp1 = @fopen($f,"a");
		@fwrite( $fp1,time());
		fflush( $fp1 );
		@fclose( $fp1 );

	}
	else
	{
		$f = $GLOBALS['fc_config']['cachePath'].'update_'.$params[8].'_.txt';
		if( file_exists( $f ) )
		{
			unlink($f);
		}

		$file_name = $this->getCachFileName('Users');
		$handle = @fopen($file_name,'r');
		$str = "";
		while (!feof($handle))
		{
    		$buffer = fgets($handle);



			if( $buffer=='' )
				continue;



			$array = explode("\t",$buffer);
			if( $array[0]!=$userID )
				$str .= $buffer;
		}
		@fclose($handle);
		$file = @fopen($file_name,'w');
		@fwrite($file, $str);
		@fflush($file);
		@fclose($file);
	}


	return null;
	//$this->updateConn('*',$queryParams);

}
elseif( $this->queryStr=="SELECT id FROM {$GLOBALS['fc_config']['db']['pref']}connections WHERE userid IS NOT NULL AND updated < DATE_SUB(NOW(),INTERVAL ? SECOND) AND ip <> ?" )
{
	$file_name = $this->getCachFileName('Connections');
	//$handle = @fopen($file_name, "r");
	$allConn = file($file_name);
	$total = '';
	$allUsers = array();
	//while ($buffer = fgets( $handle ))
	//{
	foreach( $allConn as $key=>$val )
    {	//$buffer = fgets( $handle );


		$buffer = $val;
		if( $buffer=='' )
			continue;

		$array = explode("\t",$buffer);



		if( $array[3]!='' && $array[9]!=$params[1]  )
		{
			if( file_exists($cachePath.'update_'.$array[0].'_.txt') )
			{
				if( time()-filemtime($cachePath.'update_'.$array[0].'_.txt')>$params[0]  )
				{

					$array['id'] = 	$array[0];
					unset($array[0]);unset($array[1]);unset($array[2]);unset($array[3]);unset($array[4]);unset($array[5]);unset($array[6]);
					unset($array[7]);unset($array[8]);unset($array[9]);unset($array[10]);unset($array[11]);
					$allUsers[] = $array;
					//toLog("delete file   array[0]",$array[0]);
					//unlink($cachePath.'update_'.$array[0].'_.txt');
				}
			}
			else
			{
				/*$array['id'] = 	$array[0];
				unset($array[0]);unset($array[1]);unset($array[2]);unset($array[3]);unset($array[4]);unset($array[5]);unset($array[6]);
				unset($array[7]);unset($array[8]);unset($array[9]);unset($array[10]);unset($array[11]);
				$allUsers[] = $array;*/
			}

		}
	}

	//@fclose($handle);
	//return $allUsers;
	return new ResultSet1($allUsers);
}
elseif( $this->queryStr=="DELETE FROM {$GLOBALS['fc_config']['db']['pref']}connections WHERE updated < DATE_SUB(NOW(),INTERVAL ? SECOND)" )
{
	$file_name = $this->getCachFileName('Connections');

	$allConn = file($file_name);
	$total = '';

	$allUsers = array();
	foreach($allConn as $key=>$val)
	{
    	$buffer = $val;//fgets( $handle );


		if( $buffer=='' )
			continue;

		$array = explode("\t",$buffer);

		if( file_exists($cachePath.'update_'.$array[0].'_.txt') )
		{
			//$line = file($cachePath.'update_'.$array[0].'_.txt');

			if( (time()-filemtime($cachePath.'update_'.$array[0].'_.txt'))>$params[0]  )
			{
				unlink($cachePath.'update_'.$array[0].'_.txt');
			}
			else
			{
				$total .= $buffer;
			}
		}
		else
		{
			if( (time() - strtotime($array[1]))<$params[0])
			{
				$total .= $buffer;
			}
		}
	}


	//@fclose( $handle );
	$handle = @fopen( $file_name, "w" );
	fwrite( $handle,$total );
	fflush($handle);
	fclose( $handle );
	$this->result = array();
	return true;
}
elseif( $this->queryStr=="DELETE FROM {$GLOBALS['fc_config']['db']['pref']}connections WHERE id = ?" )
{
	$file_name = $this->getCachFileName('Connections');
	$handle = @fopen($file_name, "r");
	$cacheDir = $this->getCachDir();
	$cachePath = $cacheDir->path;
	$total = '';
	$allUsers = array();
	$buffer = '';
	while ($buffer = fgets( $handle ))
	{
    	//$buffer = fgets( $handle );
		$array = explode("\t",$buffer);
		if( $array[0]!=$params[0] )
		{
			$total .= $buffer;
		}
		else
		{
			if( file_exists($cachePath.'update_'.$array[0].'_.txt') )
			{
				unlink($cachePath.'update_'.$params[0].'_.txt');
			}
		}
	}


	@fclose($handle);
	$handle = @fopen($file_name, "w");
	@fwrite($handle , $total);
	fflush($handle);
	@fclose($handle);
	$this->result = array();
	return true;
}
elseif( strpos($this->queryStr, 'SELECT roomid')!==FALSE )
{
	$file_name = $this->getCachFileName('Connections');
	$handle = @fopen($file_name, "r");
	$total = '';
	$allUsers = array();
	while ($buffer = fgets( $handle ))
	{
    	//$buffer = fgets( $handle );

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
	return new ResultSet1($allUsers);//$queryParams
}

?>