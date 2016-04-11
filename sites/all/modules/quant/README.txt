; $Id: README.txt,v 1.1.2.2 2010/08/10 23:23:21 mikestefff Exp $


INTRODUCTION
---------------------------------------------

Quant provides an engine for producing quantitative, time-based analytics 
for virtually any Drupal component. Quant takes raw data about normal Drupal 
actions, such as node creation, and plots the activity over time, with the 
selected time being configurable. 


REQUIREMENTS
---------------------------------------------

Chart API (http://drupal.org/project/chart)


INSTALLATION
---------------------------------------------

1. Add both quant and chart to your site's modules directory
2. Enable both modules
3. If you're using chart-6.x-1.2, it is highly recommended that you apply 
this patch to chart: http://drupal.org/files/issues/chart-fix-division-by-zero.patch
4. Visit quant's settings page: site.com/admin/settings/quant


CHARTS PROVIDED
---------------------------------------------

Content creation
Comment creation
Content creation by type
Aggregate content creation
User creation
User shouts (requires shoutbox)
User point transactions (requires userpoints)
Group creation (requires organic groups)
Group joins (requires organic groups)
