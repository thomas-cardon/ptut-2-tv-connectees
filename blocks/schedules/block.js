/**
 * Créer le bloc en indiquant son titre, son icone, sa catagorie
 * return de edit permet d'afficher un message lorsqu'on est sur l'éditeur
 */
( function( blocks, element, data  ) {

    var el = element.createElement;

    blocks.registerBlockType( 'tvconnecteeamu/schedules', {
        title: 'Emploi du temps promo',
        icon: 'smiley',
        category: 'common',

        edit: function() {
            return "Affiche l\'emploi du temps d'une promo";
        },
        save: function() {
            return "test";
        },
    } );
}(
    window.wp.blocks,
    window.wp.element,
    window.wp.data,
) );