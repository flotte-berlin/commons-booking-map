# Commons Booking Map

**Contributors:** poilu  
**Donate link:** https://flotte-berlin.de/mitmachen/unterstuetzt-uns/  
**Tags:** booking, commons, map, locations  
**Tested:** Wordpress 4.9.x, Commons Booking > 0.9.4  
**License:** GPLv2 or later  
**License URI:** http://www.gnu.org/licenses/gpl-2.0.html  

---
## Description

**Commons Booking Map** is a Wordpress plugin, which extends the [Commons Booking](https://github.com/wielebenwir/commons-booking) Plugin by a map of locations and assigned items. [Openstreetmap](https://openstreetmap.org) is used as base map. These locations will be shown that have items assigned by a timeframe which ends in the future. The raw positioning is done by transforming the address into geo coordinates and can be fine tuned with a map on the 'edit location' page.  
The plugin allows the creation and individual configuration of different maps. Beside other options this contains the height, zoom and map positioning, custom marker images and content of marker popups. The clustering of markers is also supported. Furthermore the items can be prefiltered by categories. It's possible to provide filters to website users, where they can set restrictions for the items shown on the map.  
Beside the presentation of locally managed locations and items Commons Booking Map gives you the capability to export/import locations to/from other plugin installations. By this data can be shared, which allows the aggregation and presentation over multiple Wordpress instances.

The inclusion of a Commons Booking map into a page can be done by a shortcode with following scheme: [cb_map id=123].

## Beschreibung

**Commons Booking Map** ist ein Wordpress Plugin, welches das [Commons Booking](https://github.com/wielebenwir/commons-booking) Plugin um eine Kartendarstellung der Standorte mit den dazugehörigen Artikeln erweitert. Als Kartenmaterial wird [Openstreetmap](https://openstreetmap.org) verwendet. Als Standorte werden jene angezeigt, denen ein Artikel über einen Zeitraum zugeordnet ist, dessen Ende in der Zukunft liegt. Die Positionierung erfolgt grob über die jeweilige Adresse und kann über eine Karte auf der 'Standort bearbeiten'-Seite feinjustiert werden.  
Das Plugin ermöglicht die Erstellung und individuelle Konfiguration von verschiedenen Karten. Dies beinhaltet u.a. die Höhe, Zoom und Kartenpositionierung, angepasste Marker-Grafiken und Inhalte der Marker-Popups. Das Clustern von Markern wird ebenfalls unterstützt. Außerdem können Artikel nach Kategorien vorgefiltert werden. WebseitennutzerInnen können ebenfalls Filter zur Auswahl der angezeigten Artikel angeboten werden.  
Neben der Anzeige lokal verwalteter Standorte und Artikel erlaubt Commons Booking Map den Export/Import von Standorten zu/von anderen Plugin-Installationen. So können Daten mit anderen geteilt werden und es wird die Aggregation und Präsentation über mehrere Wordpress-Instanzen hinweg ermöglicht.

Die Einbindung einer Commons Booking Karte in eine Seite erfolgt über einen Shortcode mit folgendem Schema: [cb_map id=123].

## Screenshot

![example](/screenshots/example_0.5.9_de.png?raw=true "example")

## Credits

[leaflet](https://leafletjs.com)  
[spin.js](http://spin.js.org/)  
[openstreetmap.org](https://openstreetmap.org)  
[JSON Schema for PHP](https://github.com/justinrainbow/json-schema)
