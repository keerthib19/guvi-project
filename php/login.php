<?php
session_start();

function db_query(string $query, array $data = array())
{
	$string = "mysql:hostname=localhost;dbname=profile_db";
	$con = new PDO($string, 'root', '');

	$stm = $con->prepare($query);
	$check = $stm->execute($data);

	if($check)
	{
		$res = $stm->fetchAll(PDO::FETCH_ASSOC);
		if(is_array($res) && !empty($res))
		{
			return $res;
		} 
	}

	return false;
}
function is_logged_in():bool
{

	if(!empty($_SESSION['PROFILE']))
	{
		return true;
	}

	return false;
}

function redirect($path):void
{
	header("Location: $path");
	die;
}

function esc(string $str):string
{
	return htmlspecialchars($str);
}

function get_image($path = ''):string 
{
	if(file_exists($path))
	{
		return $path;
	}

	return './images/no-image.jpg';
}

function user(string $key = '')
{
	if(is_logged_in())
	{
		if(!empty($_SESSION['PROFILE'][$key]))
		{
			return $_SESSION['PROFILE'][$key];
		}
	}

	return false;
}
if(!empty($_POST['data_type']))
{
	$info['data_type'] 	= $_POST['data_type'];
	$info['errors'] 	= [];
	$info['success'] 	= false;

	if($_POST['data_type'] == "login")
	$arr = [];
	$arr['email'] 		= $_POST['email'];

 	$row = db_query("select * from users where email = :email limit 1",$arr);

	if(!empty($row))
	{
		$row = $row[0];

		//check the password
		if(password_verify($_POST['password'], $row['password']))
		{
			//password correct
			$info['success'] 	= true;
			$_SESSION['PROFILE'] = $row;
		}else
		{
			$info['errors']['email'] = "Wrong email or password";
		}

	}else
	{
		$info['errors']['email'] = "Wrong email or password";
	}
    echo json_encode($info);
}

