let count = 0;

/**
 * Create a new select to add a new group for the television
 */
function addButtonTv() {
    count = count + 1;
    $.ajax({
        url: '/wp-content/plugins/plugin-ecran-connecte/public/js/utils/allCodes.php',
    }).done(function (data) {
        let div = $('<div >', {
            class: 'row'
        }).appendTo('#registerTvForm');
        let select = $('<select >', {
            id: count,
            name: 'selectTv[]',
            class: 'form-control select'
        }).append(data).appendTo(div);
        let button = $('<input >', {
            id: count,
            class: 'selectbtn',
            type: 'button',
            onclick: 'deleteRow(this.id)',
            value: 'Supprimer'
        }).appendTo(div)
    });
}

/**
 * Delete the select
 *
 * @param id
 */
function deleteRow(id) {
    let dele = document.getElementById(id);
    dele.remove();
    let dele2 = document.getElementById(id);
    dele2.remove();
}