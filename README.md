# iSpindle Public Server

This is an attempt to create a public server for the [iSpindel project|(https://github.com/universam1/iSpindel).
The server should allow any iSpindel user to keep a log of the spindle's data and visualize it via charts.

## Approach

The spindles and their data are saved in a relational database.
Users can register and authenticate via email without using a password.
The data is visualized using [C3.js](http://c3js.org/) charts.
A first interface is built with [Bootstrap 4](https://v4-alpha.getbootstrap.com/).

## ERM

A spindle is identified via the ESP8266 chip-id.
A spindle can have a calibration and multiple datapoints with values and a date.
Datapoints can be grouped together to a fermentation, allowing the users to archivate them.
Users are identified by email, have a username, an api-token and a timezone.
Token are used for registering, logging in and cookies.

## API

There is an API to write and read data from the spindles.
For writing, a valid api-token is required and the post contains the spindle-id.
For reading, a user either has to be logged in (for ui-updates) or use their api-token for remote applications.
