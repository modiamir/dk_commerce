to setup project run this:

```bash
sh setup.sh
```

for populate products to elasticsearch run this:

```bash
docker-compose exec --user www-data phpfpm bin/digicli digikala:elastic:populate
```