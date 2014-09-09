# Woodhouse Changelog

### 1.0.0 (2014-09-09)

* No API changes

### 0.5.2 (2014-02-17)

* **[FIXED]** Publishing to GitHub no longer produces errors regarding repository clone depth.
* **[IMPROVED]** Updated autoloader to [PSR-4](http://www.php-fig.org/psr/psr-4/)

### 0.5.1 (2014-01-08)

* **[FIXED]** Parsing coverage reports for codebases with zero executable lines no longer produces an error
* **[FIXED]** Explicitly publishing content that is in `.gitignore` no longer produces an error

### 0.5.0 (2013-09-08)

* **[NEW]** Added `--dry-run` option to `publish` command
* **[NEW]** Added `--verbose` option to `publish` command
* **[IMPROVED]** The publish command will now publish "error" images when there is a minor usage failure and the `--no-interaction` option is enabled
* **[IMPROVED]** Bundled status images [3.1.1](https://github.com/ezzatron/ci-status-images/releases/tag/3.1.1)
* **[IMPROVED]** [Duct](https://github.com/IcecaveStudios/duct) is now used to parse PHPUnit JSON streams

### 0.4.3 (2013-04-30)

* **[FIXED]** Added User-Agent header to GitHub API client [as required](http://developer.github.com/changes/2013-04-24-user-agent-required)

### 0.4.2 (2013-02-17)

* **[FIXED]** Fixed bug that caused publication to fail if the target branch did not already exist

### 0.4.1 (2013-02-16)

* **[IMPROVED]** Abstract git execution out of `GitHubPublisher` for better error reporting
* **[FIXED]** Configuration variables `user.name` and `user.email` are now used to prevent identity errors

### 0.4.0 (2013-02-12)

* **[NEW]** Added GitHub auth token management commands: `github:create-auth`, `github:list-auth` and `github:delete-auth`

### 0.3.2 (2013-02-08)

* **[FIXED]** Fixed issue with publishing build-status and coverage images when using woodhouse via PHAR
* **[FIXED]** Publish command no-longer produces an error when there are no artifact changes to publish

### 0.3.1 (2013-01-31)

* **[IMPROVED]** Updated to stable ezzatron/ci-status-images (3.0.1)
* **[FIXED]** Fixed case mismatch between `PhpUnitTextReader` and `PHPUnitTextReader.php`

### 0.3.0 (2013-01-31)

* **[BC]** Renamed publish command from `github:publish` to `publish`
* **[NEW]** Added support for publishing build status images (`--build-status-*` options)
* **[NEW]** Added JUnit, TAP and PHPUnit JSON test report parsers
* **[NEW]** Added support for different image themes via `--image-theme`
* **[BC]** Removed `--fixed-width`

### 0.2.0 (2013-01-19)

* **[NEW]** Added --message option
* **[FIXED]** Allowed relative paths as content sources
* **[IMPROVED]** Improved error reporting when content sources do not exist

### 0.1.0 (2013-01-18)

* Initial release
