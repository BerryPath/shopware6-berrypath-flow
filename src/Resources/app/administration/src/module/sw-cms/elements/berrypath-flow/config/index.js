import template from './sw-cms-el-config-berrypath-flow.html.twig';
import './sw-cms-el-config-berrypath-flow.scss';

const {Component, Mixin} = Shopware;

Component.register('sw-cms-el-config-berrypath-flow', {
    template,

    mixins: [
        Mixin.getByName('cms-element')
    ],

    computed: {
        viewTypeOptions() {
            return [
                {value: 'popup', label: 'Popup'},
                {value: 'inline', label: 'Inline'},
                {value: 'sidebar', label: 'Sidebar'}
            ];
        }
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('berrypath-flow');
        }
    },
});
