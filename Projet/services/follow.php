<?php
set_include_path('..'.PATH_SEPARATOR);
require_once('lib/common_service.php');
require('lib/watchdog_service.php');

$args = new RequestParameters();
$args->defineNonEmptyString('target');

if (! $args->isValid()){
  produceError('argument(s) invalide(s) --> '.implode(', ',$args->getErrorMessages()));
  return;
}

try{
    $data = new DataLayer();
    $login=$_SESSION['ident']->login;
    $follower = $data->Follow($login,$args->target);
    if ($follower)
        produceResult($follower);
    else
        produceError("author {$args->taregt} is already been followed");
} catch (PDOException $e){
    produceError($e->getMessage());
}

?>
