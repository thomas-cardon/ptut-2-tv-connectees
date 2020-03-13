/**
 * Build the block
 */
(function(blocks, element, data)
{
    var el = element.createElement;

    blocks.registerBlockType('tvconnecteeamu/inscr-student', {
        title: 'Inscription étudiant',
        icon: 'smiley',
        category: 'common',

        edit: function() {
            return "Formulaire pour inscrire des étudiants";
        },
        save: function() {
            return "test";
        },
    });
}(
    window.wp.blocks,
    window.wp.element,
    window.wp.data,
));