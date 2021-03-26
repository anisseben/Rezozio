<?php
set_include_path('..'.PATH_SEPARATOR);
require_once('lib/common_service.php');
require('lib/watchdog_service.php');

$args = new RequestParameters('POST');
$args->defineString('password',['default'=>'']);
$args->defineString('pseudo',['default'=>'']);
$args->defineString('description',['default'=>'']);


if (! $args->isValid()){
  produceError('argument(s) invalide(s) --> '.implode(', ',$args->getErrorMessages()));
  return;
}

try{
  $data = new DataLayer();
  $login=$_SESSION['ident']->login;
  $profil = $data->setProfile($login,$args->password,$args->pseudo,$args->description);
  if ($profil)
        produceResult($profil);
  else
        produceError("author {$args->pseudo} wrong parameters");
}
catch (PDOException $e){
    produceError($e->getMessage());
}


?>
