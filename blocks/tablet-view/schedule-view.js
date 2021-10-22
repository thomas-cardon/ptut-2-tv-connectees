const { registerBlockType } = wp.blocks;
const { serverSideRender: ServerSideRender } = wp;

registerBlockType('test/testblock', {
    title: 'test block',
    edit: () => {
        return (
            <>
                <ServerSideRender
                    block="test/testblock"
                />
            </>
        );
    },
    save() {
        return null; // Nothing to save here..
    }
});
