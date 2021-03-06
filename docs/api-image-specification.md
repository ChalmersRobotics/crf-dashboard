# Image API Specification

## Description
The purpose of this API is to be able to publish images for access from outside the local network, for example webcam images.

## Configuration
The only parameter that needs configuration is the secret key wich is used to authorize image uploads. It can be set in the file `includes/config.php`. 

## Endpoint Usage
### `POST /api/image/{name}`
A form file request to this endpoint is used to publish an image to the specified resource name. The name of the POST file must be `image`, but the name of the file on disk is ignored (except the file extension). The following restrictions apply:
* Allowed file extensions are `.jpeg`, `.jpg` and `.png`
* Max file size: 2 MB
* Must be a correct image file

>The usage of this endpoint requires authorization with the use of the `Token` HTTP header. 

The response of a successful upload is a HTTP 200 together with the following JSON:
```json
{
    "success": true
}
```

If the above conditions are not met or any other error occured during upload (e.g. not authorized), the following JSON response is returned together with one of the HTTP codes `400`, `401` or `500` depending on the error.

```json
{
    "success": false,
    "error": [
        "First error",
        "Second error"
    ]
}
```


### `GET /api/image/{name}`
Returns the content of the image with the specified name. If the resource does not exist, a HTTP 404 is returned. The `Content-Type` header is set according to the MIME type of the image.

## Token generation
An authorization token can be generated by taking the sha256 hash of `key || name || key`, where `||` denotes string concatenation. In other words: `sha256(key + name + key)`.

> Example: With the secret key `foo`, the access token for the resource `bar` would be: `3de5c159297a71aa95da66cc6b864eebca16bcb885d98b3c32bf75c1540d8d98`.
