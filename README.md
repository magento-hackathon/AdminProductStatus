# Admin Product Status
This project's goal is to allow a user in the Admin the ability to understand why a product may not be displaying on the front-end.

## Current Installation Technique
```
cd app
mkdir code/MagentoHackathon
git clone https://github.com/magento-hackathon/AdminProductStatus.git

Run bin/magento module:enable MagentoHackathon_AdminProductStatus
Run bin/magento setup:upgrade

```

##Information Displayed
* Is Visible
** Sets the scope of the product to a specific store, and checks if the product is salable.
** Checks Stock Records
** Checks Visibility

## Is Indexed
Runs a query to see if the specific product is at a higher version in the index changelog tables than the current version in the mview_state table.  If the product's indexes are not up to date, the indexes are listed.

## Future Plans
Like indexes, we want to know the state of the caches in relation to the specific product.  This includes varnish.

## Difficulites
Getting detailed information from varnish.

## Multistore Implications
If the installation is multi-store, you will not see the status information unless you are scoped to a specific store view on the product edit page.

