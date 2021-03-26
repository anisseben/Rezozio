<?php
set_include_path('..'.PATH_SEPARATOR);
require_once('lib/common_service.php');

$args = new RequestParameters('POST');
$args->defineNonEmptyString('userId');
$args->defineNonEmptyString('password');
$args->defineNonEmptyString('pseudo');

if (! $args->isValid()){
  produceError('argument(s) invalide(s) --> '.implode(', ',$args->getErrorMessages()));
  return;
}

try{
  $data = new DataLayer();
  $user = $data->createUser($args->userId,$args->password,$args->pseudo);
  if($user)
    produceResult($user);
  else
      produceError("user {$args->user} already exist ");
}
catch (PDOException $e){
    produceError($e->getMessage());
}


?>
