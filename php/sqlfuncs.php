<?php

function delete_tutorial($tutorial_id) {
    $conn = getconn();
    $stmt = $conn->prepare("delete from tutorial where id=:id");
    $stmt->bindParam(':id', $tutorial_id);

    $result = $stmt->execute();
    if (!$result)
        pdo_die($stmt);
    return 1;
}

function update_tutorial($tutorial_id, $fileurl, $filename){
    $conn = getconn();
    $stmt = $conn->prepare("update tutorial set file_url=:fileurl, filename=:filename where id=:tutorial_id");
    
    $stmt->bindParam(':tutorial_id', $tutorial_id);
    $stmt->bindParam(':fileurl', $fileurl);
    $stmt->bindParam(':filename', $filename);

    $result = $stmt->execute();
    if (!$result)
        pdo_die($stmt);
    return 1;
}

// return insert id
function sql_add_tutoial($name, $admin_id, $decr) {
    $conn = getconn();

    $stmt = $conn->prepare("insert into tutorial(name, admin_id, time, descriptions) values(:name, :admin_id, now(), :description)");
    $stmt->bindParam(":name", $name);
    $stmt->bindParam(":admin_id", $admin_id);
    $stmt->bindParam(":description", $decr);
    $result = $stmt->execute();

    if (!$result)
    {
        echo "DataBase Error";
        pdo_die($stmt);
    }

    $insert_id = $conn->lastInsertId();

    return $insert_id;
}


function sql_get_all_tutorial() {
    $conn = getconn();

    $stmt =$conn->prepare("select * from user_student.tutorial");

    $result = $stmt->execute();
    if (!$result)
        pdo_die($stmt);

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}


function sql_update_verify($email) {
    $conn = getconn();

    $stmt = $conn->prepare("update user set verify=1 where email=:email");

    $stmt->bindParam(":email", $email);

    $result = $stmt->execute();
    if (!$result)
        pdo_die($stmt);

    
    $result = $stmt->fetchAll();

    assert(count($result) <= 1);
    if (count($result) != 0)
        return $result[0][$column];
    else
        return null;
}

function sql_is_verified($email, $type){
    if(admin_byEmail($email))return true;
    
    $conn = getconn();
    $stmt = $conn->prepare("select verify from user where email=:email");

    $stmt->bindParam(":email", $email);
    $result = $stmt->execute();
    if (!$result)
        pdo_die($stmt);

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($result) == 0) {
        return false;
    }

    if ($result[0]['verify'] == 0) {
        return false;
    } else {
        return true;
    }
}

// input : an string "(1,3,4)" "(2,5,6,9,8,7)"
function sql_get_post_by_ids($id_list) {
    $conn = getconn();
    $query = "select * from post_info where postid IN ".$id_list." order by time DESC";
    //echo $query;
    $stmt = $conn->prepare($query);

    $result = $stmt->execute();
    if (!$result)
        pdo_die($stmt);

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function sql_get_post_content_byID($postid)
{
    $conn = getconn();

    $stmt = $conn->prepare("select * from post_content where postid = ".$postid);
    //echo "select * from post_content where postid = ".$postid;

    $result = $stmt->execute();
    if (!$result)
        pdo_die($stmt);

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function sql_update_visit($postid) {
    $conn = getconn();
    $stmt = $conn->prepare("select visit from post_info where postid=:postid");
    $stmt->bindParam(":postid", $postid);
    $result = $stmt->execute();
    if (!$result)
        pdo_die($stmt);

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    

    $newvisit = $result[0]['visit'] + 1;

    $stmt = $conn->prepare("update post_info set visit=:newvisit where postid=:postid");
    $stmt->bindParam(":newvisit", $newvisit);
    $stmt->bindParam(":postid", $postid);
    $result = $stmt->execute();

    if (!$result)
        pdo_die($stmt);

    $result = $stmt->fetchAll();

    assert(count($result) <= 1);
    if (count($result) != 0)
        return $result[0][$column];
    else
        return null;

//  $stmt = $conn->prepare("update post_info set status=:new_status where id=:order_id");
}

function sql_add_post($useremail,$email, $company_name, $position, $description, $job_content, $job_type, $major, $job_year, $url, $visa, $type)
{
    $conn = getconn();
    $post_id = $conn->lastInsertId();

    if ($type == 0) {
        $stmt = $conn->prepare("insert into post_info(user_email, email, company, position, title, time, visit, fav, url, visa, post_type) values(:useremail,:email, :company, :position, :title, now(), 0, 0, :url, :visa, 0)");
    } else {
        $stmt = $conn->prepare("insert into post_info(user_email, email, company, position, title, time, visit, fav, url, visa, post_type) values(:useremail,:email, :company, :position, :title, now(), 0, 0, :url, :visa, 1)");
    }


    $stmt->bindParam(':useremail',$useremail);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':company', $company_name);
    $stmt->bindParam(':position', $position);

    $stmt->bindParam(':title', $description);
    $stmt->bindParam(':url', $url);
    $stmt->bindParam(':visa', $visa);
    
    $result = $stmt->execute();
    $post_id = $conn->lastInsertId();
    $res = $post_id;

    if (!$result)
        pdo_die($stmt);

    $stmt = $conn->prepare("insert into post_content(postid, content) values(:postid, :content)");

    $stmt->bindParam(':postid', $post_id);
    $stmt->bindParam(':content', $job_content);

    $result = $stmt->execute();

    if (!$result)
        pdo_die($stmt);
    
    if ($type == 0) {
        $stmt = $conn->prepare("insert into post_tags(postid, job_year, major_class, company, job_type) values(:postid, :jy, :mc, :company, :jt)");
        $stmt->bindParam(':postid', $post_id);
        $stmt->bindParam(':jy', substr($job_year, 0, 4));
        $stmt->bindParam(':mc', $major);
        $stmt->bindParam(':company', $company_name);
        $stmt->bindParam(':jt', $job_type);

        $result = $stmt->execute();
        $post_id = $conn->lastInsertId();

        if (!$result)
            pdo_die($stmt);
    }
    return $res;
}

function update_post($postid,$useremail,$email, $company_name, $position, $description, $job_content, $job_type, $major, $job_year,$url,$visa,$visit)
{
    $conn = getconn();

    $stmt = $conn->prepare("update post_info set user_email=:useremail,email=:email, company=:company, position=:position, title=:title, time=now(), visit=:visit,url=:url, visa=:visa where postid=:postid");

    $stmt->bindParam(':postid',$postid);
    $stmt->bindParam(':useremail',$useremail);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':company', $company_name);
    $stmt->bindParam(':position', $position);
    $stmt->bindParam(':title', $description);
    $stmt->bindParam(':visit',$visit);
    $stmt->bindParam(':url', $url);
    $stmt->bindParam(':visa', $visa);
    
    $result = $stmt->execute();

    if (!$result)
        pdo_die($stmt);

    $stmt = $conn->prepare("update post_content set content=:content where postid=:postid");

    $stmt->bindParam(':postid', $postid);
    $stmt->bindParam(':content', $job_content);

    $result = $stmt->execute();

    if (!$result)
        pdo_die($stmt);
    
    $stmt = $conn->prepare("update post_tags set job_year=:jy, major_class=:mc, company=:company, job_type=:jt where postid=:postid");

    $stmt->bindParam(':postid', $postid);
    $stmt->bindParam(':jy', substr($job_year, 0, 4));
    $stmt->bindParam(':mc', $major);
    $stmt->bindParam(':company', $company_name);
    $stmt->bindParam(':jt', $job_type);
  
    $result = $stmt->execute();

    if (!$result)
        pdo_die($stmt);
    return 1;
}

function sql_delete_post_byPostId($postid)
{
    $conn = getconn();
    $stmt = $conn->prepare("delete from post_info where postid =".$postid);
    //$stmt = $conn->prepare("delete from post_info where postid = :postid");
    //$stmt->bindParam(':postid',$postid);
    $result = $stmt->execute();
    if (!$result)
        pdo_die($stmt);
    //$stmt = $conn->prepare("delete from post_content where postid =".$postid);
    //$stmt = $conn->prepare("delete from post_content where postid = :postid");
    //$stmt->bindParam(':postid',$postid);
    //$result = $stmt->execute();
    //if (!$result)
    //    pdo_die($stmt);
    //$stmt = $conn->prepare("delete from reply where postid =".$postid);
    //$stmt = $conn->prepare("delete from reply where postid = :postid");
    //$stmt->bindParam(':postid',$postid);
    //$result = $stmt->execute();
    //if (!$result)
    //    pdo_die($stmt);
}

function sql_add_reply($email, $reply_content, $post_id, $parent)
{
    $conn = getconn();
    $stmt = $conn->prepare("insert into reply(parentid, postid, email, content, time) values(:parentid, :postid, :email, :content, now())");

    $stmt->bindParam(':parentid', $parent);
    $stmt->bindParam(':postid', $post_id);
    $stmt->bindParam(':content', $reply_content);
    $stmt->bindParam('email', $email);
    $result = $stmt->execute();
    if (!$result)
        pdo_die($stmt);

}

function sql_get_reply($post_id)
{
    $conn = getconn();
    $stmt = $conn->prepare("select * from reply where postid = :postid order by time");
    $stmt->bindParam(':postid', $post_id);

    $result = $stmt->execute();
    if (!$result)
        pdo_die($stmt);

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}
function sql_get_userInfo_byEmail($email)
{
    $conn = getconn();
    $stmt = $conn->prepare("select * from user where email = :email");
    $stmt->bindParam(':email', $email);

    $result = $stmt->execute();
    if (!$result)
    {
        echo "What the fuck?";
        pdo_die($stmt);
    }
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function sql_get_stuInfo_byEmail($email)
{
    $conn = getconn();
    $stmt = $conn->prepare("select * from student where email = :email");
    $stmt->bindParam(':email', $email);

    $result = $stmt->execute();
    if (!$result)
    {
        echo "What the fuck?";
        pdo_die($stmt);
    }
        

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}
function sql_get_empInfo_byEmail($email)
{
    $conn = getconn();
    $stmt = $conn->prepare("select * from employer where email = :email");
    $stmt->bindParam(':email', $email);

    $result = $stmt->execute();
    if (!$result)
    {
        echo "What the fuck?";
        pdo_die($stmt);
    }
        

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function admin_byEmail($email)
{
    $conn = getconn();
    $stmt = $conn->prepare("select * from user where type=0 and email = :email");
    $stmt->bindParam(':email', $email);
    $result = $stmt->execute();
    if (!$result)
    {
        echo "What the fuck?";
        pdo_die($stmt);
    }
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if(count($result)>0)return true;
    return false;
}

function sql_insert_userInfo($email,$username,$password,$type,$hash)
{
    $conn = getconn();
    $stmt = $conn->prepare("insert into user(email,verify,name,password,type,hash) values(:email,0,:name,:password,".$type.",:hash)");
    //echo "insert into user(email,verify,password,type) values(:email,0,:password".$type.")";
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':name',$username);
    $stmt->bindParam(':hash', $hash);

    $result = $stmt->execute();
    if (!$result)
    {
        echo "insert userinfo failed";
        pdo_die($stmt);
    }
}
function sql_insert_stuInfo($email,$username,$hash,$password,$type)
{
    $conn = getconn();
    if ($type == 'stu')
        $stmt = $conn->prepare("insert into student(email,name,hash,verified,password,isalumni) values(:email,:username,:hash,0,:password,0)");
    else if ($type == 'alu')
        $stmt = $conn->prepare("insert into student(email,name,hash,verified,password,isalumni) values(:email,:username,:hash,0,:password,1)");
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':hash', $hash);
    $stmt->bindParam(':password', $password);

    $result = $stmt->execute();
    if (!$result)
    {
        echo "What the fuck?";
        pdo_die($stmt);
    }
    
}
function sql_insert_empInfo($email,$username,$hash,$password)
{
    $conn = getconn();
    $stmt = $conn->prepare("insert into employer(email,name,hash,verified,password) values(:email,:username,:hash,0,:password)");
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':hash', $hash);
    $stmt->bindParam(':password', $password);

    $result = $stmt->execute();
    if (!$result)
    {
        echo "What the fuck?";
        pdo_die($stmt);
    }
}
function sql_insert_notification($replyedemail, $postid, $readtag, $replyeremail, $title)
{
    $conn = getconn();
    //echo "insert into notification(email,postid,readtag,time,replyername) values('".$email."',".$postid.",".$readtag.",now(),'".$replyername."')";
    $stmt = $conn->prepare("insert into notification(replyedemail,postid,readtag,time,replyeremail,title) values('".$replyedemail."',".$postid.",".$readtag.",now(),'".$replyeremail."','".$title."')");

    $result = $stmt->execute();
    if (!$result)
    {
        echo "What the fuck?";
        pdo_die($stmt);
    }
}
function pdo_die($stmt)
{
    var_dump($stmt->errorInfo());
    die("PDO error!");
}

function getconn()
{
    static $conn;

    if ($conn)
        return $conn;
        
    $dbname = "user_student";
    $user = "cssaadmin"; $pw = "cssaadmin123";
    $host = "cssadbinstance.ccmgeu2ghiy1.us-east-1.rds.amazonaws.com";
        
    $dsn = "mysql:host=$host;dbname=$dbname"; // Data source name
    $conn = new PDO($dsn, $user, $pw);
    return $conn;
}
/*function Print_Post($post_row,$email,$page)
{
    if(count($post_row) == 0)return;
    $flag = 0;
    $cnt = 0;
    echo '<div class="panel-group">';
    echo '<div class="panel panel-default">';
    echo '<div class="panel-heading">';
    echo '<h4 class="panel-title">';
    echo '<a data-toggle="collapse" href="#collapse1">This week<i class="glyphicon glyphicon-triangle-bottom"></i></a>';
    echo '</h4>';
    echo '</div>';
    echo '<div id="collapse1" class="panel-collapse collapse in">';
    echo '<ul class="list-group">';

    $total = 0;
    $index = 0;

    foreach ($post_row as $row)
    {
        $cnt = $cnt + 1;
        if(strtotime($row['time']) > strtotime('now'))continue;
        if( $flag == 0 && strtotime($row['time']) < strtotime('-7 day'))
        {
            $flag = 1;
            echo '</ul>';
            echo '</div>';
            echo '</div>';
            echo '<div class="panel panel-default">';
            echo '<div class="panel-heading">';
            echo '<h4 class="panel-title">';
            echo '<a data-toggle="collapse" href="#collapse2">A Week ago<i class="glyphicon glyphicon-triangle-bottom"></i></a>';
            echo '</h4>';
            echo '</div>';
            $in = "";

            if($total == 0)$in = "in";

            echo '<div id="collapse2" class="panel-collapse collapse '.$in.'">';
            echo '<ul class="list-group">';
        }
        $cnt = $cnt + 1;
        if($cnt<$page*30)continue;
        if($cnt % 2 == 0) echo '<li class="list-group-item">';
        else echo '<li class="list-group-item list-group-item-info">';
        echo '<div style="padding:5px">';
        if($email == $row["user_email"] || admin_byEmail($email))
            echo '<button class="btn btn-danger" type=submit name="deletePost[]" value ='.$row["postid"].'>&times;</button>';
        else
            echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        echo '<a href="show-article.php?postid='.$row["postid"].'">'.$row["title"].'</a>';
        echo '<span class = "badge pull-right">'.$row["visit"].' view>';
        echo '</span>';
        echo '<button class = "starButtonOff" id="myImage'. $index .'" type=submit name="deleteFav" value ='.$row["postid"].' alt="STAR" width="34" height="26">';        $index = $index + 1;
        echo '</div>';
        echo '<div style="padding:5px">';
        echo '<span class="label label-info pull-left">'.$row["company"].'</span>';
        echo '<span class="label label-info pull-left">'.$row["position"].'</span>';
        echo '<small class = "pull-right" style="text-color:gray">Post by: '.$row["user_email"].'</small>';
        echo '</div>';
        echo '</li>';

        $total = $total + 1;
        if($cnt>($page+1)*30)break;
    }
    echo '</ul>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
}*/
function Print_Fav_Post($post_row,$email,$page,$fav_row)
{
    if(count($post_row) == 0)return;
    $flag = 0;
    $cnt = 0;
    echo '<div class="panel-group">';
    echo '<div class="panel panel-default">';
    echo '<div class="panel-heading">';
    echo '<h4 class="panel-title">';
    echo '<a data-toggle="collapse" href="#collapse1">This week<i class="glyphicon glyphicon-triangle-bottom"></i></a>';
    echo '</h4>';
    echo '</div>';
    echo '<div id="collapse1" class="panel-collapse collapse in">';
    echo '<ul class="list-group">';

    $total = 0;
    $index = 0;
    $favs = array();
    //print_r($fav_row);
    //print_r($row2['postid']);
    //echo '<br><br>';
    foreach ($fav_row as $row2)
    {
        //print_r($row2['postid']);
        array_push($favs, $row2['postid']);
        //print_r($favs);
    }
    //print_r($favs);


    foreach ($post_row as $row)
    {
        //$cnt = $cnt + 1;
        if(strtotime($row['time']) > strtotime('now'))continue;
        if( $flag == 0 && strtotime($row['time']) < strtotime('-7 day'))
        {
            $flag = 1;
            echo '</ul>';
            echo '</div>';
            echo '</div>';
            echo '<div class="panel panel-default">';
            echo '<div class="panel-heading">';
            echo '<h4 class="panel-title">';
            echo '<a data-toggle="collapse" href="#collapse2">A Week ago<i class="glyphicon glyphicon-triangle-bottom"></i></a>';
            echo '</h4>';
            echo '</div>';
            $in = "";

            if($total == 0)$in = "in";

            echo '<div id="collapse2" class="panel-collapse collapse '.$in.'">';
            echo '<ul class="list-group">';
        }
        $cnt = $cnt + 1;
        if($cnt<$page*30)continue;
        //if($cnt % 2 == 0) echo '<li class="list-group-item">';
        //else echo '<li class="list-group-item list-group-item-info">';
        echo '<li class="list-group-item">';
        echo '<div style="padding:5px">';
        //else
        //    echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        echo '<a href="show-article.php?postid='.$row["postid"].'">'.$row["title"].'</a>';
        //echo $row["postid"];
        if($email == $row["user_email"] || admin_byEmail($email))
            echo '<a href="#" class="deletePost pull-right" data-email ='.$row["postid"].'>&times;</a>';
        else
            echo '<div class = "pull-right" style="color: white;margin-left:10px; font-size: 160%;"">&times;</div>';
        if(in_array($row["postid"], $favs)) 
        {
            //echo $row["postid"];
            //echo '<br>';
            //print_r($favs);
            //print_r($row2);
            echo '<button class = "star glyphicon glyphicon-star pull-right" id="myImage'. $index .'" type=submit name="deleteFav" value ='.$row["postid"].' alt="STAR" width="34" height="26">';
        }
        else
        {
            echo '<button class = "star glyphicon glyphicon-star-empty pull-right" id="myImage'. $index .'" type=submit name="addFav" value ='.$row["postid"].' alt="STAR" width="34" height="26">';
        }
        echo '</button>';
        //echo '<img id="myImage'. $index .'" type=submit name="deleteFav" value ='.$row["postid"].' onclick="changeImage(\'myImage'. $index .'\');document.forms[1].submit()" src="../pictures/jiaStaron.png" alt="STAR" width="34" height="26">';
        $index = $index + 1;
        echo '<span class = "badge pull-right">'.$row["visit"].' view';
        echo '</span>';
        echo '</div>';
        echo '<div style="padding:15px">';
        echo '<span class="label label-info pull-left">'.$row["company"].'</span>';
        echo '<span class="label label-info pull-left" style ="margin-left:10px">'.$row["position"].'</span>';
        $uid = sql_get_uid_byEmail($row["user_email"]);
        echo '<small class = "pull-right" style="text-color:gray">Post by: <a href = "profile.php?uid='.$uid.'">'.sql_get_username_byEmail($row["user_email"]).'</a></small>';
        echo '</div>';
        echo '</li>';

        $total = $total + 1;
        if($cnt>($page+1)*30)break;
    }
    echo '</ul>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
}

function update_post_file($postid, $fileurl, $filename){
    $conn = getconn();
    $stmt = $conn->prepare("select file_url from post_info where postid=:postid");
    $stmt->bindParam(':postid',$postid);
    $result = $stmt->execute();
    if (!$result)
        pdo_die($stmt);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $oldpath = $result[0]['file_url'];
    if($oldpath != NULL){
        unlink($oldpath);
    }

    $stmt = $conn->prepare("update post_info set file_url=:fileurl, filename=:filename where postid=:postid");
    
    $stmt->bindParam(':postid',$postid);
    $stmt->bindParam(':fileurl',$fileurl);
    $stmt->bindParam(':filename',$filename);

    $result = $stmt->execute();
    if (!$result)
        pdo_die($stmt);
    return 1;
}
//New functin for profile and uid by Ziyang
    function display_profile($uid)
    {
      $result = sql_get_profile_byID($uid);
      if(count($result)==0)
      {
        echo '<h2><b>No such user!!</b></h2>';
        return;
      }
      if($result[0]['type'] == 0)
      {
        echo '<h2>😂😭ps</h2>';
        return;
      }
      else if($result[0]['type'] == 2)
      {
        $res = sql_get_stuInfo_byEmail($result[0]['email']);
        Display_stu($res);
      }
      else if($result[0]['type'] == 1)
      {
        $res = sql_get_empInfo_byEmail($result[0]['email']);
        Display_emp($res);
      }
    }

    function sql_get_profile_byID($uid)
    {
      $conn = getconn();
      $stmt = $conn->prepare("select * from user where uid = ".$uid);

      $result = $stmt->execute();
      if (!$result)
          pdo_die($stmt);
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      return $result;
    }

    function sql_get_uid_byEmail($email)
    {
      $conn = getconn();
      $stmt = $conn->prepare("select uid from user where email = :email");
      $stmt->bindParam(':email', $email);

      $result = $stmt->execute();
      if (!$result)
          pdo_die($stmt);
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      if(count($result)==0)return 0;
      return $result[0]['uid'];
    }
    function sql_get_username_byEmail($email)
    {
      $conn = getconn();
      $stmt = $conn->prepare("select name from user where email = :email");
      $stmt->bindParam(':email', $email);

      $result = $stmt->execute();
      if (!$result)
          pdo_die($stmt);
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      if(count($result)==0)return 0;
      return $result[0]['name'];
    }

    function Display_stu($res)
    {
      $row = $res[0];
      $code = decode_public($res[0]['code']);
      $isAdmin = admin_byEmail($_SESSION['email']);
      $myEmail = ($res[0]['email']==$_SESSION['email']);
      echo '<ul class="list-group">';
      echo '<li class="list-group-item"><b>Name</b>: '.$row["first_name"].' '.$row["last_name"].'</li>';
      echo '<li class="list-group-item"><b>Username</b>: '.$row["name"].'</li>';
      if($myEmail || $isAdmin || $code[0]==1)echo '<li class="list-group-item"><b>Phone number</b>: '.$row["phone_number"].'</li>';
      if($myEmail || $isAdmin || $code[1]==1)echo '<li class="list-group-item"><b>Address</b>: '.$row["address"].'</li>';
      if($myEmail || $isAdmin || $code[2]==1)echo '<li class="list-group-item"><b>LinkedIn hompage</b>: '.$row["Linkedin"].'</li>';
      if($myEmail || $isAdmin || $code[3]==1)echo '<li class="list-group-item"><b>Expected Graduation Year</b>: '.substr($row["grad_year"], 0, 7).'</li>';
      if($myEmail || $isAdmin || $code[4]==1)echo '<li class="list-group-item"><b>Major</b>: '.$row["major"].'</li>';
      echo '</ul>';
    }

    function Display_emp($res)
    {
      $row = $res[0];
      $code = decode_public($res[0]['code']);
      $isAdmin = admin_byEmail($_SESSION['email']);
      $myEmail = ($res[0]['email']==$_SESSION['email']);
      echo '<ul class="list-group">';
      echo '<li class="list-group-item"><b>Name</b>: '.$row["first_name"].' '.$row["last_name"].'</li>';
      echo '<li class="list-group-item"><b>Username</b>: '.$row["name"].'</li>';
      if($myEmail || $isAdmin || $code[0]==1)echo '<li class="list-group-item"><b>Phone number</b>: '.$row["phone_number"].'</li>';
      if($myEmail || $isAdmin || $code[1]==1)echo '<li class="list-group-item"><b>Address</b>: '.$row["address"].'</li>';
      if($myEmail || $isAdmin || $code[2]==1)echo '<li class="list-group-item"><b>LinkedIn hompage</b>: '.$row["Linkedin"].'</li>';
      if($myEmail || $isAdmin || $code[3]==1)echo '<li class="list-group-item"><b>Company Name</b>: '.$row["company"].'</li>';
      if($myEmail || $isAdmin || $code[4]==1)echo '<li class="list-group-item"><b>Position</b>: '.$row["position"].'</li>';
      echo '</ul>';
    }

    function encode_public($array)
    {
      $num = 5;//count($array);
      $res = 0;
      for($i=0;i<$num;$i++)
      {
        $res = ($res<<1)&$array[$i];
      }
      return $res;
    }

    function decode_public($code)
    {
      $num = 5;
      $array = array();
      for($i=0;$i<$num;$i++)
      {
        $array[$num-1-$i] = $code & 1;
        $code >>= 1;
      }
      return $array;
    }
?>