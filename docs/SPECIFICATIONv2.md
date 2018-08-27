# Protocol Reference
The project uses a simple REST API to store and retrieve the data using a simple CRUD interface.

Each data is stored as an entity with the following parameters
* `name` the name of the entity
* `type` the type of the entity
* `value` the value of the entity
* `ts_publish` a (unix) timestamp of when the entity's value was last published by the client using a POST request (handled by the server)
* `ts_update` a (unix) timestamp provided by the client for when the entity's value was last updated


## Available types
[comment]: <> (TODO: Add `number` and `bool`)
The available types are `string` and `image`.

For the `image` type, a GET request to `/api/data/:name/raw` is required to fetch the image contents.

## Endpoints

### `PUT /api/data/{name}`

### `GET /api/data/{name}`
Returns the value of the entity with the specified name. Mulitiple entities can be selected by separating their names with a colon (`:`). A `GET` to `/api/data/foo:bar` will return the values of both the `foo` and `bar`entity.

If the entities exists, the following JSON content is returned and a HTTP 200 OK is issued.

```json
{
    "success" : "true|false",
    "entities": [
        {
            "name": "foo",
            "type": "string",
            "value": "Hello World",
            "ts_publish": 123456789,
            "ts_update": 123456789
        },
        {
            "name" : "bar",
            "type" : "image",
            "value" : null,
            "ts_publish": 123456789,
            "ts_update": 123456789
        },
        ...
    ]
}
```

For an entity of the type `image`, the value returned is set to null, and the contents of the image has to be fetched using `/raw` below.

### `GET /api/data/{name}/raw`
Returns the raw value for the specified entity without any JSON formatting. When the entity is of an `image` type, the HTTP header `Content-Type: image/png` will be sent.

>The `/raw` endpoint does not support multiple entity names as input

### `PUT /api/data/{name}`
