# BerryPath Flow for Shopware 6

Shopware 6 plugin for the [BerryPath](https://www.berrypath.eu) Flow widget.

BerryPath helps ecommerce teams add guided selling, product finder flows and guided product advice to their webshop. This plugin adds a BerryPath Flow CMS block for Shopping Experiences and can send assisted conversion data from the checkout finish page.

## Installation

```bash
composer require berrypath/shopware6-berrypath-flow
bin/console plugin:refresh
bin/console plugin:install --activate BerryPathFlow
bin/console cache:clear
```

For local `custom/plugins` development, place it at:

```text
custom/plugins/BerryPathFlow
```

## Configuration

Global settings are available in:

```text
Extensions > My extensions > BerryPath Flow > Configure
```

Use this for global enable/disable, default market code, product ID source and the success pixel. The plugin always loads the live BerryPath script from `https://www.berrypath.eu/embed/berrypath.js`.

For product detail pages, enter a Flow UUID in the product custom fields under `BerryPath Flow`. The widget is rendered in the buy box below the product number.

Add the widget through Shopping Experiences:

- Block category: `Commerce`
- Block: `BerryPath Flow`
- Fields: Flow UUID, display type, title, description, button text and optional market override

If no Flow UUID is entered, nothing is rendered.

The success pixel is enabled by default. On the Shopware checkout finish page it loads the main `berrypath.js` file and passes the order total plus product identifiers through `window.BerryPath.conversion(...)` for assisted conversion tracking.

## Screenshots

![Homepage CMS widget](docs/screenshots/homepage-widget.png)

![Category page widget](docs/screenshots/category-widget.png)

![Product page widget](docs/screenshots/product-widget.png)
