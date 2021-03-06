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

  include_once("header.php");
  include_once("sqlfuncs.php");
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
	echo '<div style="width:500px; margin:auto" class = "container">';

	if(isset($_POST['submit']))
	{
   		display();
	} 
?>

	<h2>Change Password</h2>
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
	echo '<div class = "" >';

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
  
	echo '</div>';
	echo '</div>';
  	include_once("footer.php");
?>

</body>
</html>