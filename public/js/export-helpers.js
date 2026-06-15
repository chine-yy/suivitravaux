function exportToPdf(tableId, title, filename) {
    var table = document.getElementById(tableId);
    if (!table) { alert("Erreur: Tableau introuvable pour PDF."); return; }

    var headers = [];
    var ths = table.querySelectorAll('thead tr th');
    for (var i = 0; i < ths.length; i++) {
        if (ths[i].textContent.trim().toLowerCase().indexOf('action') !== -1) continue;
        headers.push(ths[i].innerText.trim());
    }

    var rows = [];
    var trs = table.querySelectorAll('tbody tr');
    for (var j = 0; j < trs.length; j++) {
        var cell = trs[j].querySelector('td');
        if (cell && cell.colSpan > 1) continue;

        var row = [];
        var cells = trs[j].querySelectorAll('td');
        for (var k = 0; k < cells.length; k++) {
            if (k === cells.length - 1 && cells[k].innerHTML.indexOf('btn') !== -1) continue;
            var text = cells[k].innerText || cells[k].textContent;
            text = text.replace(/\\s+/g, ' ').trim();
            row.push(text);
        }
        if (row.length > 0) rows.push(row);
    }

    var form = document.createElement('form');
    form.method = 'POST';
    form.action = window.location.pathname.split('/').slice(0, 3).join('/') + '/export/pdf';
    form.style.display = 'none';

    var csrf = document.createElement('input');
    csrf.type = 'hidden'; csrf.name = '_token';
    csrf.value = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';
    form.appendChild(csrf);

    var titleInput = document.createElement('input');
    titleInput.type = 'hidden'; titleInput.name = 'title'; titleInput.value = title;
    form.appendChild(titleInput);

    var headersInput = document.createElement('input');
    headersInput.type = 'hidden'; headersInput.name = 'headers';
    headersInput.value = JSON.stringify(headers);
    form.appendChild(headersInput);

    var rowsInput = document.createElement('input');
    rowsInput.type = 'hidden'; rowsInput.name = 'rows';
    rowsInput.value = JSON.stringify(rows);
    form.appendChild(rowsInput);

    var filenameInput = document.createElement('input');
    filenameInput.type = 'hidden'; filenameInput.name = 'filename'; filenameInput.value = filename;
    form.appendChild(filenameInput);

    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

function voirPdf(tableId, title) {
    var table = document.getElementById(tableId);
    if (!table) { alert("Erreur: Tableau introuvable pour PDF."); return; }

    var headers = [];
    var ths = table.querySelectorAll('thead tr th');
    for (var i = 0; i < ths.length; i++) {
        if (ths[i].textContent.trim().toLowerCase().indexOf('action') !== -1) continue;
        headers.push(ths[i].innerText.trim());
    }

    var rows = [];
    var trs = table.querySelectorAll('tbody tr');
    for (var j = 0; j < trs.length; j++) {
        var cell = trs[j].querySelector('td');
        if (cell && cell.colSpan > 1) continue;

        var row = [];
        var cells = trs[j].querySelectorAll('td');
        for (var k = 0; k < cells.length; k++) {
            if (k === cells.length - 1 && cells[k].innerHTML.indexOf('btn') !== -1) continue;
            var text = cells[k].innerText || cells[k].textContent;
            text = text.replace(/\\s+/g, ' ').trim();
            row.push(text);
        }
        if (row.length > 0) rows.push(row);
    }

    var form = document.createElement('form');
    form.method = 'POST';
    form.action = window.location.pathname.split('/').slice(0, 3).join('/') + '/export/voir-pdf';
    form.target = '_blank';
    form.style.display = 'none';

    var csrf = document.createElement('input');
    csrf.type = 'hidden'; csrf.name = '_token';
    csrf.value = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';
    form.appendChild(csrf);

    var titleInput = document.createElement('input');
    titleInput.type = 'hidden'; titleInput.name = 'title'; titleInput.value = title;
    form.appendChild(titleInput);

    var headersInput = document.createElement('input');
    headersInput.type = 'hidden'; headersInput.name = 'headers';
    headersInput.value = JSON.stringify(headers);
    form.appendChild(headersInput);

    var rowsInput = document.createElement('input');
    rowsInput.type = 'hidden'; rowsInput.name = 'rows';
    rowsInput.value = JSON.stringify(rows);
    form.appendChild(rowsInput);

    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}
