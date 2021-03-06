<?php
set_include_path('..'.PATH_SEPARATOR);
require_once('lib/common_service.php');


$args = new RequestParameters();
$args->defineNonEmptyString('userId');
$args->defineEnum('size',['small','large'],['default'=>'small']);

if (! $args->isValid()){
  produceError('argument(s) invalide(s) --> '.implode(', ',$args->getErrorMessages()));
  return;
}

try{
  $data = new DataLayer();
  $descFile = $data->getAvatar($args->userId);
  $dat='data_';$dat.=$args->size;
  if ($descFile){ // l'utilisateur existe
    // si l'avatar est NULL, renvoyer l'avatar par défaut :
    $flux = is_null($descFile[$dat]) ? fopen('../images/avatar_def.png','r') : $descFile[$dat];
    $mimeType = is_null($descFile[$dat]) ? 'image/png' : $descFile['mimetype'];

    header("Content-type: $mimeType");
    fpassthru($flux);
    exit();
  }
  else
    produceError('Utilisateur inexistant');
}
catch (PDOException $e){
  produceError($e->getMessage());
}

?>
