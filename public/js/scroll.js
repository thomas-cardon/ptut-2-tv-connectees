/**
 * Scroll all schedule from the bottom to the top
 */
const ticker = Tickit({
    data: ['item 1', 'item 2', 'item 3'],
    selector: '#tickit',
    duration: 10*1000,
    initialPos: -15,
    behavior: 'scroll'
}).init();
