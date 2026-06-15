<script>
(function(){
  const csrfMeta = document.querySelector('meta[name="csrf-token"]');
  const csrf = csrfMeta ? csrfMeta.getAttribute('content') : '';
  let exportUrl = '/export/pdf';
  const currentRoute = "<?php echo e(request()->route() ? request()->route()->getName() : ''); ?>";
  if (currentRoute.startsWith('super-admin.')) {
    exportUrl = "<?php echo e(route('super-admin.export.pdf')); ?>";
  } else if (currentRoute.startsWith('role-dynamique.')) {
    exportUrl = "<?php echo e(route('role-dynamique.export.pdf')); ?>";
  } else if (currentRoute.startsWith('partenaire.')) {
    try { exportUrl = "<?php echo e(route('partenaire.export.pdf')); ?>"; } catch(e) {}
  }

  window.exportToPdf = function(tableId, title, filename) {
    var id = (typeof tableId === 'string') ? tableId.replace(/^id=\"?/, '').replace(/\"?$/, '') : tableId;
    var table = (typeof id === 'string') ? document.getElementById(id) : id;
    if (!table) { console.warn('Table introuvable:', tableId); return; }
    const headers = Array.from(table.querySelectorAll('thead th')).map(h => h.innerText.trim());
    const actionIndexes = headers.map((h, i) => /action/i.test(h) ? i : -1).filter(i => i >= 0);
    const filteredHeaders = headers.filter((_, i) => !actionIndexes.includes(i));
    const rows = Array.from(table.querySelectorAll('tbody tr')).map(tr => {
      const cells = Array.from(tr.querySelectorAll('td')).map(td => {
        let clone = td.cloneNode(true);
        // Remove initials/avatars and elements marked as non-printable
        clone.querySelectorAll('.rounded-circle, .d-print-none').forEach(el => el.remove());
        return clone.innerHTML.trim();
      });
      return cells.filter((_, i) => !actionIndexes.includes(i));
    });

    // include roles only for explicit 'roles' or 'historique' exports — not for projects
    const filenameStr = (filename || '').toString().toLowerCase();
    const titleStr = (title || '').toString().toLowerCase();
    const includeRoles = /roles/.test(filenameStr) || /roles/.test(titleStr) || (currentRoute || '').includes('historique') || (currentRoute || '').includes('roles');
    // include project info when exporting a single project row (filename like 'projet_123')
    const includeProjectInfo = /^projet_\d+/.test(filenameStr) || titleStr.includes('projet');

    fetch(exportUrl, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
      body: JSON.stringify({ title: title || document.title, headers: filteredHeaders, rows: rows, filename: filename || 'export', include_roles: includeRoles, include_project_info: includeProjectInfo })
    }).then(r => r.blob()).then(blob => {
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = (filename || 'export') + '.pdf';
      document.body.appendChild(a);
      a.click();
      a.remove();
      URL.revokeObjectURL(url);
    }).catch(e=>console.error(e));
  };

  window.exportRowToPdf = function(element, title, filename, projectNameOnly) {
    const tr = element.closest('tr'); if (!tr) return;
    const table = tr.closest('table'); if (!table) return;

    let headers = Array.from(table.querySelectorAll('thead th')).map(h => h.innerText.trim());
    let row = Array.from(tr.querySelectorAll('td')).map(td => {
      let clone = td.cloneNode(true);
      clone.querySelectorAll('.rounded-circle, .d-print-none').forEach(el => el.remove());
      return clone.innerHTML.trim();
    });

    if (projectNameOnly === true || projectNameOnly === 'true') {
      const projectIndex = headers.findIndex(h => (h || '').toLowerCase().includes('nom du projet') || (h || '').toLowerCase().includes('projet'));
      if (projectIndex >= 0) {
        headers = [headers[projectIndex]];
        row = [row[projectIndex] ?? ''];
      } else if (headers.length > 0 && row.length > 0) {
        headers = [headers[0]];
        row = [row[0]];
      }
    }

    const filenameStr = (filename || '').toString().toLowerCase();
    const titleStr = (title || '').toString().toLowerCase();
    const includeRoles = /roles/.test(filenameStr) || /roles/.test(titleStr) || (currentRoute || '').includes('historique') || (currentRoute || '').includes('roles');
    const includeProjectInfo = /^projet_\d+/.test(filenameStr) || titleStr.includes('projet');

    fetch(exportUrl, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
      body: JSON.stringify({ title: title || document.title, headers: headers, rows: [row], filename: filename || 'export', include_roles: includeRoles, include_project_info: includeProjectInfo })
    }).then(r => r.blob()).then(blob => {
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = (filename || 'export') + '.pdf';
      document.body.appendChild(a);
      a.click();
      a.remove();
      URL.revokeObjectURL(url);
    }).catch(e=>console.error(e));
  };
})();
</script>
<?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/partials/export-pdf.blade.php ENDPATH**/ ?>