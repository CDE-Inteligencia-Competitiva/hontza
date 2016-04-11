<?php
// $Id: opensearch-description.tpl.php,v 1.1.2.6 2010/07/13 03:46:34 kiam Exp $

/**
 * @file
 * Displays a Opensearch description file.
 *
 * Available variables:
 * - $shortname: The short name.
 * - $description: The description.
 * - $contact: The contact email address for the site.
 * - $url_opensearch: The URL for the Opensearch feed.
 * - $url_self: The URL for the Opensearch description file.
 * - $tags: A set of words that are used as keywords to identify and categorize
 *   this search content. Keywords must be a single word and are delimited by
 *   the space character.
 * - $longname: An extended human-readable title that identifies this search
 *   engine.
 * - $attribution: A list of all sources or entities that should be credited
 *   for the content contained in the search feed.
 * - $syndication_right: The degree to which the search results provided by
 *   this search engine can be queried, displayed, and redistributed.
 * - $adult_content: A boolean value that is set to TRUE if the search results
 *   may contain material intended only for adults.
 * - $image_attributes: A string containing the image attributes for the image
 *   to use for the search engine.
 * - $image_url: The URL for the image to use for the search engine.
 * - $languages: An array that indicates for which languages the search engine
 *   returns search results.
 */
?>
<?php print "<?xml"; ?> version="1.0" encoding="UTF-8" <?php print "?>"; ?>
<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/">
  <ShortName><?php print $shortname; ?></ShortName>
  <Description><?php print $description; ?></Description>
  <Contact><?php print $contact; ?></Contact>
  <Url type="text/html" template="<?php print $url_search; ?>"/>
  <Url type="application/rss+xml" template="<?php print $url_opensearch; ?>"/>
  <Url type="application/opensearchdescription+xml" rel="self" template="<?php print $url_self; ?>" />
  <?php if (!empty($tags)): ?>
    <Tags><?php print $tags; ?></Tags>
  <?php endif; ?>
  <?php if (!empty($longname)): ?>
    <LongName><?php print $longname; ?></LongName>
  <?php endif; ?>
  <?php if ($attribution): ?>
    <Attribution><?php print $attribution; ?></Attribution>
  <?php endif; ?>
  <?php if ($syndication_right): ?>
    <SyndicationRight><?php print $syndication_right; ?></SyndicationRight>
  <?php endif; ?>
  <AdultContent><?php print $adult_content; ?></AdultContent>
  <?php if (!empty($image_attributes)): ?>
    <Image<?php print $image_attributes; ?>><?php print $image_url; ?></Image>
  <?php endif; ?>
  <?php foreach ($languages as $language): ?>
    <Language><?php print $language->language; ?></Language>
  <?php endforeach; ?>
  <InputEncoding>UTF-8</InputEncoding>
  <OutputEncoding>UTF-8</OutputEncoding>
</OpenSearchDescription>
