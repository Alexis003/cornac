<?php
    switch(TYPE_DOC)
    {
      case "facture" :  echo $LANG_ACTION['dateecheance'];  break;
      case "devis" || "commande" || "avoir":  echo $LANG_ACTION['limitevalidite'];  break;
    }
?>
