
# Chalmers Robotics Dashboard for Live Data
## Goal
The goal for this project is to provide a platform for Chalmers Robotics to publish live data to be viewed by the outside world.

## How it works
The core functionality of the project is based on a PHP backend that responds to HTTP requests for publishing data and images. Images are stored on disk, while data is stored in a mysql database.

The frontend then uses JavaScript and AJAX requests to fetch the data from the API and update the HTML contents automatically.

## Requirements [WIP]
A webserver running PHP and MySQL database.


## Installation [WIP]
1. Clone this repository using
    ```
    git clone https://github.com/ChalmersRobotics/crf-dashboard.git
    cd crf-dashboard
    ```
2. Upload `/www` to webserver
3. Create the database table, se [here](docs/api-data-specification.md#database-setup).
4. Rename or copy `config.example.php` to `config.php` and edit the settings
   * Change both the `key_secret` and `salt` to new uniqe random strings
   * Fill out the database connection settings
5. Test it out!
6. Edit the dashboard to your preferences (the one in this repository is highly customized for usage by Chalmers Robotics!)


## API Details
See [Image API Specification](docs/api-image-specification.md) and [Data API Specification](docs/api-data-specification.md) for more details on the specific APIs.

## Client Library [WIP]
The repository includes a simple python based client library and examples for publishing both images and data. The code uses the excelent [Requests](http://docs.python-requests.org/en/master/) library for HTTP communication.

## Token Generator
A publicly available token generator is included in the `/token-gen` directory. It is based on JavaScript using the [js-sha256]( https://github.com/emn178/js-sha256) library. All calculations are done in the browser, eliminating the need for sending senitive secret keys over the internet. It can be used to generate tokens for both the Image and Data API.