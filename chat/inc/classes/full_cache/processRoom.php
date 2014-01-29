<?php

if($this->queryStr == "SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}rooms ORDER BY id")
{

	$file_name = $this->getCachFileName('Rooms');
			$handle = @fopen($file_name, "r");
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
			fclose($handle);
			//return $allRooms;

	if( !function_exists("cmpRoomId") )
	{
		function cmpRoomId($elem1, $elem2)
		{
			if($elem1['id']<$elem2['id'])
				return -1;
			elseif($elem1['id']==$elem2['id'])
				return 0;
			elseif($elem1['id']>$elem2['id'])
				return 1;
		}
	}

	usort($allRooms, "cmpRoomId");


	return new ResultSet1( $allRooms );
}
elseif( strtoupper($this->queryStr) == strtoupper("SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}rooms order by ispermanent"))
{
	$file_name = $this->getCachFileName('Rooms');
			$handle = @fopen($file_name, "r");
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

			/*function cmp($a, $b)
			{

				if ($a['ispermanent'] == $b['ispermanent']) {
        			return 0;
    			}
    			return ($b['ispermanent'] == '') ? -1 : 1;
			}
			usort($allRooms, "cmp");*/

	if( !function_exists("cmpRoom1") )
	{
		function cmpRoom1($elem1, $elem2)
		{
			if($elem1['ispermanent']<$elem2['ispermanent'] || $elem2['ispermanent']=='' )
				return -1;
			elseif($elem1['ispermanent']==$elem2['ispermanent'])
				return 0;
			elseif($elem1['ispermanent']>$elem2['ispermanent'] || $elem1['ispermanent']=='')
				return 1;
		}
	}

	usort($allRooms, "cmpRoom1");

fclose($handle);
			//return $allRooms;

	return new ResultSet1( $allRooms );
}
elseif( strtoupper($this->queryStr) == strtoupper("SELECT id FROM {$GLOBALS['fc_config']['db']['pref']}rooms WHERE ispermanent IS NOT NULL ORDER BY ispermanent"))
{
	$file_name = $this->getCachFileName('Rooms');
	$handle = @fopen($file_name, "r");
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
	//return $allRooms;

	if( !function_exists("cmpRoom") )
	{
		function cmpRoom($elem1, $elem2)
		{
			if($elem1['ispermanent']<$elem2['ispermanent'])
				return -1;
			elseif($elem1['ispermanent']==$elem2['ispermanent'])
				return 0;
			elseif($elem1['ispermanent']>$elem2['ispermanent'])
				return 1;
		}
	}

	usort($allRooms, "cmpRoom");

	return new ResultSet1( $allRooms );
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
			$allRooms = array();
			$i = 0;
			while( !($arrayRoom = file($file_name)) )
			{
				//usleep(1000);//for linux
				$i++;
				if( $i>1000  )
					break;
			}

			//$handle = @fopen($file_name, "r");
			$total = '';
toLog("arrayRoom 236 update",$arrayRoom);
			foreach( $arrayRoom as $key=>$val )
			{
				$buffer = $val;

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
			/*while (!feof($handle))
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

			}*/
			toLog("total 282 update",$total);
			//@fclose($handle);
			$handle = @fopen($file_name, "w");
			fwrite($handle,$total);
			//fflush($handle);
			@fclose($handle);
			return true;


	//return $this->updateRoomsInCache( $params );
}
elseif($this->queryStr == "SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}rooms WHERE ispublic IS NOT NULL AND ispermanent IS NOT NULL ORDER BY ispermanent")
{
	return new ResultSet1($this->processRoomsAll());
}
elseif($this->queryStr == "SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}rooms WHERE ispublic IS NOT NULL AND ispermanent IS NULL ORDER BY created")
{
	return new ResultSet1($this->processRoomsAll());
}
elseif($this->queryStr == "SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}rooms WHERE ispublic IS NULL AND ispermanent IS NOT NULL ORDER BY created")
{
	$file_name = $this->getCachFileName('Rooms');
	$handle = @fopen($file_name, "r");
	$total = '';
	$allRooms = array();
	while (!feof($handle))
	{
    	$buffer = fgets($handle);
		if( $buffer=='' )
			continue;
		$array = explode("\t",$buffer);


		if( $array[5]=='' && $array[6]!='' )
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

		$allRooms[] = $array;

	}
	@fclose($handle);
	//return $allRooms;
	return new ResultSet1( $allRooms );
}
elseif($this->queryStr == "SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}rooms WHERE ispublic IS NOT NULL order by ispermanent")
{
	$file_name = $this->getCachFileName('Rooms');
	$handle = @fopen($file_name, "r");
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



	if( !function_exists("cmpRoom2") )
	{
		function cmpRoom2($elem1, $elem2)
		{
			if($elem1['id']<$elem2['id'] )
				return -1;
			elseif($elem1['id']==$elem2['id'])
				return 0;
			elseif($elem1['id']>$elem2['id']  || $elem1['id']=='')
				return 1;
		}
	}

	usort($allRooms, "cmpRoom2");




	//return $allRooms;
	return new ResultSet1( $allRooms );
}
elseif($this->queryStr == "SELECT id FROM {$GLOBALS['fc_config']['db']['pref']}rooms WHERE ispermanent IS NULL AND updated < DATE_SUB(NOW(),INTERVAL ? SECOND)")
{
	$cacheDir = $this->getCachDir();
	$cachePath = $cacheDir->path;
	$allRooms = array();
	$all = array();
	while (false !== ($entry = $cacheDir->read()))
	{
		if( strpos($entry, 'updroom')!==FALSE )
		{
			/*$array = file($cachePath.$entry);

			$i = 0;
			while( !($array = file($cachePath.$entry)) )
			{
				usleep(1000);//for linux
				//toLog("buffer$i",$buffer);
				$i++;
				if( $i>1000  )
					break;
			}

			$id = explode("_",$entry);
			if( time()-$array[0]>$params[0] )
			{
				$allRooms[]['id'] = $id[1];
				$all[] = $id[1];

				unlink($cachePath.$entry);

			}*/
			$fdif = (time() - filemtime($cachePath.$entry));
			if($params[0] < $fdif)
			{
				//unlink($fname);
				$id = explode("_",$entry);
				$allRooms[]['id'] = $id[1];
				$all[] = $id[1];

			}
		}
	}

	$file_name = $this->getCachFileName('Rooms');
	$array = file($file_name);

	$i = 0;
			while( !($array = file($file_name)) )
			{
				//usleep(1000);//for linux
				$i++;
				if( $i>1000  )
					break;
			}

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
		$handle = @fopen($file_name, "w");
		fwrite($handle, $total);
		fflush($handle);
		fclose($handle);
	}

	//toLog("allRooms",$allRooms);

	//return $allRooms;
	return new ResultSet1( $allRooms );
}
elseif($this->queryStr == "SELECT id, ispermanent FROM {$GLOBALS['fc_config']['db']['pref']}rooms")
{
	$file_name = $this->getCachFileName('Rooms');
	$handle = @fopen($file_name, "r");

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
	//return $allRooms;
	return new ResultSet1( $allRooms );
}
elseif($this->queryStr == "SELECT ispermanent FROM {$GLOBALS['fc_config']['db']['pref']}rooms ORDER BY ispermanent")
{
	$file_name = $this->getCachFileName('Rooms');
	$handle = @fopen($file_name, "r");

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
	//return $allRooms;
	return new ResultSet1( $allRooms );
}//
elseif($this->queryStr == "SELECT id FROM {$GLOBALS['fc_config']['db']['pref']}rooms WHERE password=''")
{
	$file_name = $this->getCachFileName('Rooms');
	$handle = @fopen($file_name, "r");

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
	//return $allRooms;
	return new ResultSet1( $allRooms );
}
elseif($this->queryStr == "SELECT MAX(id)+1 AS newid FROM {$GLOBALS['fc_config']['db']['pref']}rooms")
{
	return new ResultSet1($this->getRoomsIdMax());
}
elseif( strpos($this->queryStr,"DELETE")!==false &&  strpos($this->queryStr,"?")!==true  )
{
	$id = substr($this->queryStr,strpos($this->queryStr,"id=")+3);
	if( $id =='?')
		$id = $params[0];
$cacheDir = $this->getCachDir();
$cachePath = $cacheDir->path;
$fname = "updroom_".$id."_".$GLOBALS['fc_config']['cacheFilePrefix']."_.txt";

unlink($cachePath.$fname);

toLog("cachePath.fname 589 delete",$cachePath.$fname);
	$file_name = $this->getCachFileName('Rooms');

	$i = 0;
	while( !($array = file($file_name)) )
	{
		//usleep(1000);//for linux
		$i++;
		if( $i>1000  )
			break;
	}

	//$handle = @fopen($file_name, "r");
	$total = '';


	$allRooms = array();
toLog("array 606 delete",$array);
	foreach( $array as $k=>$v )
	{
		$buffer = $v;
		$allRooms = explode("\t",$buffer);
		if( $buffer=='' )
			continue;


		if( $allRooms[0]!=$id )
			$total .= $buffer;
	}

	/*while (!feof($handle))
	{
    	$buffer = fgets($handle);
	$array = explode("\t",$buffer);
		if( $buffer=='' )
			continue;


		if( $array[0]!=$id )
			$total .= $buffer;

	}*/
toLog("total 631 delete",$total);
	//fclose($handle);
	$handle = @fopen($file_name, "w");
	fwrite($handle,$total);
	//fflush($handle);
	fclose($handle);
	$this->deleteRoomById();
	return true;
}//
elseif($this->queryStr == "SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}rooms WHERE name=?")
{
	$file_name = $this->getCachFileName('Rooms');
	$handle = @fopen($file_name, "r");
	$total = '';
	$allRooms = array();

	while (!feof($handle))
	{
    	$buffer = fgets($handle);

		if( $buffer=='' )
			continue;

		$array = explode("\t",$buffer);


		if( $array[3]==$params[0])
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
	}
	@fclose($handle);

	//return $allRooms;
	return new ResultSet1( $allRooms );
}
elseif($this->queryStr == "SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}rooms WHERE id=?")
{
	if( ($rows=$this->roomsIsCached("*", "id=?", $params)) !== false)
	{
		return new ResultSet1($rows);
	}
	else
	{
		return new ResultSet1($this->processRoomsAll("*","id",$params));
	}
}

?>