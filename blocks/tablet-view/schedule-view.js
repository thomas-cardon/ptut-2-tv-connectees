/**
 * Build the block
 */
(function( blocks, element, data)
{
    var el = element.createElement;

    blocks.registerBlockType('tvconnecteeamu/schedule-view', {
        title: 'Voir un emploi du temps (mode tablette)',
        icon: 'smiley',
        category: 'common',
        attributes: {
          content: {
            type: 'string',
            default: '8379'
          },
        },
        edit: function(props) {
          return null;
        },
        save: function() {
            return null;
        },
    });
} )( window.wp.blocks, window.wp.editor, window.wp.element );
