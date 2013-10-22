# gs

A party mode playlist that interfaces with GrooveShark

## Installation

* Pull in repo
* Run `composer install`
* Create a directory in the root called tmp with proper write permissions
* Import database.sql to your mysql server
* Update config.php with your specific details


## GrooveShark API

You must have a GrooveShark API key to use this application.
You can obtain a GrooveShark API key at [developers.grooveshark.com](http://developers.grooveshark.com/).

## GrooveShark Account

One GrooveShark account provisioned with Grooveshark Anywhere is required to stream the songs to the player page.
You can obtain a GrooveShark Anywhere account at [grooveshark.com](http://grooveshark.com).

## GrooveShark API Required Methods

Beyond basic GrooveShark Public API v3 access, you should have access to the following methods:
* startAutoplay
* getSubscriberStreamKey
