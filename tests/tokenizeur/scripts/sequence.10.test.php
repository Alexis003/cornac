<?php

  $pagination->enregistrement=$_SESSION['init_infos']->nb_pet_liste;
  $pagination->col_nom="nom_col".$pagination->col_id."_9";
  $pagination->col_sens="DESC";
  $pagination->fct_tri();
  $_SESSION['cligraph']->connect() or die($_SESSION['cligraph']->errormsg);

?>