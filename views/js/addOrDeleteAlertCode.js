let counte = 0;

function addButtonAlert() {
    counte = counte + 1;
    $.ajax({
        url: '/wp-content/plugins/plugin-ecran-connecte/views/js/utils/allCodes.php',
    }).done(function (data) {
        let div = $('<div >', {
            class: 'row'
        }).appendTo('#creationAlert');
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

function deleteRowAlert(id) {
    let dele = document.getElementById(id);
    dele.remove();
    let dele2 = document.getElementById(id);
    dele2.remove();
}