<?php
set_include_path('..'.PATH_SEPARATOR);
require_once('lib/common_service.php');
require('lib/watchdog_service.php');

$args = new RequestParameters('POST');
$args->defineNonEmptyString('source');


if (! $args->isValid()){
  produceError('argument(s) invalide(s) --> '.implode(', ',$args->getErrorMessages()));
  return;
}

try{
  $data = new DataLayer();
  $login=$_SESSION['ident']->login;
  $msg = $data->postMessage($login,$args->source);
  produceResult($msg);

}
catch (PDOException $e){
    produceError($e->getMessage());
}


?>
