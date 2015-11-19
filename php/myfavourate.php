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
  	<script>
	function changeImage(elementId, postid) {
	    var image = document.getElementById(elementId);
	    if (image.src.match("jiaStaron")) {
	        image.src = "../pictures/jiaStaroff.png";

	  //       // Send insert request here
	  //       <?php
	  //       $conn = getconn();
			// $stmt = $conn->prepare("INSERT into user_fav VALUES ('".$myemail."', ".postid.");");
			// $result = $stmt->execute();
			// ?>
	    } else {
	        image.src = "../pictures/jiaStaron.png";

	  //       // Send Delete request here
	  //       <?php
	  //       $conn = getconn();
			// $stmt = $conn->prepare("DELETE from user_fav WHERE email = '".$myemail."' and postid = ".postid.";");
			// $result = $stmt->execute();
			// ?>
	    }
	}
</script>
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

?>
<div class = "row">

</div>
<div class="row" >
	<h2>My Favourate Jobs</h2>
<div class="col-xs-12 col-sm-6 col-md-8" style = "overflow:scroll; height:450px">
<?php
	if(isset($_GET["srch-term"]))
	{
		$res = print_text_search($_GET["srch-term"]);
		$targetstring = "(";
		foreach ($res as $key => $value) {
			$targetstring = $targetstring.$value.',';
		}
		if(count($res) == 0){
			echo "<h5> Sorry, we didn't find any matched result :(</h5>";
		}
		else
		{
			$targetstring[strlen($targetstring) - 1] = ')';
			//echo $targetstring;
			$res_data = sql_get_post_by_ids($targetstring);
			Print_Post($res_data,$myemail,0);
		}
	}
	else if (isset($_GET["mode"]))
	{
		if ($_GET['mode'] == 'search') {
			$result =  searchPost(array($_GET['major'], $_GET['company'], $_GET['job_type']));
			$targetstring = "(";
			foreach ($result as $key => $value) {
				$targetstring = $targetstring.$value["postid"].',';
			}

			if (count($result) == 0) {
				echo "<h5>Sorry, didn't find any result :(</h5>";
			}
			else {
				$targetstring[strlen($targetstring) - 1] = ')';
				$res_data = sql_get_post_by_ids($targetstring);
				Print_Post($res_data,$myemail,0);
			}			
		}
	}
	else
	{
		Display_all_query($myemail);
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
function Display_all_query($myemail)
{
	$conn = getconn();
	$stmt = $conn->prepare("select * from post_info P, user_fav F WHERE P.postid = F.postid and F.email = '".$myemail."' order by time DESC;");
	$result = $stmt->execute();
	if (!$result)
    {
        echo "What the fuck?";
        pdo_die($stmt);
    }
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    Print_Post($result,$myemail,0);
}
?>