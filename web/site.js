function toggle_row(id, nb) {
    for (i = id; i < id + nb; i = i + 1)
    {
        var row = document.getElementById('t_' + i);
        if (row.style.display == 'none') {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }
}

function set_checked_daily(id, style) {
    var row = document.getElementById(id);
    row.className = style;
}

function toggle_checked(id) {
    var row = document.getElementById(id + '_0');
    if (row.className == 'checked') {
        row.className = 'e';
    } else {
        row.className = 'checked';
    }

    var row = document.getElementById(id + '_1');
    if (row.className == 'checked') {
        row.className = 'v';
    } else {
        row.className = 'checked';
    }

    var row = document.getElementById(id + '_2');
    if (row.className == 'checked') {
        row.className = 'v';
    } else {
        row.className = 'checked';
    }
}

function checkElementId(tr_id, id, module, reason) {
    xmlhttp=new XMLHttpRequest();
    xmlhttp.open("GET","ajax.php?elementid=" + id + "&module=" + module  + "&reason=" + reason,true );
    xmlhttp.send();
    
    if (reason == 0) {
        set_checked_daily(tr_id, 'e');
    } else if (reason == 1) {
        x = uki({
            view: "TextField", value: "Hello world!", name: "reason_text",  rect: "0 0 180 24"
         });
        x.attachTo( document.getElementById(tr_id+'_0'), '100 30' );
         
        uki("Button[text^=Hello]").click(
            function() { alert(this.text()); }
        );
        uki("TextField").blur(
            function() { 
            checkElementId('tr_154870', 1, 'dieexit',  this.value()); 
            }
        );

        set_checked_daily(tr_id, 'checked');
    } else {
        set_checked_daily(tr_id, 'checked');
    }
}

function checkElement(tr_id, id, module) {
    xmlhttp=new XMLHttpRequest();
    xmlhttp.open("GET","ajax.php?element=" + id + "&module=" + module,true );
    xmlhttp.send();
    
    toggle_checked(tr_id, id);
}

function checkElementFile(tr_id, id, module) {
    xmlhttp=new XMLHttpRequest();
    xmlhttp.open("GET","ajax.php?elementfile=" + id + "&module=" + module,true);
    xmlhttp.send();
    
    toggle_checked(tr_id, id);
}

function checkFile(tr_id, id, module) {
    xmlhttp=new XMLHttpRequest();
    xmlhttp.open("GET","ajax.php?file=" + id + "&module=" + module,true);
    xmlhttp.send();
    
    toggle_checked(tr_id, id);
}