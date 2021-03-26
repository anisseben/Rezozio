<?php
set_include_path('..'.PATH_SEPARATOR);
require_once('lib/common_service.php');

$args = new RequestParameters();
$args->defineString('author',['default'=>'']);
$args->defineInt('before',['default'=>0]);
$args->defineInt('count',['default'=>15],['min_range'=>1]);

if (! $args->isValid()){
  produceError('argument(s) invalide(s) --> '.implode(', ',$args->getErrorMessages()));
  return;
}

try{
  $data = new DataLayer();
  $findMessages = $data->findMessages($args->author,$args->before,$args->count);
  produceResult($findMessages);
}
catch (PDOException $e){
    produceError($e->getMessage());
}


?>
