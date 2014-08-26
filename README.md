ircstats
===================

This is a history/stats manager for IRC
Currently it's set up to only work for a single chatroom, but I'm planning to expand that eventually.

Installation
============
  1. Make sure you have the perl DBI interface installed
  2. Load the irssi plugin (keepstats.pl)
  3. Set up the php application as a website (it should be directed at the 'public' directory)
    - An .htaccess file is provided. Of course, if you're able, you should put this in your server config
  4. Getting the sqlite db from your irssi system to the db directory in the php application is left as an exercise for the developer. :)
