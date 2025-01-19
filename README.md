# :money_with_wings: Amazon Product Prices :shopping:

> Project initially created to perform an automated search for product price information on some digital platforms.

## :technologist: Technologies
<div>
   <img src="https://skillicons.dev/icons?i=laravel" title="laravel" alt="laravel" width="40" height="40"/>
   <img src="https://skillicons.dev/icons?i=mongodb" title="mongodb" alt="mongodb" width="40" height="40"/>
   <img src="https://skillicons.dev/icons?i=redis" title="redis" alt="redis" width="40" height="40"/>
   <img src="https://skillicons.dev/icons?i=docker" title="docker" alt="docker" width="40" height="40"/>
</div>

## :airplane: Startup Project
 - Clone the project: `git clone https://github.com/YoungC0DE/AmazonProductPrices.git`
 - Open the project in your IDE (I recommend vscode)
 - Open the terminal in this project, and start the docker with: `docker-compose up -d` (if you use windows, don't forgot to start the docker desktop)

## :shipit: API Documentation

> Use the Insomnia or Postman to make the requests.

Url base in local enviroment: `http://localhost:81/`

#### :large_blue_circle: Return all tickets

    GET /api/ticket/get-all

#### :large_blue_circle: Return a Ticket by ID

    GET /api/ticket/${id}

| Parameter | type     | Description                 |
| :-------- | :------- | :-------------------------- |
| `id`      | `string` | **Required**. The ticket Id |

#### :green_circle: Create a Ticket

    POST /api/ticket/create

| Parameter   | type       | Description                     |
| :---------- | :--------- | :------------------------------ |
| `requestSettings.platform` | `string` | **Required**. Choose one of those platforms: AMAZON, MERCADO_LIVRE, OLX, EBAY |
| `requestSettings.searchQuery` | `string` | **Required**. Enter the product you want to search for (min 3 caracteres) |
| `filters.sortBy` | `string` | **Required**. Set the order of products: (priceAscending, priceDescending, relevance) |
| `filters.ratingAbove` | `string` | **Optional**. Set the minimum rating of the products you want to see (min 0, max 5) |

## Authors

 - [@YoungC0DE](https://www.github.com/YoungC0DE)
 - [@jelsononofre](https://www.github.com/jelsononofre)

[![MIT License](https://img.shields.io/badge/License-MIT-green.svg)](https://choosealicense.com/licenses/mit/)
