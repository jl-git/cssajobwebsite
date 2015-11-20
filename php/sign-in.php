<!DOCTYPE html>
<html lang="en">
<head>
  <title>CSSA sign in page</title>
  <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
 <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
 <script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.3.14/angular.min.js"></script>
 <link rel="stylesheet" href="../css/main.css">
 <script src="js/main.js"></script>
  <style>

  </style>
</head>

<body>
<?php
  include_once("header.php");
?>

	<div class = "container" >
	<div class = "container col-sm-offset-3 col-sm-6 col-sm-offset-3" >
			<div class = "text-center">
				<h1 >Sign in</h1>
			</div>
			
			<form role="form" action = "sign-in-jump.php" onSubmit="return checkSubmit();" name = "signin" method = "POST">
			  <div class="form-group">
			    <label for="email">Email</label>
			    <input name = "email" type="email" class="form-control" id="email">
			  </div>
			  <div class="form-group">
			    <label for="pwd">Password</label>
			    <input name = "pwd" type="password" class="form-control" id="pwd">
			  </div>
			  <div class="checkbox">
			    <label><input type="checkbox"> Remember me</label>
			  </div>
			  <div class = "text-right">
				<button type="submit" class="btn btn-default" >Sign In</button>
			  </div>

			</form>
			<p style = "color: red" id="text-alert"></p>
		</div>		
	</div>

	<script language="javascript">
	   function checkSubmit() {
	   
	   	   var t = document.getElementById("text-alert");
	       var p = document.getElementById("email");
	       if(p.value == "" || p.value == null) 
	       {
	        t.innerHTML = "Please input email!";
	        return false;
	       }
	       var patt=/[a-z]+[0-9]+@cornell.edu$/;
	       if (!patt.test(p.value)){
	       	t.innerHTML = "Please input a Cornell Email (exp. netID@cornell.edu)";
	       	return false;
	       }
	       p = document.getElementById("pwd");
	       if(p.value == "" || p.value == null) 
	       {
	        t.innerHTML = "Please input password!";
	        return false;
	       }
	       return true;
	 	}
   </script>

<?php
  include_once("footer.php");
?>
</body>
</html>