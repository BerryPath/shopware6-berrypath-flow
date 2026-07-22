import template from './berrypath-flow-onboarding.html.twig';
import './berrypath-flow-onboarding.scss';

Shopware.Component.register('berrypath-flow-onboarding', {
    template,

    computed: {
        userName() {
            const user = Shopware.Store.get('session').currentUser;

            return user?.firstName || user?.username || '';
        },
    },
});
