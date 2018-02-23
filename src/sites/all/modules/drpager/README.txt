add a theme hook 'drpager' for theming a pager
with a textfield to enter pager number to directly jump to a
page. It's a direct replacement for the built-in 'pager'
with text field replacing the number page links.
This is useful when the number of pages are very large. The
built-in 'pager' theme hook only provide links to navigate through
pages and it's hard to jump very far, say from page 5
to page 340. With the 'drpager', you enter the page number
in the text field and hit enter to go directly to that page.
Also the 'drpager' is much more compact in size than the built-in pager.

Built-in pager:

   << First < Previous  1  2  3  4  5  6 7  8  9 ... Next > Last >>
   
Drpager:

   << First < Previous [ 5 ] of 456 Next > Last >>

There is also an "adaptive" drapgera theme hook. It uses a number of pages
value set in the module settings page to decide to use the original pager
if the number of pagers is less than or equal the specified value or drpager
if greater. This allows you to retain the original pager when the number of
pages are small and switch to drpager when the number is high.

Usage:

Instead of:

theme('pager', ....

use:

theme('drpager', ....

  or
  
theme('drpagera', ...

or go to the module settings page and set a checkbox to override the 'pager' theme
hook to use 'drpager' or 'drpagera'.

Pressing the up arrow, down arrow, page up and page down keys increments/decrements the
page number input value.

Note: Drpager relies on javascript. It won't work if javascript is not
enable on the browser. Unfortunately it's not possible
to make it gracefully degrade because that means drpager need to do form
submit. But pager can exist inside drupal forms, making it impossible
for drpager itself to be a form. Drpager is fully compatible because
it is not a form but just a simple input tag with javascript event
handling attached. 