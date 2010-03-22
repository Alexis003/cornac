<?php

if(!empty($_SESSION['init_infos']->conn_agt_uniq)) 
    $acces_ip = " AND acces_ip=\"".$_SERVER['REMOTE_ADDR']."\"";
else 
    $acces_ip = "";

?>