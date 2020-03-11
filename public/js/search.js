/**
 * Search an element in the table
 *
 * @param idTable
 */
function search(idTable) {
    // Declare variables
    let input, filter, table, tr, td, allTd, j, i, txtValue, hide;
    input = document.getElementById("key"+idTable);
    filter = input.value.toUpperCase();
    table = document.getElementById("table"+idTable);
    tr = table.getElementsByTagName("tr");

    // Loop through all table rows, and hide those who don't match the search query
    for (i = 0; i < tr.length; ++i) {
        allTd = tr[i].getElementsByTagName("td");
        for(j = 0; j < allTd.length; ++j) {
            td = allTd[j];
            if (td) {
                txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    ++hide;
                }
            }
        }
        if(hide > 0) {
            tr[i].style.display = "";
        } else {
            tr[i].style.display = "none";
        }
        hide = 0;
    }
}