# 1 - Install
```bash
composer require ywoume/imkcrudbundle
```
Copy and path this in your terminal.
# 2 - Charge in symfony
in ```Bundle.php``` ad this line
```php
// file: config/bundle.php
ImkCrudBundle\ImkCrudBundle::class => ['all' => true],

```
#3 - Configuration

add ``imk_crud.yaml in packages/ folders`` and paste this inside ``imk_crud: ~`` 

## default configuration

This is an example default configuration form an entity

```yaml
imk_crud:
  entity:
    - Users:
        crud: true
        create: true
        read: true
        list: true
        delete: true
        update: true

```

