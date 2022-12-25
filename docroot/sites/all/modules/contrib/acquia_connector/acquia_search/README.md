Acquia Search for Drupal 7

Module Architecture:

* Environment classes
  * These class define overrides and store data for the current environment in search.
* Service Classes (Search API only)
  * Search API has a service class which is used instead of the array that apachesolr uses.
* Connection Classes
  * There are V2 and V3 connection classes which are extended for the version of search you use.
* Acquia's Search API Classes
  * These classes connect to Acquia's search system or API. Whenever you see Acquia Search Solr API, this indicates the Acquia api, NOT Search API.
