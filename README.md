# About 
This is a website update for Myriamgurba.com
The point of this was to create a new theme no longer using outdated Genesis framework. The new theme is a child of understrap, which is a bootstrap ready version of Auttomatic's underscores starter theme.

This theme uses bootstrap 5

# Staging site:
https://www.geoffcordner.com/mgstaging2/

# Problems to solve:

1). Using custom post types and custom fields, create a "writing" post type for Myriam's magazine work that in addition to the standard WordPress post or page also has a subtitle, a magazine field naming the magazine or website where the piece was published, a magazine logo, and a url to the externally published piece. Some blog posts will also be placed under writing.

If the piece has a magazine title, show the magazine title. If the piece has an external link, use that link for the featured image, the magazine title, the title, and any other place a url would appear. If not (ie if this is a blog post), use the permalink.

2). Archive pages set to show posts from this custom post type.

3). Create a Books custom post type and custom fields that includes the book cover, a description, ISBN #, published date, price, published formats, and button links to purchase the book, with button title and url. Create a single page to show each book. (No archive necessary).



# getting started:

In wp-content folder:

git init

git remote add origin https://github.com/gcordner/myriamgurba2022.git

git fetch origin

git checkout main

# Active Theme
Active Theme is understrap-child


# NPM Build
This theme uses understrap's build process.

To install dependencies:
npm install

To work and compile npm on the fly:
npm run watch

To create a distributable copy of the theme:
npm run dist

To delete the /dist/ directory and its files:
npm run dist-clean



Complete instructions for npm are here:
https://docs.understrap.com/#/understrap-child/npm

# Required Plugins

Gutenberg plugin is required. 
https://wordpress.org/plugins/gutenberg/




