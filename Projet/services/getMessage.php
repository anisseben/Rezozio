<?php
set_include_path('..'.PATH_SEPARATOR);
require_once('lib/common_service.php');

$args = new RequestParameters();
$args->defineNonEmptyString('messageId');

if (! $args->isValid()){
  produceError('argument(s) invalide(s) --> '.implode(', ',$args->getErrorMessages()));
  return;
}

try{
  $data = new DataLayer();
  $message = $data->getmessage($args->messageId);
  if($message)
    produceResult($message);
  else
      produceError("message {$args->messageId} n'existe pas");
}
catch (PDOException $e){
    produceError($e->getMessage());
}


?>
