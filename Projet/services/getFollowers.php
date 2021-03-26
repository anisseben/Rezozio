<?php
set_include_path('..'.PATH_SEPARATOR);

require_once('lib/common_service.php');
require_once('lib/session_start.php');
require_once('lib/watchdog_service.php');


if ( isset($_SESSION['ident'])) {
    $login=$_SESSION['ident']->login;
    $data = new DataLayer();
    $res=$data->getFollowers($login);
    produceResult($res);
}
else{
  produceError("non authentifié");
  return;
}


?>
