# Address Book

---

-   [EndPoints](#endpoints)
-   [Add Contact](#add-contact)
-   [Delete Contact](#delete-contact)
-   [Add Group](#add-group)
-   [Delete Group](#delete-group)

<a name="endpoints"></a>

## Endpoint

The base url is:

```php
https://rebuetext.com/api/v1
```

<a name="add-contact"></a>

## Add Contact

| Method | URI                          |
| :----- | :--------------------------- |
| POST   | `/address-book/contacts/add` |

## Request Params

| Param      | Type               | Description           |
| :--------- | :----------------- | :-------------------- |
| phone      | `string`(required) | Contact Phone Number  |
| first_name | `string`(optional) | Contact First Name    |
| last_name  | `string`(optional) | Contact Last Name     |
| email      | `string`(optional) | Contact Email Address |
| field_1    | `string`(optional) | Custom Value 1        |
| field_2    | `string`(optional) | Custom Value 2        |
| field_3    | `string`(optional) | Custom Value 3        |

## Sample Request Body

```json
{
    "first_name": "Jane",
    "last_name": "Doe",
    "email": "example@example.com",
    "phone": "0738361788",
    "field_1": "Custom Value 1",
    "field_2": "Custom Value 2",
    "field_3": "Custom Value 3"
}
```

## Sample Success Response

StatusCode `200`

Content

```json
{
    "status": true,
    "message": "Contact added successfully",
    "data": {
        "id": 1,
        "first_name": "Jane",
        "last_name": "Doe",
        "email": "example@example.com",
        "phone": "254738361788",
        "operator": "Airtel",
        "custom_fields": {
            "field_1": "Custom Value 1",
            "field_2": "Custom Value 2",
            "field_3": "Custom Value 3"
        },
        "created_at": "2024-11-22 23:49:40"
    }
}
```

## Sample Fail Response

StatusCode `409`

Reason `Conflict. The request conflicts with the current state of the server. So, it could not be processed.`

Content

````json
{
    "status": false,
    "message": "Contact with this phone number 254738361788 already exists.",
    "data": {
        "id": 1,
        "first_name": "Jane",
        "last_name": "Doe",
        "email": "example@example.com",
        "phone": "254738361788",
        "operator": "airtel",
        "custom_fields": {
            "field_1": "Custom Value 1",
            "field_2": "Custom Value 2",
            "field_3": "Custom Value 3"
        },
        "created_at": "2024-11-22 23:49:40"
    }
}
```
<a name="delete-contact"></a>

## DELETE Contact

| Method | URI                         |
| :----- | :-------------------------- |
| DELETE   | `/address-book/contacts/delete/{contact_id}` |

> {info} Replace {contact_id} with the id that was received when creating the contact

## Sample Success Response

StatusCode `200`

Content

```json
{
    "status": true,
    "message": "Contact deleted successfully."
}
````

<a name="add-group"></a>

## Add Group

| Method | URI                    |
| :----- | :--------------------- |
| POST   | `/address-book/groups` |

## Request Params

| Param | Type               | Description       |
| :---- | :----------------- | :---------------- |
| name  | `string`(required) | Name of the Group |

### Sample Request Body

```json
{
    "name": "Group 1"
}
```

## Sample Success Response

StatusCode `200`

Content

```json
{
    "status": true,
    "message": "Group added successfully",
    "data": {
        "id": 1,
        "name": "Group 1",
        "created_by": 2,
        "created_at": "2024-11-23 11:47:53"
    }
}
```

## Sample Fail Response

StatusCode `409`
Reason `Conflict. The request conflicts with the current state of the server. So, it could not be processed.`

Content

```json
{
    "status": false,
    "message": "Group with this name Group 1 already exists.",
    "data": {
        "id": 1,
        "name": "Group 1",
        "created_at": "2024-11-23 11:47:53"
    }
}
```

<a name="delete-group"></a>

## Delete Group

| Method | URI                                      |
| :----- | :--------------------------------------- |
| DELETE | `/address-book/groups/delete/{group_id}` |

> {info} Replace {group_id} with the id that was received when creating the group

## Sample Success Response

StatusCode `200`

Content

```json
{
    "status": true,
    "message": "Group deleted successfully."
}
```

## Sample Fail Response

StatusCode `404`

Content

```json
{
    "status": false,
    "message": "Group not found or does not belong to this account."
}
```
