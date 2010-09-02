<?php
    $nodes = array();
    $edges = array();
    foreach($lignes as $ligne) {
        if (($ida = in_array($ligne['a'], $nodes)) === false) {
            $nodes[] = $ligne['a'];
            $ida = count($nodes);
        }
        if (($idb = in_array($ligne['b'], $nodes)) === false) {
            $nodes[] = $ligne['b'];
            $idb = count($nodes);
        }
        
        $edges[] = "source=\"$ida\" target=\"$idb\"";
    }
        
    $liste_nodes = '';
    foreach($nodes as $id => $node) {
        $liste_nodes .= <<<XML
        <node id="$id" label="$node">
            <attvalues>
            </attvalues>
        </node>

XML;
    }
        
    $liste_edges = '';
    foreach($edges as $id => $node) {
        $liste_edges .= <<<XML
        <edge id="$id" $node />

XML;
     }

     $gexf = '<?xml version="1.0" encoding="UTF-8"?>';
     $gexf .= <<<XML

<gexf xmlns="http://www.gexf.net/1.1draft" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.gexf.net/1.1draft http://gexf.net/1.1draft.xsd" version="1.1">
    <meta lastmodifieddate="2009-03-20">
        <creator>Auditeur</creator>
        <description>{$_GET['module']}</description>
    </meta>
    <graph defaultedgetype="directed">
        <attributes class="node">
        <!--
            <attribute id="0" title="url" type="string"/>
            <attribute id="1" title="indegree" type="float"/>
            <attribute id="2" title="frog" type="boolean">
                <default>true</default>
            </attribute>
            -->
        </attributes>
        <nodes>
            $liste_nodes
        </nodes>
        <edges>
            $liste_edges
        </edges>
    </graph>
</gexf>    
XML;

?>