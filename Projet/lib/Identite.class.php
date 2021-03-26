<?php
class Identite {
  public $login;
  public $pseudo;

  public function __construct($login,$pseudo)
  {
    $this->login = $login;
    $this->pseudo = $pseudo;

  }
}
?>
