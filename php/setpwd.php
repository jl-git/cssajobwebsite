<!DOCTYPE HTML>
<head>
	<title>MyFavourate</title>
	<meta charset="utf-8">
	 <meta name="viewport" content="width=device-width, initial-scale=1">
	 <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	 <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	 <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
	 <script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.3.14/angular.min.js"></script>
	 <link rel="stylesheet" href="../css/main.css">
	 <link rel="stylesheet" href="../css/profile.css">
	 <script src="js/main.js"></script>
  	<style>
  	</style>
</head>

<body>
<?php
  session_start();

  include("header.php");
  include("sqlfuncs.php");
  if(!isset($_SESSION['email']))
	{
		header('Location: index.php');
		exit;
	}
  $myemail = $_SESSION["email"];

	if (sql_is_verified($myemail, $_SESSION['type'])) {

	} else {
		echo "<h3>Please verify your email</h3>";
		return;
	}
	echo '<div class = "container">';

	if(isset($_POST['submit']))
	{
   		display();
	} 
?>
<div class = "row">

</div>
<div class="row" >
	<h2>Settings</h2>
<div class="col-xs-12 col-sm-6 col-md-8" style = "overflow:scroll; height:450px">
<?php
	function update($val, $email)
	{
		
    	$conn = getconn();

    	if ($_SESSION["type"]=="stu") {
        	$stmt = $conn->prepare("update user, student set user.password='".$val."', student.password='".$val."' where user.email=:myemail and user.email=student.email");
    	} else {
        	$stmt = $conn->prepare("update user, employer set user.password='".$val."', employer.password='".$val."' where user.email=:myemail and user.email=employer.email");
    	}

    	$stmt->bindParam(":myemail",$email);

    	$result = $stmt->execute();
    	if (!$result)
        	pdo_die($stmt);

	}

?>

<?php
	$conn = getconn();
	if($_SESSION["type"]=="stu")
		$stmt = $conn->prepare("select * from student where email = :myemail;");
	else
		$stmt = $conn->prepare("select * from employer where email = :myemail;");
	$stmt->bindParam(":myemail",$myemail);
	$result = $stmt->execute();
	if (!$result)
    {
        echo "What the fuck?";
        pdo_die($stmt);
    }
        

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$row = $result[0];
	echo '<div class = "container col-sm-offset-2 col-sm-5 col-sm-offset-2" >';
	echo '<div class = "text-center">';
	echo '</div>';

  	echo '<form class="form" role="form" method="post" action="setpwd.php">
    		<div class="form-group">
      			<label for="oldpwd">Old Password:</label>
      			<input name="oldpwd" type="password" class="form-control" id="oldpwd">
    		</div>
    		<div class="form-group">
      			<label for="newpwd">New Password:</label>
      			<input name="newpwd" type="password" class="form-control" id="newpwd">
    		</div>
    		<div class="form-group">
      			<label for="repwd">Re-enter Password:</label>
      			<input name="repwd" type="password" class="form-control" id="repwd">
    		</div>
    	 	<button type="submit" class="btn btn-default" name="submit_pwd">Change Password</button>
  		 </form>';

  	if(isset($_POST['submit_pwd'])) {
		if ($row['password'] != $_POST['oldpwd']) {
			echo "Old password is not correct!";
		} else if ($_POST['newpwd'] != $_POST['repwd']) {
			echo "New password is not match!";
		} else {
			update($_POST['newpwd'], $myemail);
			echo "Password changed successfully!";
		}
	}
  	
?>
</div>

<div class="col-xs-6 col-md-4">
  	<ul class="list-group">
<?	
	
	$conn = getconn();
	if($_SESSION["type"]=="stu")
		$stmt = $conn->prepare("select * from student where email = :myemail;");
	else
		$stmt = $conn->prepare("select * from employer where email = :myemail;");
	$stmt->bindParam(":myemail",$myemail);
	$result = $stmt->execute();
	if (!$result)
    {
        echo "What the fuck?";
        pdo_die($stmt);
    }
        

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$row = $result[0];

	if($_SESSION["type"] == "stu")
	{
		echo '<li class="list-group-item">Username: '.$row["name"].'</li>';
		echo '<li class="list-group-item">Expected Graduation Year: '.substr($row["grad_year"], 0, 7).'</li>';
		echo '<li class="list-group-item">Major: '.$row["major"].'</li>';
		echo '<li class="list-group-item">Looking for ';

		switch ($row["job_type"]) {
		case 1:
			echo "Full-time job";
			break;
		case 2:
			echo "Part-time job";
			break;
		case 3:
			echo "Internship";
			break;
		}
		echo '</li>';
	}
	else
	{
		echo '<li class="list-group-item">Username: '.$row["name"].'</li>';
		echo '<li class="list-group-item">Graduation Year: '.substr($row["grad_year"],0,7).'</li>';
		echo '<li class="list-group-item">Company: '.$row["company"].'</li>';
		echo '<li class="list-group-item">Position: '.$row["position"].'</li>';
		echo '<li class="list-group-item">Linkedin Homepage: '.$row["Linkedin"].'</li>';
	}
	
	echo "</ul>";
	echo '</div>';
	echo '</div>';
	echo '</div>';
	echo '</div>';
  	include("footer.php");
?>

</body>
</html>
<?php
function Display_all_query()
{
	$conn = getconn();
	$stmt = $conn->prepare("select * from post_info order by time DESC;");
	$result = $stmt->execute();
	if (!$result)
    {
        echo "What the fuck?";
        pdo_die($stmt);
    }
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    Print_Post($result);
}
?>