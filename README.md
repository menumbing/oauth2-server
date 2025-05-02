# Hyperf OAuth2 Server
`menumbing/oauth2-server` is a standards compliant implementation of an [OAuth 2.0](https://tools.ietf.org/html/rfc6749) 
authorization server. This component is [Hyperf](https://www.hyperf.wiki/) component as wrapper of `league/oauth2-server`. 
You can easily configure an OAuth 2.0 server to protect your API with access tokens, or allow clients to request new access tokens and refresh them.

Out of the box it supports the following grants:

* Authorization code grant
* Client credentials grant
* Device authorization grant
* Implicit grant
* Refresh grant
* Resource owner password credentials grant

The following RFCs are implemented:

* [RFC6749 "OAuth 2.0"](https://tools.ietf.org/html/rfc6749)
* [RFC6750 "The OAuth 2.0 Authorization Framework: Bearer Token Usage"](https://tools.ietf.org/html/rfc6750)
* [RFC7519 "JSON Web Token (JWT)"](https://tools.ietf.org/html/rfc7519)
* [RFC7636 "Proof Key for Code Exchange by OAuth Public Clients"](https://tools.ietf.org/html/rfc7636)
* [RFC8628 "OAuth 2.0 Device Authorization Grant](https://tools.ietf.org/html/rfc8628)

## Requirements

* PHP>=8.1
* swoole extension
* openssl extension
* json extension

## Installation

```
composer req menumbing/oauth2-server

php bin/hyperf.php install:oauth2

php bin/hyperf.php start
```

## Generate Client
```
php bin/hyperf.php gen:oauth2-client
```
