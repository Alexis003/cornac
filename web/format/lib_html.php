<?php

class html_table {
    protected $rows = array();

    public $border = 0;
    public $cellpadding = 0;
    public $width = 600;
    
    public $id = "t";
    
    function __construct() {}
    
    function asHTML() {
        $html = "";
        
        $html .= build_tag('table', (array) $this);
        foreach($this->rows as $rid => $row) {
            $html .= $row->asHTML();
        }
        
        $html .= "</table>";
        return $html;
    }

    function InsertRow($id, $row) {
        $hrow = new html_row();
        
        foreach($row as $cell) {
            $hrow->addCell($cell);
        }
        array_splice($this->rows, $id, 0, array($hrow));
        $hrow->id = $this->id.'_'.(count($this->rows) - 1);
        
        return $hrow;
    }    
    
    function addRow($row) {
        $hrow = new html_row();
        $this->rows[] = $hrow;
        $hrow->id = $this->id.'_'.(count($this->rows) - 1);
        
        foreach($row as $cell) {
            $hrow->addCell($cell);
        }
        
        return $hrow;
    }    
}

class html_row {
    private $cells = array();
    public $id = null;
    public $style = array();
    private $attributes = array();

    function __construct() { }
    
    function addCell($cell) {
       $hcell = new html_cell($cell);
       $this->cells[] = $hcell;
       $hcell->id = count($this->cells) - 1;
    }
    
    function getId() {
        return $this->id;
    }

    function getCell($id) {
        return $this->cells[$id];
    }
    
    function setAttribute($name, $value) {
        $this->attributes[$name] = $value;
    }

    function setCells($cells) {
        if (is_string($cells)) {
            foreach($this->cells as $cell) {
                die('todo');
                $cell->class = $class;
            }
        } elseif (is_array($cells)) {
            foreach($cells as $id => $acell) {
                $hcell = new html_cell($acell);
                $this->cells[$id] = $hcell;
            }
        }
    }    
    function setCellsClass($class) {
        if (is_string($class)) {
            foreach($this->cells as $cell) {
                $cell->class = $class;
            }
        } elseif (is_array($class)) {
            foreach($class as $id => $aclass) {
                if (isset($this->cells[$id])) {
                    $this->cells[$id]->class = $aclass;
                }
            }
        }
    }

    function setStyle($name, $value) {
        if (is_string($name)) {
            $this->style[$name] = $value;
        } elseif (is_array($name)) {
            $this->style = array_merge($this->style, $name);
        }
    }
    
    function setCellsStyle($name, $value) {
        if (is_string($name)) {
            foreach($this->cells as $cell) {
                $cell->style[$name] = $value;
            }
        } elseif (is_array($name)) {
            foreach($this->cells as $cell) {
                $cell->style = array_merge($cell->style, $name);
            }
        }
    }

    function asHTML() {
        $attributes = array('id' => $this->id);
        $attributes = array_merge($attributes, $this->attributes);
        if ($this->style) {
            $attributes['style'] = build_inline_style($this->style);
        }
        $html = build_tag("tr",  $attributes);
        foreach($this->cells as $cid => $cell) {
            $cell->id = $this->id.'_'.$cid;
            $html .= $cell->asHTML();
        }
        $html .= "</tr>\n";
        return $html;
    }
}

class html_cell {
    private $cell = null;
    public $id = null;
    public $class = null;
    public $style = array();
    public $attributes = array();
    
    function __construct($content) { 
        $this->content = $content; 
    }

    function getId() {
        return $this->id;
    }

    function asHTML() {
        $attributes = array('id' => $this->id, 'class' => $this->class) ;
        if (count($this->style) > 0) {
            $attributes['style'] = build_inline_style($this->style);
        }
        $attributes = array_merge($attributes, $this->attributes);
        $html = build_tag("td", $attributes);
        
        $html .= $this->content;
        
        $html .= "</td>\n";
        return $html;
    }

    function setAttribute($name, $value) {
        $this->attributes[$name] = $value;
    }    
}

function build_inline_style($styles) {
    $r = '';
    foreach($styles as $name => $value) {
        $r .= "$name:$value;";
    }
    
    return substr($r,0, -1);
}

function build_tag($tag, $attributes) {
    $tag = "<$tag";
    
    foreach($attributes as $name => $value) {
        if (is_array($value)) {continue; } // @note protected
        $tag .= " ".htmlentities($name)."=\"".htmlentities($value)."\"";
    }
    $tag .= ">";
    return $tag;
}


?>