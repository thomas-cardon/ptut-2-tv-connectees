/**
 * Créer le bloc en indiquant son titre, son icone, sa catagorie
 * return de edit permet d'afficher un message lorsqu'on est sur l'éditeur
 */
( function( blocks, element, data  ) {

    var el = element.createElement;

    blocks.registerBlockType( 'tvconnecteeamu/add-alert', {
        title: 'Ajout alerte',
        icon: 'smiley',
        category: 'common',

        edit: function() {
            return "Ajoute une alerte via un formulaire";
        },
        save: function() {
            return "yo";
        },
    } );
}(
    window.wp.blocks,
    window.wp.element,
    window.wp.data,
) );