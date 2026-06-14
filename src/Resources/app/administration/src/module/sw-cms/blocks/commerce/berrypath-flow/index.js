import './component';
import './preview';

Shopware.Service('cmsService').registerCmsBlock({
    name: 'berrypath-flow',
    label: 'sw-cms.blocks.commerce.berrypath-flow.label',
    category: 'commerce',
    component: 'sw-cms-block-berrypath-flow',
    previewComponent: 'sw-cms-preview-berrypath-flow',
    defaultConfig: {
        marginBottom: '20px',
        marginTop: '20px',
        marginLeft: '20px',
        marginRight: '20px',
        sizingMode: 'boxed'
    },
    slots: {
        content: 'berrypath-flow'
    }
});
