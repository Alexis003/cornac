<?php

function get_html_check($lines) {
        $xml = '<xml>
';
        foreach($lines as $line) {
            $line['element'] = htmlentities($line['element']);
            $xml .= <<<XML
    <row>
        <element>{$line['element']}</element>
        <count>{$line['nb']}</count>
    </row>

XML;
        }
        $xml .= "</xml>";

        global $prefixe;
        $xml = print_entete($prefixe).$xml.print_pieddepage($prefixe);
        
        return $xml;
}

function get_html_level2($lines) {
        $xml = '<xml>
';
        
        foreach($lines as $file => $rows) {
            $file = htmlentities($file, ENT_COMPAT, 'UTF-8');
            $nb = count($rows);
            $xml .= <<<XML
    <row>
        <file>$file</file>
        <elements count="$nb">

XML;
            foreach($rows as $row) {
                $row['nb'] = htmlentities($row['nb'], ENT_COMPAT, 'UTF-8');
                $row['element'] = htmlentities($row['element'], ENT_COMPAT, 'UTF-8');
                $xml .= <<<XML
            <occurrence>
                <element>{$row['element']}</element>
                <count>{$row['nb']}</count>
            </occurrence>

XML;
            }
            $xml .= <<<XML
        </elements>
    </row>

XML;
        }
        $xml .= "</xml>";

        global $prefixe;
        $xml = print_entete($prefixe).$xml.print_pieddepage($prefixe);
        
        return $xml;
}        

    
function print_entete($prefixe='Sans Nom') {
    global $entete;
    
    return <<<XML
<?xml version="1.0" encoding="UTF-8"?>

XML;

}

function print_pieddepage($prefixe='Sans Nom') {
    return <<<XML
XML;
}
?>