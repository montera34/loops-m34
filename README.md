Loop Shortcodes
=====

Latest version release: 0.1

Loop shortcodes plugin lets you show a list of contents everywhere: inside pages, posts or any other content.

## Usage
To include a list of contents you can use the shortcode `[m34loop]`.

Parameters:

+ To build the loop. This group of parameters works the same way as WP_Query args
  - `post_type`
  - `order`
  - `orderby`
  - `posts_per_page`
  - `taxonomy`
  - `terms`
  - `meta_key`
  - `meta_value`
  - `meta_value_num`
  - `meta_compare`
+ To build each loop item (which fields and how to order them):
  - `fields`: comma separated fields. Options: featured image, title, date, excerpt, a taxnomony slug
+ Placeholders
  - `%today%` in `meta_value` or `meta_value_num` values today's date with the format Y-m-d (usefull to make a loop of current events)
+ Style output
  - `colums`: from 1 to 4
  - `image_size`: thumbnail, medium, large, full or any other registered size
