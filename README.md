# KyPanel for Blesta

Version 1.0.0 · Blesta 5.0 and later · PHP 7.4 to 8.4

Provisioning module for selling hosting accounts on KyPanel servers.

## Install

1. Copy `components/modules/kypanel` to the same folder in your Blesta.
2. In KyPanel, open Ky Admin › Development › Manage API Tokens and create a token with
   **accounts** (read and change) and **packages** (read). Limit it to the IP of your Blesta server.
3. In Blesta, go to Settings › Modules › Available, install **KyPanel** and add a server:
   hostname, port 7711 and the token.
4. Create a package with the module **KyPanel** and type the name of the KyPanel package.

Creating, suspending, unsuspending, cancelling, password and package changes run on the server.
Clients and staff get a "Log in to KyPanel" link on the service.

## Languages

English and Portuguese (Brazil). To add another, copy `language/en_us` to `language/<code>`
and translate the right side.

License: MIT

Documentation: https://docs.kypanel.com/ · Support: https://my.kypanel.com/tickets/new
