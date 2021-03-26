<?php
set_include_path('..'.PATH_SEPARATOR);
require_once('lib/common_service.php');
require_once('lib/watchdog_service.php');

$args = new RequestParameters('POST');
$args->image=$_FILES['image'];

if (!isset($_FILES['image']) || $_FILES['image']['tmp_name']==''){
  produceError('Fichier image non reçu');   // cas des fichiers trop volumineux
  return;
} else if (strpos($_FILES['image']['type'],'image/') !== 0 ){
  produceError('Le fichier reçu n\'est pas une image');
  return;
}
function createImageFromFile($fileName){
    return imagecreatefromstring(file_get_contents($fileName));
}

$image = createImageFromFile($_FILES['image']['tmp_name']);  // création de l'image source
$largeur = imagesx($image);
$hauteur = imagesy($image);
$c = min($largeur,$hauteur);                         // dimension du plus grand carré
$image48 = imagecreatetruecolor(48,48);              // création de l'image 48x48 (vide)
$image256 = imagecreatetruecolor(256,256);           // création de l'image 256x256 (vide)
imagecopyresampled($image48, $image, 0, 0, ($largeur-$c)/2, ($hauteur-$c)/2, 48, 48, $c, $c);    // génération de l'image après découpage et redimensionnement
imagecopyresampled($image256, $image, 0, 0, ($largeur-$c)/2, ($hauteur-$c)/2, 256, 256, $c, $c); // génération de l'image après découpage et redimensionnement

$fluxTmp256 = fopen("php://temp", "r+");            // création d'un flux de stockage temporaire
$fluxTmp48 = fopen("php://temp", "r+");             // création d'un flux de stockage temporaire
imagepng($image48, $fluxTmp48);                     // écriture de l'image en PNG dans le flux
imagepng($image256, $fluxTmp256);                   // écriture de l'image en PNG dans le flux
rewind($fluxTmp48);                                 // repositionnement en début de flux
rewind($fluxTmp256);                                // repositionnement en début de flux


if (! $args->isValid()){
  produceError('argument(s) invalide(s) --> '.implode(', ',$args->getErrorMessages()));
  return;
}

try{
    $data = new DataLayer();
    $log=$_SESSION['ident']->login;
    $img = $data->uploadAvatar($log,['mimetype'=>'image/png','data_small'=>$fluxTmp48,'data_large'=>$fluxTmp256]);
    produceResult($img);

} catch (PDOException $e){
    produceError($e->getMessage());
}

?>
