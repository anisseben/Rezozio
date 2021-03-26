<?php

  require_once(__DIR__.'/lib/functionsToHTML.php');
  $dataPersonne ="";    // si utilisateur non authentifié, data-personne n'est pas défini


  if (isset($personne)) // l'utilisateur est authentifié
     $dataPersonne = 'data-personne="'.htmlentities(json_encode($personne)).'"'; // l'attribut data-personne contiendra l'objet personne, en JSON
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
 <meta charset="UTF-8" />
 <title>Page rezozio</title>
 <link rel="stylesheet" href="style/style.css" />
 <script src="js/fetchUtils.js"></script>
 <script src="js/gestion_log.js"></script>
 <script src="js/action_post.js"></script>
 <script src="js/filtre_messages.js"></script>
 <script src="js/filtre_Users.js"></script>
 <script src="js/gestion_follow.js"></script>

</head>
<?php
  echo "<body $dataPersonne>";
?>
  <h1>Rezozio</h1>


<section id="main">

  <section id="espace_variable">
   <section class="deconnecte">
     <form method="POST" action="services/login.php"  id="form_login">
      <fieldset>
       <legend>Connexion</legend>
       <label for="login">Login :</label>
       <input type="text" name="login" id="login" required="" autofocus=""/></br>
       <label for="password">Mot de passe :</label>
       <input type="password" name="password" id="password" required="required" /></br>
       <button type="submit" name="valid">OK</button></br>
       <output  for="login password" name="message"></output>
       </fieldset>
     </form>
       <button id="goCreateUser" >createUser</button>
      </fieldset>

      <section id="secnew" hidden>
        <form method="POST" action="services/createUser.php" id="createUser">
         <fieldset>
           <label for="userId">Login :</label>
           <input type="text" name="userId" id="userId" required="required" autofocus/>
           <label for="pseudo">Pseudo :</label>
           <input type="text" name="pseudo" id="pseudo" required="required" autofocus/>
          <label for="password">Mot de passe :</label>
          <input type="password" name="password" id="password" required="required" />
          <button type="submit" name="valid">OK</button>
          <output  for="login password" name="message"></output>
         </fieldset>
        </form>
        <h4 id="doneUser"></h4>
      </section>
   </section>
   <section class="connecte">
    <img id="avatar" alt="mon avatar" src="" />
    <h2 id="titre_connecte"></h2>




    <form  id="form_upload" action="">
     <fieldset>
        <legend>Upload profile by </legend>
        <select name="upprof" id='sltup'>
          <option value="setpro">Setprofil</option>
          <option value="uploadphoto">Upload avatar</option>
        </select><br />
      </fieldset>
    </form>


    <button id='letsUpload'>Upload</button>


  <section id="Setprofile" hidden>
    <form method="POST" action="services/setProfile.php" id='stpf'>
     <fieldset>
       <label for="description">Description :</label>
       <input type="text" name="description" id="description"  autofocus/>
       <label for="pseudo">Pseudo :</label>
       <input type="text" name="pseudo" id="pseudo"  autofocus/>
      <label for="password">Mot de passe :</label>
      <input type="password" name="password" id="password"  />
      <button type="submit" name="valid">OK</button>
     </fieldset>
    </form>
  </section>
  <section id="UploadAvatare" hidden>
    <form id="uat" method="POST" action="services/uploadAvatar.php" enctype="multipart/form-data">
     <fieldset>
       <legend>Nouvel avatar</legend>
        <input type="file" name="image" required="required"/>
        <button type="submit" name="valid" value="envoyer">Envoyer</button>
     </fieldset>
    </form>
  </section>
  <p id='here'> </p>
    <button id="logout">Déconnexion</button>
   </section>
   </section>





<section id=fil_message >

  <section id="recherche_messages">

  <form method="POST" id="form_filtre" action="services/findMessages.php" >
   <fieldset>
      <legend>Filtrer les message par :</legend>
      <select id="selec" name="filtre">
        <option value="aucun">Aucun filtre</option>
        <option value="followers" class="connecte">Par followers</option>
      </select>
      <br/>
      <button type="submit" name="valid" value="envoyer">Filter</button>
    </form>

        <form method="POST" action="services/findMessages.php" id="form_filtre_msg_users">
        <input type="text" name="author" id="user" placeholder="  Chercher par user"/>
        <button type="submit" name="valid" value="envoyer">Recherche</button>
        </form>


    </fieldset>




  </section>


 <section id="messages">
 </section>


  <section id='post_message'>
     <form method="POST" action="services/postMessage.php" name="form_post_msg">
        <input type="text" name="source" id="message" placeholder="Envoyer un message"/>
        <button type="submit" name='valid' value="envoyer">Envoyer</button>
     </form>
  </section>

</section>




<section id="followers_and_users">



  <form method="POST" id="get_followers" action="services/getFollowers.php" class="connecte">
    <fieldset>
    <legend>Get followers</legend>
    <select name="getFollowers" id="select_filtre">
      <option value='followers'>Followers</option>
      <option value="subcribers">subcribers</option>
    </select>
    <button type="submit" name='valid' value="envoyer">Recherche</button>
  </fieldset>
  </form>

  <form method="POST" id="form_find_user" action="services/findUsers.php">
    <input type="text" name="searchedString" id="users" placeholder="  Chercher un user"/>
    <button type="submit" name="valid" value="envoyer">Recherche</button>
  </form>

  <section id="filtre_erreur">
  </section>

  <section id="filter_result">

  </section>



</section>

</section>


</body>
</html>
