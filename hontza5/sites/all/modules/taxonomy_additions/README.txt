$Id: README.txt,v 1.1 2011/02/01 00:20:14 amateescu Exp $

CONTENTS OF THIS FILE
---------------------

 * Overview
 * Installation
 * Featues and API functions
 * Todo
 * Credits


Overview
--------

This module adds various improvements to the core Taxonomy module.


Installation 
-------------------

1. Place the entire taxonomy_additions directory into your Drupal modules
   directory (normally sites/all/modules).

2. Enable the taxonomy_additions module by navigating to:

   Administer > Site building > Modules


Featues and API functions
----------------------------

- Features:

  - Delete link in the vocabulary terms list.

    Navigate to Administer > Content management > Taxonomy > list terms (from a
    vocabulary) and enjoy deleting a term without going through the edit page.

  - Option to not allow duplicate terms in a vocabulary.

    Navigate to Administer > Content management > Taxonomy > edit vocabulary
    and check the "Prevent duplicate terms" option from the "Settings" group.

  - Views argument handler which adds the capabality of showing the results of
    related terms' nodes (e.g. If you have a view list for the term "Drupal",
    and that term is related to "Drupal core", nodes that are tagged with
    "Drupal core" will also show up in that view).

    1. Create a view at admin/build/views/add
    2. Choose "View Type: node"
    3. Select the desired filters and fields
    4. At "Arguments" add "Taxonomy: Term ID"
    5. At "Validator Options" select "Taxonomy term related"
    6. (optional) Select a specific vocabulary for restricting the term search

- API functions:

  - Provides an equivalent to taxonomy_get_term_by_name() which adds an
    extra parameter ($vid) for restricting the search to a specific vocabulary.

    function taxonomy_additions_get_term_by_name($name, $vid = NULL)

    @see: taxonomy_get_term_by_name() from taxonomy.module

  - Provides an equivalent to taxonomy_node_get_terms_by_vocabulary() which
    saves a database query by searching for the terms directly in the $node object.
    Should be used only after a node_load().

    function taxonomy_additions_node_get_terms_by_vocabulary($node, $vid, $key = 'tid')

    @see: taxonomy_node_get_terms_by_vocabulary() from taxonomy.module

  - Provides an equivalent to taxonomy_get_tree() which gets a more useful
    taxonomy tree, with "nesteds" terms in an array that you can
    access with $term->children.

    function taxonomy_additions_get_nested_tree($terms = array(), $max_depth = NULL, $parent = 0, $parents_index = array(), $depth = 0)

    @see: taxonomy_get_tree() from taxonomy.module


Todo
----

- Improve documentation and function signature for taxonomy_additions_get_nested_tree.
- Add code samples for the API functions provided by this module.


Credits
-------

Developed and maintained by Tremend Software Consulting - http://www.tremend.ro/

Written by:
 - Andrei Mateescu (amateescu) - http://drupal.org/user/729614
