# SAMPLE CODES

---

-   [Overview](#overview)
-   [PHP](#php)
-   [JS](#js)
-   [PYTHON](#python)
-   [JAVA](#java)

<a name="overview"></a>

## Overview

We are continually creating helper packages for pupular Languages to help you intergrate seemlesly with our APIs

<a name="php"></a>

## PHP

```php
<?php
$message = "Hello test";
$phone ="0708361797";
$senderid = "CLOUD_REBUE";
sendSms($message, $phone, $senderid);

//sms sending function.
function sendSms ($message, $phone, $senderid){
$url = 'https://rebuetext.com/api/v1/send-sms';
$token = "token_string";

	$post_data=array(
	'sender'=>$senderid,
	'phone'=>$phone,
	'correlator'=>'2',
	'message'=>$message
    );

$data_string = json_encode($post_data);
$ch = curl_init( $url );
curl_setopt( $ch, CURLOPT_POST, 1);
curl_setopt( $ch, CURLOPT_POSTFIELDS, $data_string);
curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt( $ch, CURLOPT_HEADER, 0);
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER,
    array(
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization:Bearer '.$token,
        'Content-Length: ' . strlen($data_string)
        )
    );

$response = curl_exec($ch);
curl_close($ch);
print_r($response);

```

<a name="js"></a>

## JS

```js
const https = require("https");
const token = "token_string";

const data = JSON.stringify({
    sender: "CLOUD_REBUE",
    phone: "0708361797",
    message: "Testing with JavaScript",
});

const options = {
    hostname: "https://rebuetext.com",
    port: 443,
    path: "/api/v1/send-sms",
    method: "POST",
    headers: {
        "Content-Type": "application/json",
        "Content-Length": data.length,
        Authorization: "Bearer" + " " + token,
    },
};
```

<a name="python"></a>

## Python

```python
import requests

BASE_URL = 'https://rebuetext.com/api/v1/send-sms'
token = "token_string"

headers = {'Authorization': "Bearer {}".format(token)}

data = {"phone": "0708361797","sender":"BizTxt","message":"message","correlator":"106","endpoint":"https://xxx.com/delivery"}

response = requests.post(BASE_URL, json=data, headers=headers)
print(response)
print(response.json())
```

<a name="java"></a>

## JAVA

```java
import java.io.BufferedReader;
import java.io.DataOutputStream;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.URL;

class Main
{
    public static void main(String[] args)
    {
        try {
            URL url = new URL("https://rebuetext.com/api/v1/send-sms");
            String token = "token_string";
            String phone = "0708361797";
            String postData = "{\n  \"phone\": \"0708361797\",\n  \"sender\": \"CLOUD_REBUE\",\n  \"message\": \"Testing from JAVA\",\n  \"correlator\": 101\n}";

            HttpURLConnection conn = (HttpURLConnection) url.openConnection();
            conn.setRequestMethod("POST");
            conn.setDoOutput(true);
            conn.setRequestProperty("Content-Type", "application/json");
             conn.setRequestProperty("Authorization", "Bearer ".concat(token));
            conn.setRequestProperty("Content-Length", Integer.toString(postData.length()));
            conn.setUseCaches(false);

            try (DataOutputStream dos = new DataOutputStream(conn.getOutputStream())) {
                dos.writeBytes(postData);
            }

            try (BufferedReader br = new BufferedReader(new InputStreamReader(
                                                conn.getInputStream())))
            {
                String line;
                while ((line = br.readLine()) != null) {
                    System.out.println(line);
                }
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
    }
}
```

<larecipe-swagger  base-url="https://rebuetext.com"  has-auth-header="{ 'Authorization': 'Bearer token_here' }" endpoint="/api/v1/send-sms" default-params="" default-method="post" has-common-headers></larecipe-swagger>

<!-- { 'phone': 'phone_number_here','sender':'CLOUD_REBUE','message':'Test Message','correlator':'test-api' } -->
