# GrooveShrimp

A party mode playlist that interfaces with GrooveShark

## Notes
The current version is based on AngularJS and a RESTful back-end.

## Installation

* Pull in repo
* Run `composer install`
* Import database.sql to your mysql server
* Copy `api/config.php.example` to `api/config.php` and fill in your credentials


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

# Application

## Queue
![Image of Queue](https://raw.githubusercontent.com/nbar1/gs/master/github-resources/images/queue.png)

## Search
![Image of Queue](https://raw.githubusercontent.com/nbar1/gs/master/github-resources/images/search.png)

## Player
![Image of Queue](https://raw.githubusercontent.com/nbar1/gs/master/github-resources/images/player.png)