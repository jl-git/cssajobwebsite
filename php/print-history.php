<!DOCTYPE HTML>
<head>
	<title>Homepage</title>
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
  		$(document).ready(function(){
  			$("a.deletePost").click(function(e){
	          e.preventDefault();
	          var thiss = $(this);
	          var parent = $(this).parent()
	          var whole_list = parent.parent();
	          $.ajax({
	            type: 'post',
	            url: 'homepage.php',
	            data: 'deletePost=' + thiss.attr('data-email'),
	            beforeSend: function() {
	              whole_list.animate({opacity:'0.5'},50);
	            },
	            success: function() {
	              whole_list.slideUp(50,function() {
	                whole_list.remove();
	              });
	            }
	          });
	      });
  		})
  	</script>
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
?>

<div class = "container">
<div class="row" >
	<h2>Post History</h2>
<div class="col-xs-12 col-sm-6 col-md-8" style = "overflow:scroll; height:450px">
	<form action ="homepage.php" method = POST>
<?php

	$conn = getconn();
	if(isset($_POST["deletePost"]))
	{
		$postid = $_POST["deletePost"];
		sql_delete_post_byPostId($postid);
		exit;
	}
	if(isset($_POST["deleteFav"]))
	{
		//echo "dffsf".$_POST["deleteFav"];
		$stmt = $conn->prepare("DELETE FROM user_fav WHERE email='".$myemail."' and postid =".$_POST["deleteFav"]."");
		$stmt->execute();
	}
	if(isset($_POST["addFav"]))
	{
		//echo "dffsf".$_POST["addFav"];
		$stmt = $conn->prepare("INSERT into user_fav VALUES ('".$myemail."', ".$_POST["addFav"].");");
		$stmt->execute();
	}
	if(isset($_POST["deletePost"]))
	{
		$postid = $_POST["deletePost"];
		sql_delete_post_byPostId($postid);
		exit;
	}
	//$server = mysql_connect("localhost","root","1qaz-pl,");
	/*$server = mysql_connect("cssadbinstance.ccmgeu2ghiy1.us-east-1.rds.amazonaws.com", "cssaadmin", "cssaadmin123"); 
	if (!$server) { 
		print "Error - Could not connect to MySQL"; 
		exit; 
	}
	$db = mysql_select_db("user_student"); 
	if (!$db) { 
		print "Error - Could not select the user_student database"; 
		exit; 
	}
	if(!isset($_SESSION['email'])||!isset($_SESSION['type']))
	{
		header('Location: index.php');
		exit;
	}
	$type = $_SESSION['type'];
	if(isset($_GET["srch-term"]))
	{
		$SRCH = $_GET["srch-term"];
		if($type == 'stu'){
			header('Location: homepage-std.php?srch-term='.$SRCH);
			exit;
		}
		else{
			header('Location: homepage-alu.php?srch-term='.$SRCH);
			exit;
		}
	}
	else
	{
		if($type == 'stu'){
			header('Location: homepage-std.php');
			exit;
		}
		else{
			header('Location: homepage-alu.php');
			exit;
		}
		 <ul class="pagination">
  <li><a href="#">1</a></li>
  <li><a href="#">2</a></li>
  <li><a href="#">3</a></li>
  <li><a href="#">4</a></li>
  <li><a href="#">5</a></li>
</ul>
	}*/
	if(isset($_POST["deletePost"]))
          {
            $postid = $_POST["deletePost"][0];
            sql_delete_post_byPostId($postid);
          }

		$conn = getconn();
		$stmt = $conn->prepare("select * from post_info where user_email =:email order by time DESC;");
		$stmt->bindParam(':email',$myemail);
		$result = $stmt->execute();
		if (!$result)
	        pdo_die($stmt);
	    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	    $num_res = count($result);
		$PageDisplay = 0;
		if(isset($_GET["page"]))$PageDisplay = $_GET["page"];
		$numPerPage = 30;
		$max_page = (int)($num_res/$numPerPage);
		if($PageDisplay>$max_page)$PageDisplay = $max_page;
		else if($PageDisplay<0)$PageDisplay = 0;
		//Jia Edit
				$conn = getconn();
				$stmt = $conn->prepare("select * from user_fav as F WHERE F.email = '".$myemail."' order by F.postid;");
				$result2 = $stmt->execute();
				if (!$result2)
			    {
			        echo "What the fuck?";
			        pdo_die($stmt);
			    }
				$result2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
				//End of Edit
				//Modified
				Print_Fav_Post($result,$myemail,$PageDisplay,$result2);
	    //Print_Post($result,$myemail,$PageDisplay);
	    if($max_page>0)
		{
			echo '<ul class="pagination">';
			for($i = 0;$i<=$max_page;$i++)
			{
				if($i == $PageDisplay)echo '<li class = "active">';
				else echo '<li>';
				echo '<a href="homepage.php?page='.$i.'">'.($i*$numPerPage+1).'-'.(($i+1)*$numPerPage).'</a>';
			}
			echo '</ul>';
		}
?>
</form>
</div>
<div class="col-xs-6 col-md-4">
  	
<?php	
	/*$conn = getconn();
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
	// Jia Test, SHoud be removed
		echo '<div class="row" >';
			echo '<div class = "row">';
			echo '<div class="addpostbtn col-xs-12 col-sm-6 col-md-8">';
				echo '<a class = "btn btn-warning" href="myfavourate.php">Go to my Favourates!</a> ';
			echo '</div>';
		echo '</div>';
		echo '</div>';
	echo '</div>';
	echo '</div>';
	echo '</div>';
	echo '</div>';*/
	if(admin_byEmail($myemail))
	{
	  echo '<h2>Welcome Admin</h2>';
	  echo '<h3><a href="adminUsr.php">Manage User</a></h3>';
	}
	$myuid = sql_get_uid_byEmail($myemail);
	display_profile($myuid);
	echo '<div style=>';
		echo '<a style="width:100%;" class="btn btn-warning" href="postjob.php">Post New Job!</a> ';
		echo '</div>';
	echo '</div>';
	echo '</div>';
	echo '</div>';
  	include_once("footer.php");
?>

</body>
</html>
<?php
/*function print_posts($res_data) {
	foreach ($res_data as $key => $row) {
		echo "<li class=\"list-group-item\">";
		echo "<span class=\"badge\">".$row["visit"]." view</span>";
		echo '<a href="show-article.php?postid='. $row["postid"] .'">'.$row["tags"].'</a>';
		echo '<div>';
		echo '<span class="label label-info">'.$row["company"].'</span>';
		echo '<span class="label label-info">'.$row["position"].'</span>';
		echo '</div>';
		echo "</li> "; 
	}
}*/


function print_text_search($SRCH)
{
	$conn = getconn();

	//echo '<ul class="list-group">';
		$token = strtok($SRCH, " \t\n");
  
	 	$stop_words = array("a", "about", "above", "after", "again", "against", "all", "am", "an", "and", "any", "are", 
	 		"aren't", "as", "at", "be", "because", "been", "before", "being", "below", "between", "both", "but", "by", 
	 		"can't", "cannot", "could", "couldn't", "did", "didn't", "do", "does", "doesn't", "doing", "don't", "down", 
	 		"during", "each", "few", "for", "from", "further", "had", "hadn't", "has", "hasn't", "have", "haven't", 
	 		"having", "he", "he'd", "he'll", "he's", "her", "here", "here's", "hers", "herself", "him", "himself", "his", 
	 		"how", "how's", "i", "i'd", "i'll", "i'm", "i've", "if", "in", "into", "is", "isn't", "it", "it's", "its",
	 		"itself", "let's", "me", "more", "most", "mustn't", "my", "myself", "no", "nor", "not", "of", "off", "on", 
	 		"once", "only", "or", "other", "ought", "our", "ours", "ourselves", "out", "over", "own", "same", "shan't", 
	 		"she", "she'd", "she'll", "she's", "should", "shouldn't", "so", "some", "such", "than", "that", "that's", 
	 		"the", "their", "theirs", "them", "themselves", "then", "there", "there's", "these", "they", "they'd", 
	 		"they'll", "they're", "they've", "this", "those", "through", "to", "too", "under", "until", "up", "very", 
	 		"was", "wasn't", "we", "we'd", "we'll", "we're", "we've", "were", "weren't", "what", "what's", "when", "want", "wants",
	 		"when's", "where", "where's", "which", "while", "who", "who's", "whom", "why", "why's", "with", "won't", 
	 		"wanted", "would", "wouldn't", "you", "you'd", "you'll", "you're", "you've", "your", "yours", "yourself", 
	 		"yourselves", 
			);
	 	$res_id = array();
		while ($token !== false)
	   	{
		   	$token = strtolower($token);
		   	if (in_array($token, $stop_words)) 
		   	{
		   		$token = strtok(" \t\n");
		   		continue;
		   	}
		   	$query = "select * from post_info 
		   	where company like '%".$token."%' or position like '%".$token."%' or title like '%".$token."%' order by time DESC;";
			$stmt = $conn->prepare($query);
			$result = $stmt->execute();
			if (!$result)
		    {
		        echo "What the fuck?";
		        pdo_die($stmt);
		    }
		    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
			foreach ($result as $row) {
				if(in_array($row["postid"], $res_id)) {
					//reset($row);
					continue;
				} 
				$info = $row["title"]." ".$row["company"]." ".$row["position"];
				$info = strtolower($info);
				$info_token = explode(" ", $info);
				if (!in_array($token, $info_token) && eregi("[^\x80-\xff]", $token)) {
					//reset($row);
					continue;
				}
				array_push($res_id, $row["postid"]);
			} 
			$token = strtok(" \t\n");
	   }
	   return $res_id;
	   //echo '</div>';
}


function searchPost($tag_array) {
	$conn = getconn();

	
	$majorClass = $tag_array[0];
	$companyName = $tag_array[1];
	$jobType = $tag_array[2];

	$query = "select postid from post_tags where (".$majorClass." = 0 or major_class = ".$majorClass.") and (".$companyName." = 0 or company = ".$companyName.") and (".$jobType." = 0 or job_type = ".$jobType.")";
	$stmt = $conn->prepare($query);
	$result = $stmt->execute();
	if (!$result)
    {
        echo "What the fuck?";
        pdo_die($stmt);
    }
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	/*
	$pid_array = array();


	while ($row = mysql_fetch_array($result)) {
		array_push($pid_array, $row["postid"]);
	}
	return $pid_array;*/
	return $result;
}

?>