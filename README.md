# ClubConnect

ClubConnect is an online hub that unifies campus clubs.

## setup

mac:

```
brew install php
```

windows: use XAMPP and put this folder in your htdocs directory

both mac and windows:

clone the repo, then run setup.php once to create the database and seed it with clubs:

```
php setup.php
```

then start the local server:

```
php -S localhost:8000
```

open http://localhost:8000 in your browser.

## structure

```
index.php         home page
directory.php     club directory with filters
calendar.php      weekly calendar view
club.php          individual club detail page (takes ?id=X in the url)
setup.php         run this once to build the database
style.css         all styles
main.js           all client side javascript
includes/
  db.php          database connection, used by every page
  header.php      shared nav bar, included at the top of every page
  footer.php      closing tags and script, included at the bottom
assets/           images and other static files
database.db       the sqlite database, already committed so you don't need to run setup again unless you want to reset
```
