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

if(!empty($_POST['data_type']))
{
	$info['data_type'] 	= $_POST['data_type'];
	$info['errors'] 	= [];
	$info['success'] 	= false;

	if($_POST['data_type'] == "signup")
	{

		if(empty($_POST['firstname']))
	{
		$info['errors']['firstname'] = "A first name is required";
	}else
	if(!preg_match("/^[\p{L}]+$/", $_POST['firstname']))
	{
		$info['errors']['firstname'] = "First name cant have special characters or spaces and numbers";
	}

	//validate lastname
	if(empty($_POST['lastname']))
	{
		$info['errors']['lastname'] = "A last name is required";
	}else
	if(!preg_match("/^[\p{L}]+$/", $_POST['lastname']))
	{
		$info['errors']['lastname'] = "Last name cant have special characters or spaces and numbers";
	}

	//validate email
	if(empty($_POST['email']))
	{
		$info['errors']['email'] = "An email is required";
	}else
	if(!filter_var($_POST['email'],FILTER_VALIDATE_EMAIL))
	{
		$info['errors']['email'] = "Email is not valid";
	}


	//validate gender
	$genders = ['Male','Female'];
	if(empty($_POST['gender']))
	{
		$info['errors']['gender'] = "A gender is required";
	}else
	if(!in_array($_POST['gender'], $genders))
	{
		$info['errors']['gender'] = "Gender is not valid";
	}
	

	//validate password
	if(empty($_POST['password']))
	{
		$info['errors']['password'] = "A password is required";
	}else
	if($_POST['password'] !== $_POST['retype_password'])
	{
		$info['errors']['password'] = "Passwords dont match";
	}else
	if(strlen($_POST['password']) < 8)
	{
		$info['errors']['password'] = "Password must be at least 8 characters long";
	}


	if(empty($info['errors']))
	{
		//save to database
		$arr = [];
		$arr['firstname'] 	  = $_POST['firstname'];
		$arr['lastname'] 	  = $_POST['lastname'];
		$arr['email'] 		  = $_POST['email'];
		$arr['gender'] 		  = $_POST['gender'];
		
		$arr['password'] 	  = password_hash($_POST['password'], PASSWORD_DEFAULT);
		$arr['date'] 		  = date("Y-m-d H:i:s");

		db_query("insert into users (firstname,lastname,gender,password,date,email) values (:firstname,:lastname,:gender,:password,:date,:email)",$arr);

		$info['success'] 	= true;
	}
}


echo json_encode($info);
}

