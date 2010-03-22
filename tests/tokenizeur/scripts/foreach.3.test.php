<?php


if ($_SESSION['cligraph']->nbtuples==0)
{
  $date_mess_stock=array();
}
else
{
  $i=0;
  foreach($result2 as $ligne)
  {
    $date_mess_stock[$i]=$ligne[0];
    echo "<script language='javascript'>calend_valeur_stock[0][".$i."]=\"".$date_mess_stock[$i]."\";</script>";
    $i++;
  }
}

?>