# cURL calls for testing API functionality
This document contains a number of cURL calls designed for testing the API of this service.

The host used in these calls is the default `localhost`, if the host name has been changed, the commands need to be modified accordingly.

## /api/order/create

- Create a new order with 2 products
  ```bash
    curl --location --request POST 'localhost/api/order/create' \
        --header 'Content-Type: application/json' \
        --data-raw '{
            "buyer_name": "Kis Pista",
            "buyer_email": "kis.pista@example",
            "delivery_type": "pick_up",
            "billing_address": {
                "name": "home",
                "address": "Seholse utca 12.",
                "city": "Budapest",
                "zip": "1234"
            },
            "shipping_address": {
                "name": "home",
                "address": "Seholse utca 12.",
                "city": "Budapest",
                "zip": "1234"
            },
            "ship_to_billing": false,
            "products": [
                {
                    "name": "Kistétel",
                    "quantity": 12,
                    "price": 23
                },{
                    "name": "Nagytétel",
                    "quantity": 5,
                    "price": 23
                }
            ]
        }
        '
  ```
- Create a new order with 2 products, and the same billing and shipping addresses
  ```bash
    curl --location --request POST 'localhost/api/order/create' \
        --header 'Content-Type: application/json' \
        --data-raw '{
            "buyer_name": "Kis Pista",
            "buyer_email": "kis.pista@example",
            "delivery_type": "pick_up",
            "billing_address": {
                "name": "home",
                "address": "Seholse utca 12.",
                "city": "Budapest",
                "zip": "1234"
            },
            "ship_to_billing": true,
            "products": [
                {
                    "name": "Kistétel",
                    "quantity": 12,
                    "price": 23
                },{
                    "name": "Nagytétel",
                    "quantity": 5,
                    "price": 23
                }
            ]
        }
        '
  ```

- **Fail**: Create a new order with no products
  ```bash
    curl --location --request POST 'localhost/api/order/create' \
        --header 'Content-Type: application/json' \
        --data-raw '{
            "buyer_name": "Kis Pista",
            "buyer_email": "kis.pista@example",
            "delivery_type": "pick_up",
            "billing_address": {
                "name": "home",
                "address": "Seholse utca 12.",
                "city": "Budapest",
                "zip": "1234"
            },
            "shipping_address": {
                "name": "home",
                "address": "Seholse utca 12.",
                "city": "Budapest",
                "zip": "1234"
            },
            "ship_to_billing": false,
            "products": []
        }
        '
  ```

- **Fail**: Create a new order with invalid buyer email
  ```bash
    curl --location --request POST 'localhost/api/order/create' \
        --header 'Content-Type: application/json' \
        --data-raw '{
            "buyer_name": "Kis Pista",
            "buyer_email": "kis.pista.example",
            "delivery_type": "pick_up",
            "billing_address": {
                "name": "home",
                "address": "Seholse utca 12.",
                "city": "Budapest",
                "zip": "1234"
            },
            "shipping_address": {
                "name": "home",
                "address": "Seholse utca 12.",
                "city": "Budapest",
                "zip": "1234"
            },
            "ship_to_billing": false,
            "products": [{
                    "name": "Kistétel",
                    "quantity": 12,
                    "price": 23
                }
            ]
        }
        '
  ```

## /api/order/update/{order_id}
The following calls will fail without specifying a valid value in the `{order_id}` path parameter. Use the `/api/order/list` endpoint to fetch valid order data.

- Update the status of an order to `completed`
  ```bash
  curl --location --request POST 'localhost/api/order/update/{order_id}' \
    --header 'Content-Type: application/json' \
    --data-raw '{
        "status": "completed"
    }'
  ```
- **Fail**: Update the status of an order to an invalid status value
  ```bash
  curl --location --request POST 'localhost/api/order/update/{order_id}' \
    --header 'Content-Type: application/json' \
    --data-raw '{
        "status": "throwing_around"
    }'
  ```

## /api/order/list
- List all orders without any filters (empty JSON object)
  ```bash
  curl --location --request POST 'localhost/api/order/list' \
    --header 'Content-Type: application/json' \
    --data-raw '{}'
  ```
- List orders with `status` specified
  ```bash
  curl --location --request POST 'localhost/api/order/list' \
    --header 'Content-Type: application/json' \
    --data-raw '{
        "status": "new"
    }'
  ``` 
- List orders with both `start_date` and `end_date` specified
  ```bash
  curl --location --request POST 'localhost/api/order/list' \
    --header 'Content-Type: application/json' \
    --data-raw '{
        "start_date": "2022-02-07",
        "end_date": "2022-02-08"
    }'
  ```
- List orders with only `start_date` specified
  ```bash
  curl --location --request POST 'localhost/api/order/list' \
    --header 'Content-Type: application/json' \
    --data-raw '{
        "start_date": "2022-02-07"
    }'
  ```
- List orders with only `end_date` specified
  ```bash
  curl --location --request POST 'localhost/api/order/list' \
    --header 'Content-Type: application/json' \
    --data-raw '{
        "end_date": "2022-02-08"
    }'
  ```

- List orders with `order_id` specified (**note**: `order_id` must be valid for any result rows to be returned)
  ```bash
  curl --location --request POST 'localhost/api/order/list' \
    --header 'Content-Type: application/json' \
    --data-raw '{
        "order_id": "1234567"
    }'
  ```

- List orders with all parameters specified (**note**: `order_id` must be valid for any result rows to be returned)
  ```bash
  curl --location --request POST 'localhost/api/order/list' \
    --header 'Content-Type: application/json' \
    --data-raw '{
        "order_id": "1234567",
        "status": "new",
        "start_date": "2022-02-7",
        "end_date": "2022-02-08"
    }'
  ```

- List orders with all parameters except `order_id` specified
  ```bash
  curl --location --request POST 'localhost/api/order/list' \
    --header 'Content-Type: application/json' \
    --data-raw '{
        "status": "new",
        "start_date": "2022-02-7",
        "end_date": "2022-02-08"
    }'
  ```
- **Fail**: List orders with future `end_date` specified
  ```bash
  curl --location --request POST 'localhost/api/order/list' \
    --header 'Content-Type: application/json' \
    --data-raw '{
        "end_date": "2032-02-08"
    }'
  ```
- **Fail**: List orders with future `start_date` specified
  ```bash
  curl --location --request POST 'localhost/api/order/list' \
    --header 'Content-Type: application/json' \
    --data-raw '{
        "start_date": "2032-02-08"
    }'
  ```
- **Fail**: List orders with `start_date` being after `end_date`
  ```bash
  curl --location --request POST 'localhost/api/order/list' \
    --header 'Content-Type: application/json' \
    --data-raw '{
        "start_date": "2032-02-08",
        "end_date": "2022-02-08"
    }'
  ```
- **Fail**: List orders with invalid `status` specified
  ```bash
  curl --location --request POST 'localhost/api/order/list' \
    --header 'Content-Type: application/json' \
    --data-raw '{
        "status": "not_valid"
    }'
  ```