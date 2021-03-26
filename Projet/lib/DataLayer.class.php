<?php
require_once("lib/db_parms.php");

Class DataLayer{
    private $connexion;
    public function __construct(){

            $this->connexion = new PDO(
                       DB_DSN, DB_USER, DB_PASSWORD,
                       [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                       ]
                     );



    }


    function createUser($login,$password,$pseudo){
  $sql= <<<EOD
  insert into rezozio.users (login,password,pseudo)
  values(:login,:password,:pseudo)
  returning login,pseudo;
EOD;
  $stmt=$this->connexion->prepare($sql);
  $stmt->bindValue(':login',$login);
  $stmt->bindValue(':password',password_hash($password,CRYPT_BLOWFISH));
  $stmt->bindValue(':pseudo',$pseudo);
  $stmt->execute();
  return $stmt->fetch();
}



   /*
    * Test d'authentification
    * $login, $password : authentifiants
    * résultat :
    *    Instance de Personne représentant l'utilsateur authentifié, en cas de succès
    *    NULL en cas d'échec
    */
   function authentifier($login, $password){ // version password hash
        $sql = <<<EOD
        select
        login, pseudo , password
        from rezozio.users
        where login = :login
EOD;
        $stmt = $this->connexion->prepare($sql);
        $stmt->bindValue(':login', $login);
        $stmt->execute();
        $info = $stmt->fetch();
        if ($info && crypt($password, $info['password']) == $info['password'])
              return new Identite($info['login'], $info['pseudo']);
        else
          return NULL;
    }



    function getUser($login){
      $sql = <<<EOD
      select
      rezozio.users.login as "userId, rezozio.users.pseudo
      from rezozio.users
      where rezozio.users.login = :userId
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':userId', $login);
      $stmt->execute();
      return $stmt->fetchAll();

  }




    function getProfile($login){
      $sql= <<<EOD
      select
        login as "userId", pseudo, description,
        s1.target is not null as "followed",

        s2.target is not null as "isFollower"
     from rezozio.users
     left join rezozio.subscriptions as s1 on users.login = s1.target and s1.follower = :current

     left join rezozio.subscriptions as s2 on users.login = s2.follower and s2.target = :current
     where users.login = :userId
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':userId', $login);
      $stmt->execute();
      return $stmt->fetchAll();

}



function getmessage($messageId){
  $sql = <<<EOD
  select
  messages.id as "messageId", messages.author ,users.pseudo, messages.content , messages.datetime
  from rezozio.users,rezozio.messages
  where messages.id=:messageID
EOD;
  $stmt = $this->connexion->prepare($sql);
  $stmt->bindValue(':messageID', $messageID);
  $stmt->execute();
  return $stmt->fetchAll();

}



 function postMessage($login,$content){
     $sql = <<<EOD
     insert into rezozio.messages (author,content) values (:login,:content)
     returning id
EOD;
    $stmt = $this->connexion->prepare($sql);
    $stmt->bindValue(':login',$login);
    $stmt->bindValue(':content',$content);
    $stmt->execute();
    return $stmt->fetch();
   }


   function setProfile($login,$pass,$pseudo,$desc){
     if ($pass !=''){
    $sql = "update rezozio.users set password=:pass";
    if ($pseudo!='') {
      $sql.=',pseudo=:pseu';
    }
    if($desc!=''){
      $sql.=',description=:cont';
    }
    $sql.='where login=:log returning login,pseudo';
  }
    else {if ($pseudo!='') {
    $sql.='update rezozio.users set pseudo=:pseu';
  if($desc!=''){
    $sql.=',description=:cont';
  }
  $sql.=' where login=:log returning login,pseudo';
}
  else{
    if($desc!=''){
      $sql='update rezozio.users set description=:cont where login=:log
      returning login,pseudo';
}
else {
  $sql='select login , pseudo from rezozio.users where login=:log';
}
}
}

$stmt=$this->connexion->prepare($sql);
$stmt->bindValue(':log',$login);
if($pass!=''){
$stmt->bindValue(':pass',password_hash($pass,CRYPT_BLOWFISH));
}
if ($pseudo!='') {
$stmt->bindValue(':pseu',$pseudo);
}
if ($desc!='') {
$stmt->bindValue(':cont',$desc);
}
$stmt->execute();
return $stmt->fetch();
}





function findUsers($chaine){
  $sql = <<<EOD
     select login, pseudo from rezozio.users
     where lower(login) LIKE lower(:chaine) OR lower(pseudo) LIKE lower(:chaine)

EOD;
      $stmt = $this->connexion->prepare($sql);
      $chaine= '%'.$chaine.'%';
      $stmt->bindValue(':chaine',$chaine);
      $stmt->execute();
      $res=[];
      while($users= $stmt->fetch()){
        $res[]=$users;
      }
      return $res;
}



function findMessages($author,$before=0,$count=15){
  $sql = " select * from rezozio.messages where lower(author) LIKE lower(:author)";
  if ($before !=0) {
       $sql.="and id < :before";
     }
    $sql.="order by id DESC" ;

    $stmt = $this->connexion->prepare($sql);
    $author='%'.$author.'%';
    $stmt->bindValue(':author',$author);
    if ($before!=0) {
      $stmt->bindValue(':before',$before);
    }
    $stmt->execute();
    $tab=[];
    $res=0;
    while ($res < $count && $message=$stmt->fetch()){
      $tab[]=$message;
      $res++;
    }
    return $tab;
}



function findFollowedMessages($follower,$before=0,$count=15){
  $sql = "select * from rezozio.messages where author IN (SELECT target from rezozio.subscriptions where follower=:follower)";
  if ($before !=0) {
       $sql.="and id < :before";
     }
    $sql.="order by id DESC" ;

    $stmt = $this->connexion->prepare($sql);
    $stmt->bindValue(':follower',$follower);
    if ($before!=0) {
      $stmt->bindValue(':before',$before);
    }
    $stmt->execute();
    $tab=[];
    $res=0;
    while ($res< $count && $message=$stmt->fetch()){
      $tab[]=$message;
      $res++;
    }
    return $tab;
}


function getFollowers($login){
  $sql = <<<EOD
  select rezozio.users.login as "userId", rezozio.users.pseudo, t2.follower is not null as "mutual"
  from rezozio.subscriptions as t1
  left join rezozio.subscriptions as t2 on t1.follower = t2.target and t2.follower = :target
  join rezozio.users on login = t1.follower
  where t1.target = :target

EOD;
    $stmt = $this->connexion->prepare($sql);
    $stmt->bindValue(':target',$login);
    $stmt->execute();
    return $stmt->fetchAll();
}




function Follow($login,$follower){
       $sql='insert into rezozio.subscriptions (follower,target) values(:follower,:target)';
       $stmt=$this->connexion->prepare($sql);
       $stmt->bindValue(':follower',$login);
       $stmt->bindValue(':target',$follower);
       $stmt->execute();
       return $stmt->rowCount()==1;
     }




function Unfollow($follower,$target){
   $sql= <<<EOD
   delete from rezozio.subscriptions
    where rezozio.follower=:follower and target=:target
EOD;
  $stmt = $this->connexion->prepare($sql);
  $stmt->bindValue(':followerl',$follower);
  $stmt->bindValue(':target',$target);
  $stmt->execute();
  return $stmt->rowCount()==1;
 }



   /*
    * Récupère l'avatar d'un utilisateur
    * $login : login de l'utilisateur
    * résultat :
    *   si l'utilisateur existe : table assoc
    *    'mimetype' : mimetype de l'image
    *    'data' : flux ouvert en lecture sur les données binaires de l'image
    *     si l'utilisateur n'a pas d'avatar, 'mimetype' et 'data' valent NULL
    *   si l'utilisateur n'existe pas : le résultat vaut NULL
    */
   function getAvatar($login){
     $sql = <<<EOD
           select avatar_type , avatar_small , avatar_large
           from rezozio.users
           where login=:login
EOD;
           $stmt = $this->connexion->prepare($sql);
           $stmt->bindValue(':login', $login);
           $stmt->bindColumn('avatar_type', $mimeType);
           $stmt->bindColumn('avatar_small', $flow_small, PDO::PARAM_LOB);
           $stmt->bindColumn('avatar_large',$flow_large,PDO::PARAM_LOB);
           $stmt->execute();
           $res = $stmt->fetch();
           if ($res)
              return ['mimetype'=>$mimeType,'data_small'=>$flow_small,'data_large'=>$flow_large];
           else
              return false;
         }




function uploadAvatar($login,$imageSpec){
      $sql= <<<EOD
      update rezozio.users
      set(avatar_type,avatar_large,avatar_small) = (:mime,:large,:small)
      where login =:login
EOD;
    $stmt=$this->connexion->prepare($sql);
    $stmt->bindValue(':login',$login);
    $stmt->bindValue(':mime',$imageSpec['mimetype']);
    $stmt->bindValue(':large',$imageSpec['data_large'], PDO::PARAM_LOB);
    $stmt->bindValue(':small',$imageSpec['data_small'],PDO::PARAM_LOB);
    try{
    $stmt->execute();
    return $stmt->rowCount()==1;
  }catch(PDOException $e){
    return false;
  }
}


function getSubscriptions($login){
        $sql= <<<EOD
        select rezozio.users.login as "userId", rezozio.users.pseudo, t2.target is not null as "mutual"
        from rezozio.subscriptions as t1
        left join rezozio.subscriptions as t2 on t1.target = t2.follower and t2.target= :target
        join rezozio.users on login = t1.target
        where t1.follower=:target
EOD;
        $stmt=$this->connexion->prepare($sql);
        $stmt->bindValue(':target',$login);
        $stmt->execute();
        return $stmt->fetchAll();
      }


  }

?>
