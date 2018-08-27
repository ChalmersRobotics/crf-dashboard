
# Chalmers Robotics Dashboard for Live Data
## Goal
The goal for this project is to provide a platform for Chalmers Robotics to publish live data to be viewed by the outside world.

## How it works
The core functionality of the project is based on a PHP backend that responds to HTTP requests for publishing data and images. Images are stored on disk, while data is stored in a mysql database.

The frontend then uses JavaScript and AJAX requests to fetch the data from the API and update the HTML contents automatically.

## Requirements [WIP]
A webserver running PHP and a MySQL database.


## Installation [WIP]


## API Details
See [Image API Specification](docs/api-image-specification.md) and [Data API Specification](docs/api-data-specification.md) for more details on the specific APIs.

## Client Library [WIP]
The repository includes a simple python based client library and examples for publishing both images and data. The code uses the excelent [Requests](http://docs.python-requests.org/en/master/) library for HTTP communication.
