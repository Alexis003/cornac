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