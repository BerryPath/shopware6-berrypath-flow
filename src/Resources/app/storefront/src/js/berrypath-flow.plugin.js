const DEFAULT_SCRIPT_SRC = 'https://www.berrypath.eu/embed/berrypath.js';

let berryPathScriptPromise = null;
let berryPathScriptLoaded = false;

export default class BerryPathFlowPlugin extends window.PluginBaseClass {
    init() {
        this.scriptSrc = this.el.dataset.berrypathScriptUrl || DEFAULT_SCRIPT_SRC;
        this._loadBerryPathScript();
        this._setupDeferredButtons();
        this._setupListingObserver();
    }

    _setupDeferredButtons() {
        this.el.querySelectorAll('[data-berrypath-type="popup"], [data-berrypath-type="sidebar"]').forEach((button) => {
            if (button.dataset.berrypathDeferredClickBound === 'true') {
                return;
            }

            button.dataset.berrypathDeferredClickBound = 'true';
            button.addEventListener('click', (event) => {
                if (berryPathScriptLoaded) {
                    return;
                }

                event.preventDefault();
                event.stopImmediatePropagation();

                this._loadBerryPathScript()
                    .then(() => {
                        window.setTimeout(() => button.click(), 0);
                    })
                    .catch(() => {
                        console.error('[BerryPath] Could not load berrypath.js.');
                    });
            }, true);
        });
    }

    _setupListingObserver() {
        const listingElement = document.querySelector('.cms-element-product-listing-wrapper');
        if (!listingElement || !window.PluginManager) {
            return;
        }

        const listingPlugin = window.PluginManager.getPluginInstanceFromElement(listingElement, 'Listing');
        if (!listingPlugin || !listingPlugin.$emitter) {
            return;
        }

        listingPlugin.$emitter.subscribe('Listing/afterRenderResponse', () => {
            this._loadBerryPathScript().catch(() => {});
        });
    }

    _loadBerryPathScript() {
        if (berryPathScriptLoaded || window.BerryPath?.__ready) {
            berryPathScriptLoaded = true;
            return Promise.resolve();
        }

        if (berryPathScriptPromise) {
            return berryPathScriptPromise;
        }

        const existingScript = document.querySelector(`script[src="${this.scriptSrc}"]`);
        if (existingScript && existingScript.dataset.berrypathLoaded === 'true') {
            berryPathScriptLoaded = true;
            return Promise.resolve();
        }

        berryPathScriptPromise = new Promise((resolve, reject) => {
            const script = existingScript || document.createElement('script');

            script.addEventListener('load', () => {
                script.dataset.berrypathLoaded = 'true';
                berryPathScriptLoaded = true;
                resolve();
            }, {once: true});

            script.addEventListener('error', reject, {once: true});

            if (!existingScript) {
                script.src = this.scriptSrc;
                script.type = 'text/javascript';
                script.async = true;
                (document.head || document.body).appendChild(script);
            }
        });

        return berryPathScriptPromise;
    }
}
