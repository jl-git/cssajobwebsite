<!DOCTYPE html>
<html lang="en">
<head>
  <title>CSSA sign in page</title>
  <meta http-equiv="refresh" content="10; URL=index.php" charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
 <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
 <script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.3.14/angular.min.js"></script>
 <link rel="stylesheet" href="http://localhost/cssajobwebsite/css/main.css">
 <script src="js/main.js"></script>
  <style>
  </style>
</head>

<body>
    <?php
    include("header.php");
    $server = mysql_connect("cssadbinstance.ccmgeu2ghiy1.us-east-1.rds.amazonaws.com", "cssaadmin", "cssaadmin123"); 
    if (!$server) { 
        print "Error - Could not connect to MySQL"; 
        exit; 
    }
    $db = mysql_select_db("user_student"); 
    if (!$db) { 
        print "Error - Could not select the guest database"; 
        exit; 
    }

    $email = $_POST['email'];
    $pwd = $_POST['pwd'];

    $query1 = "select S.password from employer S where S.email ='". $email."';";
    $alu_result = mysql_query($query1);

    $query2 = "select S.password from student S where S.email ='". $email."';";
    $stu_result = mysql_query($query2);
    if(!$alu_result || !$stu_result){
        print "Error - Failed to get result from stu or employer"; 
        exit; 
    }    


    $stu_row = mysql_fetch_array($stu_result);
    $alu_row = mysql_fetch_array($alu_result);

    if (!$stu_row && !$alu_row) {
      echo "<p>Email Invalid!</p><br>";
    } else {
      if($stu_row){
        if($stu_row[0] == $pwd){
           echo "<p>Hello!</p><br>";
        } else{
           echo "<p>Wrong Password!</p><br>";
        }
      } else {
        if($alu_row[0] == $pwd){
           echo "<p>Hello!</p><br>";
        } else{
           echo "<p>Wrong Password!</p><br>";
        }
      }
    }
    ?>
    <p>Pages will redirect automatically after 10 seconds</p>
    </div>


<?php
  include("footer.php");
?>
</body>
</html>