This project includes a base REST API class along with web service specific API classes.

The RestApi class takes care of making requests, caching, and parsing various response formats (JSON, XML, and serialized PHP).

Also included in this project are extensions of the RestApi class for specific web services.  So far, this includes:
  * Twitter
    * Twitter (OAuth)
  * Twitter Search
  * Flickr
  * New York Times
    * Article Search
  * RightScale
  * kl.am
  * Foursquare
  * Last.fm
  * Nike+
  * Tumblr

Some of these services are more complete than others.  When using these service extensions, make sure they are placed in the same directory as the base class.restapi.php file.