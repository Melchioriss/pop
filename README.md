# Play or Pay

## First start (development environment)

```bash
cp ./.env.dist ./.env

# then edit .env to set up STEAM_API_KEY variable

make from-scratch
make import-pop
make games

# then login into the site

# and make yourself admin
make them=yoursteamname admin

# add yourself into the group in case you're not a member
make them=yoursteamname member of=PoPSG

# that's all
```
