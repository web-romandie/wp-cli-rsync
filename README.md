# WP CLI plugin "rsync"

WordPress CLI to rsync files. No database is synchronized since it can easily be done in one command.

```shell
wp db import - <<< $(wp @prod db export -)",
```

# Installation

Via composer (recommended)

First add theses line in composer.json.

```json
{
  "type": "git",
  "url": "https://github.com/web-romandie/wp-cli-rsync.git"
}
```

Then, we can install the plugin:

```shell
composer install wr/wp-cli-rsync dev-master 
```

Alternatively it can be installed globally with `wp package`

```shell
wp package install https://github.com/web-romandie/wp-cli-rsync.git
```

# Configuration

We need to add some configuration in file `wp-cli.yml`

```yaml
@prod:
  ssh: username@host:port/path
```

# Usage

````shell
wp rsync dev
wp rsync prod
````
