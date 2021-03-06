<?php
/*
	Page Generate Script:	class/group/make.php
	This script generates the Form Group page.
	
	HTTP Inputs:
		'ID'
		
	Page Features:
		"Back" button - Hyperlinks to class/group/index.php
		"Group Name" text box - 1 to 30 characters
		"Max Group Members" slider/whatever
		"Looking for Members" check box
		"Open Group" check box
		"Form Group" button - Runs AJAX class/group/functions/makeGroup.php
		
	AJAX Functions:
		makeGroup.php
			Inputs
				'ID'
				'Name'
				'Max'
				'Looking'
				'Open'
			Outputs/Actions
				Case "0" - Script failure
					TBD
				Case "1" - Success
					Redirect to class/group/view.php
				Case "2" - Idle Timeout
					Redirect to index.php
				Case "3" - Group Name Constraint Error
					Show constraintts
				Case "4" - User Already Leads Group
					Show message
					
	Page Connections:
		class/group/index.php
		class/group/view.php
*/

session_start();

if(isset($_SESSION['username'])){
	if(($_SESSION['idle'] + 600) < time()){
		unset($_SESSION['username']);
		unset($_SESSION['idle']);
		header("Location: ../../index.php", true, 303);
		exit();
	}
}
else{
	header("Location: ../../index.php");
	exit();
}

$username = $_SESSION['username'];
$_SESSION['idle'] = time();

$mysqli = new mysqli("localhost", "SelectOnly", "system", "LSU-ACE");
if($mysqli->connect_errno){
	//Send HTTP error code
	exit();
}


if(!isset($_GET['ID'])){
	header("Location: ../../profile/index.php", true, 303);
	exit();
}
$ID = $_GET['ID'];

$res = $mysqli->query("SELECT * FROM Taking WHERE Cid = '$ID' AND Sid = '$username';");

if($res->num_rows == 0){
	header("Location: ../../profile/index.php", true, 303);
	exit();
}



$html = "<html>
 	<head> 
   		<title>Form Group</title>
		<style>
		body{margin:40px
		auto;max-width:650px;line-height:1.6;font-size:18px;color:#444;padding:0
		10px}h1,h2,h3{line-height:1.2}
		</style>
  	</head>
  	<body> 
		<h1>Form Group</h1>
            <a href= \"index.php?ID=$ID\">Back</a>
            <form name=\"makeGroup\">
				Group Name: <input type=\"text\" id=\"groupName\"><br>
                Max Group Members:<input type=\"text\" id=\"max\" maxlength=\"36\"><br>
                Looking for Members:<input id=\"Looking\" type=\"checkbox\" name=\"Looking\"><br>
                Open Group:<input id=\"Open\" type=\"checkbox\" name=\"Open\"><br>                
				</div>
				
				<input type=\"button\" value=\"Form Group\" onClick=\"loadDoc('functions/makeGroup.php', myFunction)\">
			</form>	
    </body>
	
	<script>
		function loadDoc(url, cFunction) 
		{
			var name = document.getElementById('groupName').value;
			var max = document.getElementById('max').value;
			max = parseInt(max);
			var looking = document.getElementById('Looking').checked;
			var open = document.getElementById('Open').checked;
			var attributes = 'ID=$ID&Name=' + escape(name) + '&Max=' + max + '&Looking=' + looking + '&Open=' + open;
	
			var xhttp;
			xhttp=new XMLHttpRequest();
			xhttp.onreadystatechange = function() 
			{
				if (this.readyState == 4 && this.status == 200) 
				{
					cFunction(this);
				}
			};
			xhttp.open(\"POST\", url, true);
			xhttp.setRequestHeader(\"Content-type\", \"application/x-www-form-urlencoded\");
			xhttp.send(attributes);
		}
		
		function myFunction(xhttp) 
		{
			switch(xhttp.responseText)
			{
				case \"0\": 
					alert(\"Form Group Script Failure\");
					break;
		
				case \"1\": 
					window.location = \"view.php?ID=$ID&Sid=$username\";
					break;
		
				case \"2\": 
					window.location = \"../../index.php\";
					break;
		
				case \"3\": 
					alert(\"Group Name must be 1 to 30 characters long\");
					break;
		
				case \"4\": 
					alert(\"You are already leading a group\");
					break;         
			}
		}
	</script>
</html>";

echo $html;

exit();


?>