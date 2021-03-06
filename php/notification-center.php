<!DOCTYPE HTML>
<head>
	<title>Notification Center</title>
	<meta charset="utf-8">
	 <meta name="viewport" content="width=device-width, initial-scale=1">
	 <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	 <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	 <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
	 <script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.3.14/angular.min.js"></script>
	 <link rel="stylesheet" href="../css/main.css">
	 <link rel="stylesheet" href="../css/profile.css">

	 <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.0/css/bootstrap-toggle.min.css" rel="stylesheet">
	 <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.0/js/bootstrap-toggle.min.js"></script>
	 <script src="js/main.js"></script>

</head>

<body>
<?php
  session_start();
  include_once("sqlfuncs.php");
  include_once("header.php");
?>
<div class="container">
  <h2>Notification Center</h2>
  <div class="tab-content">
      <?php
          $conn = getconn();
          $stmt = $conn->prepare("select * from notification where replyedemail=:email order by time DESC");
          $stmt->bindParam(":email", $_SESSION['email']);
          
          $result = $stmt->execute();
          if (!$result)
            pdo_die($stmt);
          $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

          $readCnt = 0;
          $readArr = array();
          $unreadCnt = 0;
          $unreadArr = array();
          foreach ($result as $row) {
            // unread notes
            if ($row['readtag'] == 0) {
              $unreadCnt = $unreadCnt + 1;
              $unreadArr[$unreadCnt] = $row;
            }
            else if ($row['readtag'] == 1) {
              $readCnt = $readCnt + 1;
              $readArr[$readCnt] = $row;
            }
          }
          
          echo '<div class="notes">';
          echo '<table class="table table-striped">';
          echo '<tr>';
          echo '<th>Replyer</th>';
          echo '<th>Message</th>';
          echo '<th>Time</th>';
          echo '</tr>';
          // first print unread notes ordered by time
          for ($i = 1; $i <= $unreadCnt; $i++) {
            echo '<tr>';
            echo '<td><b>'.$unreadArr[$i]['replyeremail'].'</b></td>';
            echo '<td><b><a href="show-article.php?postid='.$unreadArr[$i]['postid'].'">"'.$unreadArr[$i]['title'].'"</a></b></td>';
            echo '<td><b>'.$unreadArr[$i]['time'].'</b></td>';
            echo '</tr>';
          }

          // second print read notes ordered by time
          for ($i = 1; $i <= $readCnt; $i++) {
            echo '<tr>';
            echo '<td>'.$readArr[$i]['replyeremail'].'</td>';
            echo '<td><a href="show-article.php?postid='.$readArr[$i]['postid'].'">"'.$readArr[$i]['title'].'"</a></td>';
            echo '<td>'.$readArr[$i]['time'].'</td>';
            echo '</tr>';
          }
          echo '</table>';
          echo '</div>';
      ?>         
  </div>
</div>
<?php include_once("footer.php");?>
</body>
</html>