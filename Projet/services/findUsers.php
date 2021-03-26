<?php
set_include_path('..'.PATH_SEPARATOR);
require_once('lib/common_service.php');

$args = new RequestParameters();
$args->defineNonEmptyString('searchedString');

if (! $args->isValid()){
  produceError('argument(s) invalide(s) --> '.implode(', ',$args->getErrorMessages()));
  return;
}

try{
    $data = new DataLayer();
    $user = $data->findUsers($args->searchedString);
    if ($user)
          produceResult($user);
    else
          produceError("veuillez saisir un nom");

} catch (PDOException $e){
    produceError($e->getMessage());
}

?>
