<?php
set_include_path('..'.PATH_SEPARATOR);

require_once('lib/common_service.php');
require_once('lib/session_start.php');


if ( ! isset($_SESSION['ident'])) {
  $args = new RequestParameters('POST');
  $args->defineNonEmptyString('login');
  $args->defineNonEmptyString('password');

  if (! $args->isValid()){
   produceError('argument(s) invalide(s) --> '.implode(', ',$args->getErrorMessages()));
   return;
  }

  else  {
    $data = new DataLayer();
    $info=$data->authentifier($args->login,$args->password);
    if(! is_null($info)){
      $_SESSION['ident']=$info;
      produceResult($info);

    }
    else {
       produceError("utilisateur n'existe pas");
    }
  }

} else {
   produceError("déjà authentifié");
   return;
}
?>
