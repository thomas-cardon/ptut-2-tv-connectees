let counte = 0;

/**
 * Create a new select to add a new group for the alert
 */
function addButtonAlert() {
    console.log(counte);
    counte = counte + 1;
    $.ajax({
        url: '/wp-content/plugins/plugin-ecran-connecte/public/js/utils/allCodes.php',
    }).done(function (data) {
        let div = $('<div >', {
            class: 'row'
        }).appendTo('#alert');
        let select = $('<select >', {
            id: counte,
            name: 'selectAlert[]',
            class: 'form-control select'
        }).append(data).appendTo(div);
        let button = $('<input >', {
            id: counte,
            class: 'selectbtn',
            type: 'button',
            onclick: 'deleteRowAlert(this.id)',
            value: 'Supprimer'
        }).appendTo(div)
    });
}

/**
 * Delete the select
 *
 * @param id
 */
function deleteRowAlert(id) {
    let dele = document.getElementById(id);
    dele.remove();
    let dele2 = document.getElementById(id);
    dele2.remove();
}