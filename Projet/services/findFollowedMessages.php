<?php
set_include_path('..'.PATH_SEPARATOR);
require_once('lib/common_service.php');
require('lib/watchdog_service.php');

$args = new RequestParameters();
$args->defineString('before',['default'=>0]);
$args->defineInt('count',['default'=>15],['min_range'=>1]);

if (! $args->isValid()){
  produceError('argument(s) invalide(s) --> '.implode(', ',$args->getErrorMessages()));
  return;
}

try{
  $data = new DataLayer();
  $follower= $_SESSION['ident']->login;
  $findFollowedMsg = $data->findFollowedMessages($follower,$args->before,$args->count);
  produceResult($findFollowedMsg);
}
catch (PDOException $e){
    produceError($e->getMessage());
}


?>
