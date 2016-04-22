// $Id

Drupal.behaviors.buhoBehavior = function(context) {
  /**
   * Superfish Menus
   * http://users.tpg.com.au/j_birch/plugins/superfish/
   * @see js/superfish.js
   */
   /*intelsat-2015 se ha comentado*/ 
  /*jQuery('#navigation ul').superfish({
    animation: { opacity: 'show', height:'show' },
    easing: 'swing',
    speed: 250,
    autoArrows:  false,*/
    /*dropShadows: false*/ /* Needed for IE */
  /*});*/

  /**
   * Forum Comment Link Dialog Box
   * When clicking the link icon in forum comments,
   * this code triggers a dialog box with a link
   * to the comment for easy access to copy it.
   */
  jQuery(".copy-comment").click(function() {
      prompt('Link to this comment:', this.href);
  });
};
