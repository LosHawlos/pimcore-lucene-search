# Pimcore Lucene Search
![lucenesearch crawler](https://cloud.githubusercontent.com/assets/700119/25579028/7da66f40-2e74-11e7-8da5-988d61feb2e2.jpg)

## Note
This is a fork of the original dachcom-digital/pimcore-lucene-search repository.
Dachcom did not update this bundle for Pimcore X in favor of the [Dynamic Search Bundle](https://github.com/dachcom-digital/pimcore-dynamic-search)

This includes changes by [yosoDev](https://github.com/yosoDev) and [beheist](https://github.com/beheist), thanks a lot!

### Requirements
- Pimcore >= 11

#### Pimcore 10
Use the version 3.X for Pimcore 10

#### Pimcore 5/6
Get the Pimcore 5/6 Version [here](https://github.com/dachcom-digital/pimcore-lucene-search).


#### Pimcore 4 
Get the Pimcore4 Version [here](https://github.com/dachcom-digital/pimcore-lucene-search/tree/pimcore4).

### Installation  
1. Add code below to your `composer.json`    
2. Activate & install it through the ExtensionManager

```json
"require" : {
    "pringuin/pimcore-lucene-search" : "~4.0.0"
}
```

### Configuration
To enable LuceneSearch, add those lines to your `AppBundle/Resources/config/pimcore/config.yml`:
    
```yaml
lucene_search:
    enabled: true
```

A complete setup could look like this:

```yaml
lucene_search:
    enabled: true
    fuzzy_search_results: false
    search_suggestion: true
    seeds:
        - 'http://your-domain.dev'
    filter:
        valid_links:
            - '@^http://your-domain.dev.*@i'
    view:
        max_per_page: 10
    crawler:
        content_max_size: 4
        content_start_indicator: '<!-- main-content -->'
        content_end_indicator: '<!-- /main-content -->'
```

You need to add the config parameter to your config.yml to override the default values. 
Execute this command to get some information about all the config elements of LuceneSearch:

```bash
# configuration about all config parameters
$ bin/console config:dump-reference LuceneSearchBundle

# configuration info about the "fuzzy_search_results" parameter
$ bin/console config:dump-reference LuceneSearchBundle fuzzy_search_results
```

We also added a [detailed documentation](docs/00_Configuration_Values.md) about all possible config values.

### Features
* Maintenance driven indexing
* Auto Complete
* Restricted Documents & Usergroups ([member](https://github.com/dachcom-digital/pimcore-members) plugin recommended but not required)

### Usage

**Default**  
The crawler Engine will start automatically every night by default. Please check that the pimcore default maintenance script is properly installed.

**Command Line Command**  
If you want to start the crawler manually, use this command:

```
$ php bin/console lucenesearch:crawl -f -v
```

| command | short command | type | description |
|:---|:---|:---|:---|
| ```force``` | `-f` | force crawler start | sometimes the crawler stuck because of a critical error mostly triggered because of wrong configuration. use this command to force a restart |
| ```verbose``` | `-v` | show some logs | good for debugging. you'll get some additional information about filtered and forbidden links while crawling. |

## Logs
You'll find some logs from the last crawl in your backend (at the bottom on the LuceneSearch settings page). Of course you'll also find some logs in your `var/logs` folder.
**Note:** please enable the debug mode in pimcore settings to get all types of logs.

## Further Information

- [Categories](docs/20_Categories.md): Learn more about category based crawling / searching.
- [Custom Header](docs/29_Custom_Request_Header.md): Learn how to add custom headers to the crawler request (like a auth token).
- [Restrictions](docs/30_Restrictions.md): Learn more about restricted crawling / indexing.
- [Custom Meta Content](docs/40_Meta.md): Learn more about crawling / searching custom meta.
- [Crawler Events](docs/50_Crawler_Events.md): Hook into crawler process to add custom fields to index.
- [Lucene Document Modification](docs/60_Document_Modification.md): Remove or change availability of lucene documents within a pimcore update/deletion event.
- [Frontend Implementation](docs/90_Frontend_Implementation.md): Get a step by step walkthrough to implement lucene search into your website.

## Copyright and license
Copyright: [DACHCOM.DIGITAL](http://dachcom-digital.ch)  
For licensing details please visit [LICENSE.md](LICENSE.md)  

## Upgrade Info
Before updating, please [check our upgrade notes!](UPGRADE.md)