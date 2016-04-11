<?php
// $Id: opensearch.pages.inc,v 1.1.2.42 2010/09/26 23:30:14 kiam Exp $

/**
 * @file
 * Page callback file for the OpenSearch feed module.
 */

/**
 * Returns an open search description file.
 *
 * @param $info
 *   An array describing the search hook for which visualize information.
 *
 * @return
 *   The XML content for the Opensearch description file.
 */
function opensearch_description($info) {
  // application/opensearchdescription+xml is not standard.
  drupal_set_header('Content-Type: application/xml; charset=utf-8');

  print theme('opensearch_description', $info);
}

/**
 * Returns an open search results feed.
 *
 * @param $info
 *   An array describing the search hook for which visualize information.
 * @param $keywords
 *   The keywords used for the search.
 * @param $results
 *  An array containing the search results.
 *
 * @return
 *   The XML content for the Opensearch feed.
 */
function opensearch_feed($info, $keywords, $results) {
  global $base_url;

  $namespaces = array(
    'xmlns:atom' => 'http://www.w3.org/2005/Atom',
    'xmlns:opensearch' => 'http://a9.com/-/spec/opensearch/1.1/',
    'xmlns:relevance' => 'http://a9.com/-/opensearch/extensions/relevance/1.0/',
  );

  $items = '';
  foreach ($results as $result) {
    // Merge some default values into the search result to avoid PHP notices.
    $result += array('snippet' => '');

    // Build the RSS item's properties and attributes.
    $args = array(array(
      'key' => 'guid',
      'value' => $result['link'],
      'attributes' => array(
        'isPermaLink' => 'false',
      ),
      'relevance:score' => isset($result['score']) ? (float) $result['score'] : NULL,
    ));

    // Format the the search result as an RSS item.
    $items .= format_rss_item(
      $result['title'], $result['link'], $result['snippet'], $args
    );
  }

  $description = _opensearch_check_plain($info->settings['rss_description'], $info->type);
  $title = _opensearch_check_plain($info->settings['rss_title'], $info->type);

  $query = array();
  foreach ($_GET as $index => $value) {
    if ($index != 'q') {
      $query[$index] = $value;

      if ($index == 'page') {
        $query[$index]++;
      }
    }
  }

  $args = array(
    // @todo Find a better way to get the following three values.
    'opensearch:totalResults' => isset($GLOBALS['pager_total_items'][0]) ? $GLOBALS['pager_total_items'][0] : 0,
    'opensearch:startIndex' => isset($GLOBALS['pager_page_array'][0]) ? $GLOBALS['pager_page_array'][0] * 10 + 1 : 1,
    'opensearch:itemsPerPage' => 10,
    array(
      'key' => 'atom:link',
      'attributes' => array(
        'rel' => 'self',
        'href' => url('opensearch/' . $info->type . '/' . drupal_urlencode($keywords), array('query' => $query, 'absolute' => TRUE)),
        'type' => 'application/rss+xml',
      ),
    ),
    array(
      'key' => 'atom:link',
      'attributes' => array(
        'rel' => 'search',
        'href' => url('opensearch/'.  $info->type, array('absolute' => TRUE)),
        'type' => 'application/opensearchdescription+xml',
      ),
    ),
    array(
      'key' => 'atom:link',
      'attributes' => array(
        'rel' => 'alternate',
        'href' => url('search/'.  $info->type, array('absolute' => TRUE)),
        'type' => 'text/html',
      ),
    ),
    array(
      'key' => 'opensearch:Query',
      'attributes' => array('role' => 'request', 'searchTerms' => $keywords),
    ),
  );

  $output = array();
  $output[] = '<?xml version="1.0" encoding="utf-8" ?>';
  $output[] = '<rss version="2.0"' . drupal_attributes($namespaces) . '>';
  $output[] = format_rss_channel($title, $base_url, $description, $items, NULL, $args);
  $output[] = '</rss>';

  drupal_set_header('Content-Type: application/rss+xml; charset=utf-8');
  print implode("\n", $output);
}

/**
 * Presents an opensearch results page.
 * @param $info
 *   An array describing the search hook for which visualize information.
 *
 * @return
 *   The XML content for the Opensearch feed, or the error page 404.
 */
function opensearch_view($info) {
  if ($info->feed) {
    // Retrieve and log the search keywords.
    $keywords = trim(search_get_keys());
    watchdog('opensearch', '%keys (@type).', array('%keys' => $keywords, '@type' => module_invoke($info->type, 'search', 'name')), WATCHDOG_NOTICE, l(t('results'), 'search/' . $info->type . '/' . $keywords));

    // OpenSearch pages are 1-indexed.
    if (isset($_GET['page'])) {
      $_GET['page']--;
    }

    // Collect the search results and display the feed.
    $results = module_invoke($info->type, 'search', 'search', $keywords);
    opensearch_feed($info, $keywords, $results);
  }
  else {
    drupal_not_found();
  }
}
