# Protocol Reference
The project uses a simple REST API to store and retrieve the data using a simple CRUD interface.

Each data is stored as an entity with the following parameters
* `name` the name of the entity
* `value` the string value of the entity
* `ts_publish` a (unix) timestamp of when the entity's value was last published by the client using a POST request (handled by the server)
* `ts_update` a (unix) timestamp provided by the client for when the entity's value was last updated


## Endpoints

### `POST /api/data/{name}`
Used to publish data. The request should be JSON formatted with the following content:
```json
{
    "data":[
        {
            "name": "foo",
            "value": "Hello",
            "ts_update": 123456789,
            "token": "<TOKEN>"
        },
        {
            "name": "bar",
            "value": "World",
            "ts_update": 123456789,
            "token": "<TOKEN>"
        }
    ]
}
```

> See the [Authorization](#authorization) section for details on the `token` field.

[WIP] Response, Error codes



### `GET /api/data/{name}`
Returns the value of the entity with the specified name. Mulitiple entities can be selected by separating their names with a colon (`:`). A `GET` to `/api/data/foo:bar` will return the values of both the `foo` and `bar`entity.

If the entities exists, the following JSON content is returned and a HTTP 200 OK is issued.

```json
{
    "success" : "true",
    "entities": [
        {
            "name": "foo",
            "value": "Hello World",
            "ts_publish": 123456789,
            "ts_update": 123456789
        },
        {
            "name" : "bar",
            "value" : "content",
            "ts_publish": 123456789,
            "ts_update": 123456789
        }
    ]
}
```

## Authorization
To stop everyone from publishing and creating data entities the Data API uses a similar authorization scheme as the Image API.

The authorization tokens are generated in [the same way](api-image-specification.md#token-generation) but instead of utilizing the `Token` header, the token is passed individually for each entity in the JSON formatted POST request. This requires one token for each entity. If the correct token is not provided, the data entity will not be published.


