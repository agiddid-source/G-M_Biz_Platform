# Alamos Generator

Alamos Generator is a PHP multi-page application. Each page is rendered on the server, and navigation and form submissions request a new page.

## Routing

`router.php` is the front controller. The named routes and their page handlers are listed in `_inc/routes.php`; `_inc/routing.php` generates application-aware URLs, including when the project runs in a subdirectory such as `/Alamos`.

Apache must have `mod_rewrite` enabled and allow `.htaccess` overrides for this directory. The root `.htaccess` sends application page requests through the front controller and leaves existing static assets accessible. Data files remain blocked from direct web access.

### Admin routes

- `/admin` or `/admin/dashboard` — operations overview
- `/admin/orders` — order list and status updates (`status` is an optional query parameter)
- `/admin/orders/{reference}` — order details and status updates
- `/admin/pos` — point of sale (`sale` is used for the saved-sale message)
- `/admin/customers` — customer list
- `/admin/customers/new` — create a customer
- `/admin/customers/{id}` — customer profile and edits
- `/admin/reports/sales` — sales report (`from` and `to` are optional query parameters)

### Storefront routes

- `/storefront` — redirects to the cart while the storefront landing page is unfinished
- `/storefront/cart`
- `/storefront/checkout`
- `/storefront/confirmation`

Existing PHP page URLs remain as compatibility aliases. GET requests redirect to their canonical route; POST requests are still dispatched to their existing page handler during the transition.

The inventory, rentals, equipment, workers, settings, and account pages are not registered yet because their current PHP files are empty. Admin login and logout are also unfinished; admin routes are not protected by authentication yet.

## Local data

The prototype stores catalog, sales, and customer data in JSON files under `data/`. The cart and checkout flow is a local demo; it does not process payments or send messages.
