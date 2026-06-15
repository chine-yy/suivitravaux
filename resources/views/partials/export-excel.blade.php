<script>
function exportToExcel(tableId, filename) {
    var table = document.getElementById(tableId);
    if (!table) return false;
    var wb = XLSX.utils.table_to_book(table, {sheet: "Sheet1"});
    XLSX.writeFile(wb, filename + '.xlsx');
    return true;
}
</script>