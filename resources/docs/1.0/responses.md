# API Responses

---

-   [Overview](#overview)
-   [Response Structure](#res-structure)
-   [Success Response](#res-success)
-   [Error Response](#res-error)
-   [References](#ref)

<a name="overview"></a>

## Overview

In order to help you process our responses in a better and faster way, we have a structured way of sending responses, whether successful or errenous responses.

<a name="res-structure"></a>

## Response Structure

All our responses will be a json Object, with KEY data, as shown below

| Key     | Type                       | Description                                                                                                               |
| :------ | :------------------------- | :------------------------------------------------------------------------------------------------------------------------ |
| Status  | `boolean`                  | The Request status. `true` for a successful request otherwise `false`                                                     |
| message | `string`(required)         | A description of the request, or an explanation of why the request failed                                                 |
| data    | `array,object,disctionary` | Contains all the data requested by an endpoint/all the data necesaary for a successful reconciliation in a future request |

<a name="res-success"></a>

## Sample Success Response

StatusCode `200`

Content

```php
[
    {
        "status": true,
        "message": "Message queued successfully",
        "data": {
            "short_code": "CLOUD_REBUE",
            "phone": "254708XXXXXX",
            "operator": "Safaricom",
            "message": "Test API 1",
            "correlator": 1,
            "delivery_endpoint": null,
            "batch_id": "b493d826-7255-4886-abdd-5550cd084198",
            "uniqueId": "d4cd3f5c-3b22-4174-85aa-392e3435c783",
            "time_to_send": "2024-11-22T20:01:24.750745Z",
            "sms_units": 1,
            "length": 10,
            "remarks": "Message queued successfully",
            "created_at": "2024-11-22 23:01:24"
        }
    }
]
```

<a name="res-error"></a>

## Sample Error Response

StatusCode `401`

Reason `Invalid or Missing Token`

Content

```php
{
    "status": false,
    "message": "Unauthenticated."
}
```

> {info} As with HTTP Restful APIs', always check the response code too

##

<a name="ref"></a>

## References

<a href='https://restfulapi.net/http-status-codes/' target="__blank"> HTTP response Codes </a>.
