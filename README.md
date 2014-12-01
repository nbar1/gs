# GrooveShrimp

A party mode playlist that interfaces with GrooveShark.

## Features

* Unlimited end-users adding songs to central queue
* Ability for users to promote a song to the top of the queue (rate-limit configurable)
* TinySong API fallback when GrooveShark Search API rate-limit is reached (excludes artist search)
* App-specific volume control from player page
* Configurable autoplayer that will play a song that is similar to the last 10 user-played songs when the queue is empty

## Notes
* The current version is based on AngularJS and a REST back-end utilizing Slim-PHP.
* Music playback requires a flash compatible browser. Mobile end-user interface does not require flash.


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