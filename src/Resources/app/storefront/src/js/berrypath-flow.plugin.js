const DEFAULT_SCRIPT_SRC = 'https://www.berrypath.eu/embed/berrypath.js';

let berryPathScriptPromise = null;

export default class BerryPathFlowPlugin extends window.PluginBaseClass {
    init() {
        this.scriptSrc = this.el.dataset.berrypathScriptUrl || DEFAULT_SCRIPT_SRC;
        this.errorElement = this.el.querySelector('[data-berrypath-flow-error]');
        this._hideError();

        this._loadBerryPathScript()
            .then(() => {
                window.BerryPath?.scan?.(this.el);
                this._sendConversion();
                this._setupListingObserver();
            })
            .catch(() => this._showError());
    }

    _setupListingObserver() {
        if (this.listingObserverRegistered) {
            return;
        }

        const listingElement = document.querySelector('.cms-element-product-listing-wrapper');
        if (!listingElement || !window.PluginManager) {
            return;
        }

        const listingPlugin = window.PluginManager.getPluginInstanceFromElement(listingElement, 'Listing');
        if (!listingPlugin?.$emitter) {
            return;
        }

        listingPlugin.$emitter.subscribe('Listing/afterRenderResponse', () => {
            window.BerryPath?.scan?.(document);
        });
        this.listingObserverRegistered = true;
    }

    _sendConversion() {
        if (!this.el.dataset.berrypathFlowConversion || this.el.dataset.berrypathConversionSent === 'true') {
            return;
        }

        let conversion;

        try {
            conversion = JSON.parse(this.el.dataset.berrypathFlowConversion);
        } catch {
            this._showError();
            return;
        }

        if (typeof window.BerryPath?.conversion !== 'function') {
            this._showError();
            return;
        }

        window.BerryPath.conversion(conversion);
        this.el.dataset.berrypathConversionSent = 'true';
    }

    _loadBerryPathScript() {
        if (window.BerryPath?.__ready) {
            return Promise.resolve();
        }

        if (berryPathScriptPromise) {
            return berryPathScriptPromise;
        }

        const existingScript = document.querySelector(`script[src="${this.scriptSrc}"]`);

        berryPathScriptPromise = new Promise((resolve, reject) => {
            const script = existingScript || document.createElement('script');
            const handleLoad = () => resolve();
            const handleError = () => {
                berryPathScriptPromise = null;
                reject(new Error('BerryPath script could not be loaded.'));
            };

            script.addEventListener('load', handleLoad, { once: true });
            script.addEventListener('error', handleError, { once: true });

            if (!existingScript) {
                script.src = this.scriptSrc;
                script.type = 'text/javascript';
                script.async = true;
                script.dataset.berrypathFlowScript = 'true';
                (document.head || document.body).appendChild(script);
            }
        });

        return berryPathScriptPromise;
    }

    _showError() {
        if (this.errorElement) {
            this.errorElement.hidden = false;
        }
    }

    _hideError() {
        if (this.errorElement) {
            this.errorElement.hidden = true;
        }
    }
}
