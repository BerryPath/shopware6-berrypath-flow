import template from './sw-cms-el-component-berrypath-flow.html.twig';
import './sw-cms-el-component-berrypath-flow.scss';

const {Component, Mixin} = Shopware;

Component.register('sw-cms-el-component-berrypath-flow', {
    template,

    mixins: [
        Mixin.getByName('cms-element')
    ],

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('berrypath-flow');
            this.initElementData('berrypath-flow');
        },
    },
});
