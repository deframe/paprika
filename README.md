# Paprika Deployment Engine

This utility allows Capistrano-like deployments to remote servers.

## Installation

Paprika can be installed in a similar way to Composer. Just type **curl -sS http://[domain]/paprika/installer | php** into a terminal and you are good to go! The resulting 'paprika.phar' file can be moved to a global location, renamed, etc.

Once installed Paprika can be updated to the latest version by using **php paprika.phar self-update**

## Usage

To create a configuration (or '*Papfile*') in the current directory run **php paprika.phar papify**

*More coming soon!*

## Development

1. *git clone [url]*
2. *composer install*
3. Make changes.
4. Update dist/version and src/Paprika/Application.php with new version
5. Commit changes and tag new version
6. *php bin/compile-paprika*
7. Upload dist/* to the webserver containing the Paprika distribution
8. Tell people to run *php paprika.phar self-update* to get the latest version!