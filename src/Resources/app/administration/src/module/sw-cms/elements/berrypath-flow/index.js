import './component';
import './config';
import './preview';

Shopware.Service('cmsService').registerCmsElement({
    name: 'berrypath-flow',
    label: 'sw-cms.elements.berrypath-flow.label',
    component: 'sw-cms-el-component-berrypath-flow',
    configComponent: 'sw-cms-el-config-berrypath-flow',
    previewComponent: 'sw-cms-el-preview-berrypath-flow',
    defaultConfig: {
        flowToken: {
            source: 'static',
            value: ''
        },
        viewType: {
            source: 'static',
            value: 'popup'
        },
        title: {
            source: 'static',
            value: 'Need help choosing?'
        },
        description: {
            source: 'static',
            value: 'Answer a few short questions and quickly find the product that fits you.'
        },
        ctaText: {
            source: 'static',
            value: 'Start the choice helper'
        },
        locale: {
            source: 'static',
            value: ''
        },
        market: {
            source: 'static',
            value: ''
        }
    },
    defaultData: {}
});
