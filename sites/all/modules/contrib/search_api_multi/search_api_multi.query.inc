<?php

/**
 * Interface representing a search query on multiple Search API indexes on the
 * same server. The server has to support the "search_api_multi" feature.
 *
 * For discerning from which index a certain field should be used (for filtering
 * or specifying the fulltext fields, for instance), all field identifiers have
 * to be prefixed with their index' machine name, seperated by a colon.
 * For example, to filter on the "author:name" field from the index with the
 * machine name "default_node_index", use "default_node_index:author:name" as
 * the identifier.
 *
 * Methods not returning something else will return the object itself, so calls
 * can be chained.
 */
interface SearchApiMultiQueryInterface {

  /**
   * Constructor used when creating SearchApiMultiQueryInterface objects.
   *
   * @param SearchApiServer $server
   *   The server this search will be executed on.
   * @param array $options
   *   Associative array of options configuring this query. Recognized options
   *   are:
   *   - conjunction: The type of conjunction to use for this query - either
   *     'AND' or 'OR'. 'AND' by default. This only influences the search keys,
   *     filters will always use AND by default.
   *   - 'parse mode': The mode with which to parse the $keys variable, if it
   *     is set and not already an array. See SearchApiMultiQuery::parseModes() for
   *     recognized parse modes.
   *   - languages: The languages to search for, as an array of language IDs.
   *     If not specified, all languages will be searched. Language-neutral
   *     content (LANGUAGE_NONE) is always searched.
   *   - offset: The position of the first returned search results relative to
   *     the whole result on the server.
   *   - limit: The maximum number of search results to return. -1 means no
   *     limit.
   *   - 'filter class': Can be used to change the SearchApiQueryFilterInterface
   *     implementation to use.
   *   - 'search id': A string that will be used as the identifier when storing
   *     this search in the static cache.
   *   All options are optional.
   *
   * @throws SearchApiException
   *   If a search with these options won't be possible.
   */
  public function __construct(SearchApiServer $server, array $options = array());

  /**
   * @return array
   *   An associative array of parse modes recognized by objects of this class.
   *   The keys are the parse modes' ids, values are associative arrays
   *   containing the following entries:
   *   - name: The translated name of the parse mode.
   *   - description: (optional) A translated text describing the parse mode.
   */
  public function parseModes();

  /**
   * Method for creating a filter to use with this query object.
   *
   * @param $conjunction
   *   The conjunction to use for the filter - either 'AND' or 'OR'.
   *
   * @return SearchApiQueryFilterInterface
   *   A filter object that is set to use the specified conjunction.
   */
  public function createFilter($conjunction = 'AND');

  /**
   * Sets the keys to search for. If this method is not called on the query
   * before execution, this will be a filter-only query.
   *
   * @param $keys
   *   A string with the unparsed search keys, or NULL to use no search keys.
   *
   * @return SearchApiMultiQueryInterface
   *   The called object.
   */
  public function keys($keys = NULL);

  /**
   * Sets the fields that will be searched for the search keys. If this is not
   * called, all fulltext fields should be searched.
   *
   * @param array $fields
   *   An array containing fulltext fields that should be searched.
   *
   * @throws SearchApiException
   *   If one of the fields isn't of type "text".
   *
   * @return SearchApiMultiQueryInterface
   *   The called object.
   */
  public function fields(array $fields);

  /**
   * Adds a subfilter to this query's filter.
   *
   * @param SearchApiQueryFilterInterface $filter
   *   A SearchApiQueryFilter object that should be added as a subfilter.
   *
   * @return SearchApiMultiQueryInterface
   *   The called object.
   */
  public function filter(SearchApiQueryFilterInterface $filter);

  /**
   * Add a new ($field $operator $value) condition filter.
   *
   * @param $field
   *   The field to filter on. Either a field specification as detailed in the
   *   class comment, or the special field "search_api_multi_index" which means
   *   a filter on the searched indexes.
   * @param $value
   *   The value the field should have (or be related to by the operator).
   * @param $operator
   *   The operator to use for checking the constraint. The following operators
   *   are supported for primitive types: "=", "<>", "<", "<=", ">=", ">". They
   *   have the same semantics as the corresponding SQL operators.
   *   If $field is a fulltext field, $operator can only be "=" or "<>", which
   *   are in this case interpreted as "contains" or "doesn't contain",
   *   respectively.
   *   If $value is NULL, $operator also can only be "=" or "<>", meaning the
   *   field must have no or some value, respectively.
   *
   * @return SearchApiMultiQueryInterface
   *   The called object.
   */
  public function condition($field, $value, $operator = '=');

  /**
   * Add a sort directive to this search query. If no sort is manually set, the
   * results will be sorted descending by relevance.
   *
   * How sorts on index-specific fields are handled may differ between service
   * backends.
   *
   * @param $field
   *   The field to sort by. The special field "search_api_relevance" may be
   *   used to sort by relevance.
   * @param $order
   *   The order to sort items in - either 'ASC' or 'DESC'.
   *
   * @throws SearchApiException
   *   If the field is multi-valued or of a fulltext type.
   *
   * @return SearchApiMultiQueryInterface
   *   The called object.
   */
  public function sort($field, $order = 'ASC');

  /**
   * Adds a range of results to return. This will be saved in the query's
   * options. If called without parameters, this will remove all range
   * restrictions previously set.
   *
   * @param $offset
   *   The zero-based offset of the first result returned.
   * @param $limit
   *   The number of results to return.
   *
   * @return SearchApiMultiQueryInterface
   *   The called object.
   */
  public function range($offset = NULL, $limit = NULL);

  /**
   * Executes this search query.
   *
   * @return array
   *   An associative array containing the search results. The following keys
   *   are standardized:
   *   - 'result count': The overall number of results for this query, without
   *     range restrictions. Might be approximated, for large numbers.
   *   - results: An array of results, ordered as specified. The array keys are
   *     arbitrary unique keys, values are arrays containing the following keys:
   *     - id: The item's ID.
   *     - index_id: The machine name of the index this item was found on.
   *     - score: A float measuring how well the item fits the search.
   *     - entity (optional): If set, the fully loaded result item. This field
   *       should always be used by modules using search results, to avoid
   *       duplicate item loads.
   *     - excerpt (optional): If set, an HTML text containing highlighted
   *       portions of the fulltext that match the query.
   *   - warnings: A numeric array of translated warning messages that may be
   *     displayed to the user.
   *   - ignored: A numeric array of search keys that were ignored for this
   *     search (e.g., because of being too short or stop words).
   *   - performance: An associative array with the time taken (as floats, in
   *     seconds) for specific parts of the search execution:
   *     - complete: The complete runtime of the query.
   *     - hooks: Hook invocations and other client-side preprocessing.
   *     - preprocessing: Preprocessing of the service class.
   *     - execution: The actual query to the search server, in whatever form.
   *     - postprocessing: Preparing the results for returning.
   *   Additional metadata may be returned in other keys. Only 'result count'
   *   and 'result' always have to be set, all other entries are optional.
   */
  public function execute();

  /**
   * @return SearchApiServer
   *   The search server this query should be executed on.
   */
  public function getServer();

  /**
   * @return array
   *   An array of SearchApiIndex objects representing all indexes that will be
   *   searched.
   */
  public function getIndexes();

  /**
   * @return
   *   This object's search keys - either a string or an array specifying a
   *   complex search expression.
   *   An array will contain a '#conjunction' key specifying the conjunction
   *   type, and search strings or nested expression arrays at numeric keys.
   *   Additionally, a '#negation' key might be present, which means – unless it
   *   maps to a FALSE value – that the search keys contained in that array
   *   should be negated, i.e. not be present in returned results.
   */
  public function &getKeys();

  /**
   * @return
   *   The unprocessed search keys, exactly as passed to this object. Has the
   *   same format as getKeys().
   */
  public function getOriginalKeys();

  /**
   * @return array
   *   An array containing the fields that should be searched for the search
   *   keys.
   */
  public function &getFields();

  /**
   * @return SearchApiQueryFilterInterface
   *   This object's associated filter object.
   */
  public function getFilter();

  /**
   * @return array
   *   An array specifying the sort order for this query. Array keys are the
   *   field names in order of importance, the values are the respective order
   *   in which to sort the results according to the field.
   */
  public function &getSort();

  /**
   * @param $name string
   *   The name of an option.
   *
   * @return mixed
   *   The value of the option with the specified name, if set. NULL otherwise.
   */
  public function getOption($name);

  /**
   * @param string $name
   *   The name of an option.
   * @param mixed $value
   *   The new value of the option.
   *
   * @return The option's previous value.
   */
  public function setOption($name, $value);

  /**
   * @return array
   *   An associative array of query options.
   */
  public function &getOptions();

}

/**
 * Standard implementation of SearchApiMultiQueryInterface.
 */
class SearchApiMultiQuery implements SearchApiMultiQueryInterface {

  /**
   * The server.
   *
   * @var SearchApiServer
   */
  protected $server;

  /**
   * The searched indexes.
   *
   * @var array
   */
  protected $indexes;

  /**
   * The search keys. If NULL, this will be a filter-only search.
   *
   * @var mixed
   */
  protected $keys;

  /**
   * The unprocessed search keys, as passed to the keys() method.
   *
   * @var mixed
   */
  protected $orig_keys;

  /**
   * The fields that will be searched for the keys.
   *
   * @var array
   */
  protected $fields;

  /**
   * The search filter associated with this query.
   *
   * @var SearchApiQueryFilterInterface
   */
  protected $filter;

  /**
   * The sort associated with this query.
   *
   * @var array
   */
  protected $sort;

  /**
   * Search options configuring this query.
   *
   * @var array
   */
  protected $options;

  /**
   * Count for providing a unique ID.
   */
  protected static $count = 0;

  /**
   * Constructor for SearchApiMultiQuery objects.
   *
   * @param SearchApiIndex $server
   *   The server the query should be executed on.
   * @param array $options
   *   Associative array of options configuring this query. Recognized options
   *   are:
   *   - conjunction: The type of conjunction to use for this query - either
   *     'AND' or 'OR'. 'AND' by default. This only influences the search keys,
   *     filters will always use AND by default.
   *   - 'parse mode': The mode with which to parse the $keys variable, if it
   *     is set and not already an array. See SearchApiMultiQuery::parseModes() for
   *     recognized parse modes.
   *   - languages: The languages to search for, as an array of language IDs.
   *     If not specified, all languages will be searched. Language-neutral
   *     content (LANGUAGE_NONE) is always searched.
   *   - offset: The position of the first returned search results relative to
   *     the whole result on the server.
   *   - limit: The maximum number of search results to return. -1 means no
   *     limit.
   *   - 'filter class': Can be used to change the SearchApiQueryFilterInterface
   *     implementation to use.
   *   - 'search id': A string that will be used as the identifier when storing
   *     this search in the Search API's static cache.
   *   All options are optional.
   *
   * @throws SearchApiException
   *   If a search with these options won't be possible.
   */
  public function __construct(SearchApiServer $server, array $options = array()) {
    if (!$server->supportsFeature('search_api_multi')) {
      throw new SearchApiException(t("The search server @name doesn't support multi-index searches.", array('@name' => $server->name)));
    }
    $this->server = $server;
    $this->indexes = search_api_index_load_multiple(FALSE, array('server' => $server->machine_name, 'enabled' => 1));
    if (empty($this->indexes)) {
      throw new SearchApiException(t('There are no enabled indexes on the searched server @name.', array('@name' => $server->name)));
    }
    foreach ($this->indexes as $index) {
      if (empty($index->options['fields'])) {
        throw new SearchApiException(t("Can't search an index which hasn't got any fields defined."));
      }
      if (isset($options['parse mode'])) {
        $modes = $this->parseModes();
        if (!isset($modes[$options['parse mode']])) {
          throw new SearchApiException(t('Unknown parse mode: @mode.', array('@mode' => $options['parse mode'])));
        }
      }
    }
    $this->options = $options + array(
      'conjunction' => 'AND',
      'parse mode' => 'terms',
      'filter class' => 'SearchApiQueryFilter',
      'search id' => __CLASS__,
    );
    $this->filter = $this->createFilter('AND');
    $this->sort = array();
  }

  /**
   * @return array
   *   An associative array of parse modes recognized by objects of this class.
   *   The keys are the parse modes' ids, values are associative arrays
   *   containing the following entries:
   *   - name: The translated name of the parse mode.
   *   - description: (optional) A translated text describing the parse mode.
   */
  public function parseModes() {
    $modes['direct'] = array(
      'name' => t('Direct query'),
      'description' => t("Don't parse the query, just hand it to the search server unaltered. " .
          "Might fail if the query contains syntax errors in regard to the specific server's query syntax."),
    );
    $modes['single'] = array(
      'name' => t('Single term'),
      'description' => t('The query is interpreted as a single keyword, maybe containing spaces or special characters.'),
    );
    $modes['terms'] = array(
      'name' => t('Multiple terms'),
      'description' => t('The query is interpreted as multiple keywords seperated by spaces. ' .
          'Keywords containing spaces may be "quoted". Quoted keywords must still be seperated by spaces.'),
    );
    return $modes;
  }

  /**
   * Parses the keys string according to the $mode parameter.
   *
   * @return
   *   The parsed keys. Either a string or an array.
   */
  protected function parseKeys($keys, $mode) {
    if ($keys == NULL || is_array($keys)) {
      return $keys;
    }
    $keys = '' . $keys;
    switch ($mode) {
      case 'direct':
        return $keys;

      case 'single':
        return array('#conjunction' => $this->options['conjunction'], $keys);

      case 'terms':
        $ret = explode(' ', $keys);
        $ret['#conjunction'] = $this->options['conjunction'];
        $quoted = FALSE;
        $str = '';
        foreach ($ret as $k => $v) {
          if (!$v) {
            continue;
          }
          if ($quoted) {
            if ($v[drupal_strlen($v)-1] == '"') {
              $v = substr($v, 0, -1);
              $str .= ' ' . $v;
              $ret[$k] = $str;
              $quoted = FALSE;
            }
            else {
              $str .= ' ' . $v;
              unset($ret[$k]);
            }
          }
          elseif ($v[0] == '"') {
            $len = drupal_strlen($v);
            if ($len > 1 && $v[$len-1] == '"') {
              $ret[$k] = substr($v, 1, -1);
            }
            else {
              $str = substr($v, 1);
              $quoted = TRUE;
              unset($ret[$k]);
            }
          }
        }
        if ($quoted) {
          $ret[] = $str;
        }
        return array_filter($ret);
    }
  }

  /**
   * Method for creating a filter to use with this query object.
   *
   * @param $conjunction
   *   The conjunction to use for the filter - either 'AND' or 'OR'.
   *
   * @return SearchApiQueryFilterInterface
   *   A filter object that is set to use the specified conjunction.
   */
  public function createFilter($conjunction = 'AND') {
    $filter_class = $this->options['filter class'];
    return new $filter_class($conjunction);
  }

  /**
   * Sets the keys to search for. If this method is not called on the query
   * before execution, this will be a filter-only query.
   *
   * @param $keys
   *   A string with the unparsed search keys, or NULL to use no search keys.
   *
   * @return SearchApiMultiQuery
   *   The called object.
   */
  public function keys($keys = NULL) {
    $this->orig_keys = $keys;
    if (isset($keys)) {
      $this->keys = $this->parseKeys($keys, $this->options['parse mode']);
    }
    else {
      $this->keys = NULL;
    }
    return $this;
  }
  /**
   * Sets the fields that will be searched for the search keys. If this is not
   * called, all fulltext fields should be searched.
   *
   * @param array $fields
   *   An array containing fulltext fields that should be searched.
   *
   * @throws SearchApiException
   *   If one of the fields isn't of type "text".
   *
   * @return SearchApiMultiQueryInterface
   *   The called object.
   */
  public function fields(array $fields) {
    foreach ($fields as $spec) {
      list($index_id, $field) = explode(':', $spec, 2);
      if (!isset($this->indexes[$index_id])) {
        throw new SearchApiException(t("Trying to search on fields of index @index which doesn't lie on server @server.", array('@index' => $index_id, '@server' => $this->server->name)));
      }
      if (empty($this->indexes[$index_id]->options['fields'][$field]) || !search_api_is_text_type($this->indexes[$index_id]->options['fields'][$field]['type'])) {
        throw new SearchApiException(t('Trying to search on field @field which is no indexed fulltext field.', array('@field' => $field)));
      }
    }
    $this->fields = $fields;
    return $this;
  }

  /**
   * Adds a subfilter to this query's filter.
   *
   * @param SearchApiQueryFilterInterface $filter
   *   A SearchApiQueryFilter object that should be added as a subfilter.
   *
   * @return SearchApiMultiQuery
   *   The called object.
   */
  public function filter(SearchApiQueryFilterInterface $filter) {
    $this->filter->filter($filter);
    return $this;
  }

  /**
   * Add a new ($field $operator $value) condition filter.
   *
   * @param $field
   *   The field to filter on, e.g. 'title'.
   * @param $value
   *   The value the field should have (or be related to by the operator).
   * @param $operator
   *   The operator to use for checking the constraint. The following operators
   *   are supported for primitive types: "=", "<>", "<", "<=", ">=", ">". They
   *   have the same semantics as the corresponding SQL operators.
   *   If $field is a fulltext field, $operator can only be "=" or "<>", which
   *   are in this case interpreted as "contains" or "doesn't contain",
   *   respectively.
   *   If $value is NULL, $operator also can only be "=" or "<>", meaning the
   *   field must have no or some value, respectively.
   *
   * @return SearchApiMultiQuery
   *   The called object.
   */
  public function condition($field, $value, $operator = '=') {
    $this->filter->condition($field, $value, $operator);
    return $this;
  }

  /**
   * Add a sort directive to this search query. If no sort is manually set, the
   * results will be sorted descending by relevance.
   *
   * @param $field
   *   The field to sort by. The special fields 'search_api_relevance' (sort by
   *   relevance) and 'search_api_id' (sort by item id) may be used.
   * @param $order
   *   The order to sort items in - either 'ASC' or 'DESC'.
   *
   * @throws SearchApiException
   *   If the field is multi-valued or of a fulltext type.
   *
   * @return SearchApiMultiQuery
   *   The called object.
   */
  public function sort($field, $order = 'ASC') {
    if ($field != 'search_api_relevance' && $field != 'search_api_id') {
      list($index_id, $f) = explode(':', $field, 2);
      if (!isset($this->indexes[$index_id])) {
        throw new SearchApiException(t("Trying to search on fields of index @index which doesn't lie on server @server.", array('@index' => $index_id, '@server' => $this->server->name)));
      }
      $fields = $this->indexes[$index_id]->options['fields'];
      $fields += array(
        'search_api_relevance' => array('type' => 'decimal'),
        'search_api_id' => array('type' => 'integer'),
      );
      if (empty($fields[$f])) {
        throw new SearchApiException(t('Trying to sort on unknown field @field.', array('@field' => $f)));
      }
      $type = $fields[$f]['type'];
      if (search_api_is_list_type($type) || search_api_is_text_type($type)) {
        throw new SearchApiException(t('Trying to sort on field @field of illegal type @type.', array('@field' => $f, '@type' => $type)));
      }
    }
    $order = strtoupper(trim($order)) == 'DESC' ? 'DESC' : 'ASC';
    $this->sort[$field] = $order;
    return $this;
  }

  /**
   * Adds a range of results to return. This will be saved in the query's
   * options. If called without parameters, this will remove all range
   * restrictions previously set.
   *
   * @param $offset
   *   The zero-based offset of the first result returned.
   * @param $limit
   *   The number of results to return.
   *
   * @return SearchApiMultiQueryInterface
   *   The called object.
   */
  public function range($offset = NULL, $limit = NULL) {
    $this->options['offset'] = $offset;
    $this->options['limit'] = $limit;
    return $this;
  }

  /**
   * Executes this search query.
   *
   * @return array
   *   An associative array containing the search results. The following keys
   *   are standardized:
   *   - 'result count': The overall number of results for this query, without
   *     range restrictions. Might be approximated, for large numbers.
   *   - results: An array of results, ordered as specified. The array keys are
   *     arbitrary unique keys, values are arrays containing the following keys:
   *     - id: The item's ID.
   *     - index_id: The machine name of the index this item was found on.
   *     - score: A float measuring how well the item fits the search.
   *     - entity (optional): If set, the fully loaded result item. This field
   *       should always be used by modules using search results, to avoid
   *       duplicate item loads.
   *     - excerpt (optional): If set, an HTML text containing highlighted
   *       portions of the fulltext that match the query.
   *   - warnings: A numeric array of translated warning messages that may be
   *     displayed to the user.
   *   - ignored: A numeric array of search keys that were ignored for this
   *     search (e.g., because of being too short or stop words).
   *   - performance: An associative array with the time taken (as floats, in
   *     seconds) for specific parts of the search execution:
   *     - complete: The complete runtime of the query.
   *     - hooks: Hook invocations and other client-side preprocessing.
   *     - preprocessing: Preprocessing of the service class.
   *     - execution: The actual query to the search server, in whatever form.
   *     - postprocessing: Preparing the results for returning.
   *   Additional metadata may be returned in other keys. Only 'result count'
   *   and 'result' always have to be set, all other entries are optional.
   */
  public final function execute() {
    $start = microtime(TRUE);

    // Add filter for languages.
    if (isset($this->options['languages'])) {
      $this->addLanguages($this->options['languages']);
    }

    // Add fulltext fields, unless set
    if ($this->fields === NULL) {
      $this->fields = array();
      foreach ($this->indexes as $index) {
        $prefix = $index->machine_name . ':';
        foreach ($index->getFulltextFields() as $f) {
          $this->fields[] = $prefix . $f;
        }
      }
    }

    // Call pre-execute hook.
    $this->preExecute();

    // Let modules alter the query.
    drupal_alter('search_api_multi_query', $this);

    $pre_search = microtime(TRUE);

    // Execute query.
    $response = $this->server->searchMultiple($this);

    $post_search = microtime(TRUE);

    // Call post-execute hook.
    $this->postExecute($response);

    $end = microtime(TRUE);
    $response['performance']['complete'] = $end - $start;
    $response['performance']['hooks'] = $response['performance']['complete'] - ($post_search - $pre_search);

    // Store search for later retrieval for facets, etc.
    search_api_multi_current_search(NULL, $this, $response);

    return $response;
  }

  /**
   * Helper method for adding a language filter.
   */
  protected function addLanguages(array $languages) {
    if (array_search(LANGUAGE_NONE, $languages) === FALSE) {
      $languages[] = LANGUAGE_NONE;
    }

    if (count($languages) == 1) {
      $this->condition('search_api_language', reset($languages));
    }
    else {
      $filter = $this->createFilter('OR');
      foreach ($languages as $lang) {
        $filter->condition('search_api_language', $lang);
      }
      $this->filter($filter);
    }
  }

  /**
   * Pre-execute hook for modifying search behaviour.
   */
  protected function preExecute() {}

  /**
   * Post-execute hook for modifying search behaviour.
   *
   * @param array $results
   *   The results returned by the server, which may be altered.
   */
  protected function postExecute(array &$results) {}

  /**
   * @return SearchApiIndex
   *   The search index this query will be executed on.
   */
  public function getServer() {
    return $this->server;
  }

  /**
   * @return array
   *   An array of SearchApiIndex objects representing all indexes that will be
   *   searched.
   */
  public function getIndexes() {
    return $this->indexes;
  }

  /**
   * @return
   *   This object's search keys - either a string or an array specifying a
   *   complex search expression.
   *   An array will contain a '#conjunction' key specifying the conjunction
   *   type, and search strings or nested expression arrays at numeric keys.
   *   Additionally, a '#negation' key might be present, which means – unless it
   *   maps to a FALSE value – that the search keys contained in that array
   *   should be negated, i.e. not be present in returned results.
   */
  public function &getKeys() {
    return $this->keys;
  }

  /**
   * @return
   *   The unprocessed search keys, exactly as passed to this object. Has the
   *   same format as getKeys().
   */
  public function getOriginalKeys() {
    return $this->orig_keys;
  }

  /**
   * @return array
   *   An array containing the fields that should be searched for the search
   *   keys.
   */
  public function &getFields() {
    return $this->fields;
  }

  /**
   * @return SearchApiQueryFilterInterface
   *   This object's associated filter object.
   */
  public function getFilter() {
    return $this->filter;
  }

  /**
   * @return array
   *   An array specifying the sort order for this query. Array keys are the
   *   field names in order of importance, the values are the respective order
   *   in which to sort the results according to the field.
   */
  public function &getSort() {
    return $this->sort;
  }

  /**
   * @param $name string
   *   The name of an option.
   *
   * @return mixed
   *   The option with the specified name, if set, or NULL otherwise.
   */
  public function getOption($name) {
    return isset($this->options[$name]) ? $this->options[$name] : NULL;
  }

  /**
   * @param string $name
   *   The name of an option.
   * @param mixed $value
   *   The new value of the option.
   *
   * @return The option's previous value.
   */
  public function setOption($name, $value) {
    $old = $this->getOption($name);
    $this->options[$name] = $value;
    return $old;
  }

  /**
   * @return array
   *   An associative array of query options.
   */
  public function &getOptions() {
    return $this->options;
  }

}
