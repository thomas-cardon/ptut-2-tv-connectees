let count = 0;

/**
 * Ajoute un select pour ajouter un emploi du temps à une télévision
 */
function addButtonTv() {
    count = count + 1;
    $.ajax({
        url: '/wp-content/plugins/plugin-ecran-connecte/views/js/utils/allCodes.php',
    }).done(function(data) {
        let div = $('<div >', {
            class:'row'
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
 *  Supprime la ligne sélectionnée
 * @param id    ID de la ligne
 */
function deleteRow(id) {
    let dele = document.getElementById(id);
    dele.remove();
    let dele2 = document.getElementById(id);
    dele2.remove();
}