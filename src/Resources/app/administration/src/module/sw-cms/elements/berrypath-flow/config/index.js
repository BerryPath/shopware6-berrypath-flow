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
                {value: 'popup', label: this.$tc('sw-cms.elements.berrypath-flow.viewTypes.popup')},
                {value: 'inline', label: this.$tc('sw-cms.elements.berrypath-flow.viewTypes.inline')},
                {value: 'sidebar', label: this.$tc('sw-cms.elements.berrypath-flow.viewTypes.sidebar')}
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
