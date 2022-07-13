# Symfony 6.1 with Tailwind CSS basic project

Use this as a template to get your own SF 6.1 project going.
This project is configured so that you must login to access any page - but you could modify
the permissions easily enough.

This project was created on MacOS, so the instructions are tested on MacOS.

## Development - Getting Up and Running

### First time working on this project?

You should use your own APP_SECRET in `.env` - generate one here: [http://nux.net/secret](http://nux.net/secret)

If you don't have the `symfony` console command, you can install it with:

`brew install symfony-cli/tap/symfony-cli`

The first time you use the Symfony Proxy you will need to configure it and do some tweaks to your local networking:

1. `symfony server:ca:install`
2. `symfony proxy:start`
3. `symfony proxy:domain:attach mydevsite`
4. You will now need to set a proxy in your network stack - follow instructions from https://symfony.com/doc/current/setup/symfony_server.html#local-domain-names)

#### If you have prepared the proxy (or don't want to use it):

1. `composer install`
2. Create an `.env.local` with appropriate db connection details and other properties as necessary
3. `yarn install`
4. `symfony serve -d`
5. `yarn dev-server`

#### Create a default dev database

6. `symfony console doctrine:database:create`
7. `symfony console doctrine:migrations:migrate`

## Test User

The migrations include a test login:

> - **username**: test@example.com
> - **password**: password

This password will only work if you use the original APP_SECRET.
If you changed the secret (which is advised) you can use the console command `symfony console security:hash-password`
to generate a new password has, then store that in the migration or
update it on your db.


## Browser Problems

I use the Brave browser. It always defaults to blocking the JS/CSS (maybe because it comes from a different domain).
So, I have to disable the Brave shields before I can see all the styling. I don't have this problem when the site
is deployed.
