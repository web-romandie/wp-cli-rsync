# Pure Taloe

WordPress CLI to rsync files. No database is synchronized since it can easily be done in one command.

```shell
wp db import - <<< $(wp @prod db export -)",
```

# Installation

```shell
wp package install https://github.com/web-romandie/wp-cli-rsync.git
```

# Usage

````shell
wp sync --env=dev
wp sync --env=prod
````
