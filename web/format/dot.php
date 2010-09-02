<?php
    $dot =  "digraph G {
size=\"8,6\"; ratio=fill; node[fontsize=24];
";
        $clusters = array();
        foreach($lignes as $ligne) {
            $dot .= "\"{$ligne['a']}\" -> \"{$ligne['b']}\";\n";
            if ($ligne['cluster']) {
                $clusters[$ligne['cluster']][] = $ligne['a'];
            }
        }
        
        if (count($clusters) > 0) {
          foreach($clusters as $nom => $liens) {
            $dot .= "subgraph \"cluster_$nom\" {label=\"$nom\"; \"".join('"; "', $liens)."\"; }\n";
          }
        }
        
        $dot .= '}';
?>