/**
 * Inverse the table (TODO a real sort)
 *
 * @param n
 * @param idTable
 */

let reverse = false;
let oldN;
function sortTable(n, idTable = '')
{
    var table;
    if(oldN === n) {
        reverse = !reverse;
    } else {
        oldN = n;
    }
    table = document.getElementById("table"+idTable);
    quickSort(table.rows, 1, table.rows.length - 1, n, table);

    for (var i = 1; i < table.rows.length - 1; ++i) {
        if(reverse) {
            if(table.rows[i].getElementsByTagName("TD")[n].innerHTML.toLowerCase() < table.rows[i + 1].getElementsByTagName("TD")[n].innerHTML.toLowerCase()) {
                quickSort(table.rows, 1, table.rows.length - 1, n, table);
            }
        } else {
            if(table.rows[i].getElementsByTagName("TD")[n].innerHTML.toLowerCase() > table.rows[i + 1].getElementsByTagName("TD")[n].innerHTML.toLowerCase()) {
                quickSort(table.rows, 1, table.rows.length - 1, n, table);
            }
        }
    }
}

function swap(items, leftIndex, rightIndex)
{
    items[leftIndex].parentNode.insertBefore(items[rightIndex], items[leftIndex]);
    items[rightIndex].parentNode.insertBefore(items[leftIndex], items[rightIndex]);
}


function partition(items, left, right, n)
{
    var pivot   = items[Math.floor((right + left) / 2)],
        i       = left, //left pointer
        j       = right; //right pointer
    while (i <= j) {
        if(reverse) {
            while (items[i].getElementsByTagName("TD")[n].innerHTML.toLowerCase() > pivot.getElementsByTagName("TD")[n].innerHTML.toLowerCase()) {
                i++;
            }
            while (items[j].getElementsByTagName("TD")[n].innerHTML.toLowerCase() < pivot.getElementsByTagName("TD")[n].innerHTML.toLowerCase()) {
                j--;
            }
        } else {
            while (items[i].getElementsByTagName("TD")[n].innerHTML.toLowerCase() < pivot.getElementsByTagName("TD")[n].innerHTML.toLowerCase()) {
                i++;
            }
            while (items[j].getElementsByTagName("TD")[n].innerHTML.toLowerCase() > pivot.getElementsByTagName("TD")[n].innerHTML.toLowerCase()) {
                j--;
            }
        }
        if (i <= j) {
            swap(items, i, j);
            i++;
            j--;
        }
    }
    return i;
}

function quickSort(items, left, right, n)
{
    var index;
    if (items.length > 1) {
        index = partition(items, left, right, n);
        if (left < index - 1) {
            quickSort(items, left, index - 1, n);
        }
        if (index < right) {
            quickSort(items, index, right, n);
        }
    }
    return items;
}

/*
function sortTable(n, idTable = '')
{
    var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
    table = document.getElementById("table"+idTable);
    switching = true;
    dir = "asc";

    while (switching) {
        switching = false;
        rows = table.rows;

        for (i = 1; i < (rows.length - 1); ++i) {
            shouldSwitch = false;

            x = rows[i].getElementsByTagName("TD")[n];
            y = rows[i + 1].getElementsByTagName("TD")[n];

            console.log(x.innerHTML);
            console.log(y.innerHTML);

            if (dir == "asc") {
                if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                    shouldSwitch = true;
                    break;
                }
            } else if (dir == "desc") {
                if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                    shouldSwitch = true;
                    break;
                }
            }
        }
        if (shouldSwitch) {
            rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
            switching = true;
            switchcount ++;
        } else {
            if (switchcount == 0 && dir == "asc") {
                dir = "desc";
                switching = true;
            }
        }
    }
}
 */