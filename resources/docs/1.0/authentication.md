# Authentication

---

Our endpoints make use of Auth Tokens to authenticate incoming requests,

-   [Generate API Keys](#authentication)
-   [Sample Header](#sample-header)

<a name="authentication"></a>

## Generate Token

1. Head over to your [dashboard](https://rebuetext.com/). Under `Account` , you should be able to navigate to `API Keys` tab.
2. Click on `Create Token`. Copy the generated token somewhere safe as we WON'T display it again

![Account](https://ressirli.sirv.com/Cloud%20Rebue/account-token.png) 3. The generated `keys` MUST be appended on all your requests' Headers, under the <a href='https://swagger.io/docs/specification/authentication/bearer-authentication/' target="__blank"> Bearer Token Authorization.</a>

<a name="sample-header"></a>

### Sample Header

```php
{
    "Content-type: application/json",
    "Accept: application/json",
    "Authorization: Bearer 123456789"
}
```

> {info} Please NOTE that Authorization value is a concatenation of string `Bearer` and `your generated token` with a white space between the two

##

> {danger} The Generated Token Gives a person full access to your account. Don't share it under any circumstance️
