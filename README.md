# LegalWeb.GdprTools

## Requirements

```"neos/neos": "~4.3.0"```

## Installation

The package is currently not registered at packagist so you have to install it manually:

Configure composer to use github repository:

```json
    "repositories": [
        {
            "type": "github",
            "url": "https://github.com/legalwebio/legalweb-neos"
        }
    ]
```

Then require the package:

```bash
composer require "legalwebio/legalweb-neos:@dev"
```

And run the migrations:

```bash
./flow doctrine:migrate
```

Open the legalweb.io backend module and click "Daten jetzt aktualisieren".
This will download the current dataset from the legalweb.io API.

### Configuration

```yaml
LegalWeb:
  GdprTools:
    # Set `apiUrl` to the endpoint provided by legal web in their API documentation.
    # For example: https://legalweb.io/api
    apiUrl: ''
    # Set `apiKey` to the GUID provided by legal web.
    # For example: d2ed9078-1ddd-426f-88d4-46a7cded8c88 
    apiKey: ''
    # Set `callbackUrl` to the URL that legal web should call to trigger a dataset update.
    # Use `{token}` as a placeholder for the configured `callbackToken`.
    # For example: https://www.example.com/legalweb-gdprtools-update?token={token}
    callbackUrl: ''
    # Set `callbackToken` to a randomly generated URL-safe string.
    # For example: 50.cp5q8nxZW_0YFGkTt1QfU3R~USkyBqwKasCjZrB-wbENoxbeFuirCJTRGuoC
    callbackToken: ''
    # Set `services` to an array of strings, each item corresponding to a key that is expected in the
    # `services` section of the API response.
    # For example: ['imprint', 'contractterms', 'dppopup', 'dppopupconfig', 'dppopupcss', 'dppopupjs']
    services: []
    # The language to use for imprint, data protection popup etc. if no language is passed to the eel helper
    # or if the language passed to the eel helper does not exist in the dataset.
    # For example: de
    fallbackLanguage: ''
```

Set up a cronjob that executes `./flow legalweb:update`.

Ensure that the `LegalWeb.GdprTools:Component.DataProtectionPopup` fusion component is included in every page before the closing `body` tag.

Replace the content of your imprint, contract terms and data protection pages with the nodes `LegalWeb.GdprTools:Imprint`, `LegalWeb.GdprTools:ContractTerms` and `LegalWeb.GdprTools:DataProtectionStatement` respectively.

![Screenshot of node creation dialog](https://user-images.githubusercontent.com/4510166/90875089-e9806600-e3a0-11ea-8873-5ba934cf72bc.png)

## Features

* Provides a public endpoint that can be called by legalweb.io to trigger an update.
    * The URL for the endpoint is `legalweb-gdprtools-update`. A GET parameter `token` is required and must be set to the configured `callbackToken`.
* Provides a backend module to allow users to see when the last update occurred and to manually trigger an update.
    * Messages sent by legalweb.io are also shown in the backend module.
* Provides a CLI command to trigger an update if the last update happened more than one week ago.
    * The CLI command is `./flow legalweb:update`.
      This will only fetch data from the API if the latest retrieved dataset is older than one week.
      To fetch a new dataset no matter how old the latest local dataset is, use `./flow legalweb:update --force`

## API Documentation

https://github.com/legalwebio/legalwebapi

## Coding Style and Tests

Use [PSR-12](https://www.php-fig.org/psr/psr-12/) and check your changes with `composer check` before committing. This will run [phpcs](https://github.com/squizlabs/PHP_CodeSniffer) and [phpstan](https://github.com/phpstan/phpstan).

To execute the tests, add this package to a neos installation and run `composer test` in the `Packages/Plugins/LegalWeb.GdprTools` directory.

