# Woodhouse

[![Build Status]](https://travis-ci.org/IcecaveStudios/woodhouse)
[![Test Coverage]](https://coveralls.io/r/IcecaveStudios/woodhouse?branch=develop)
[![SemVer]](http://semver.org)

**Woodhouse** is a command line utility (and PHP library) for publishing build artifacts such as test reports and code
coverage metrics to a GitHub pages repository. It was originally designed to run in a [Travis CI](http://travis-ci.org)
build, but can be used in any environment.

* [Download executable PHAR](http://icecavestudios.github.io/woodhouse/woodhouse)
* Install via [Composer](http://getcomposer.org) package [icecave/woodhouse](https://packagist.org/packages/icecave/woodhouse)

## Features

### Publishing artifacts

The most basic use of **Woodhouse** is to publish build artifacts.

    $ woodhouse publish bob/widget report.html:artifacts/tests.html --auth-token 0be..8a3

The example above publishes a file called **report.html** in the current directory to
**artifacts/tests.html** in the **gh-pages** branch of the **bob/widget** GitHub repository.
Multiple artifacts can be published in a single commit by specifying additional **source:destination** pairs.
The source path may reference individual files or directories.

### Build status badges

**Woodhouse** is able to parse several common test report formats to deduce the result of a build
and publish an appropriate status image. This image can be used in a GitHub README.md file or on
a website to show the current status of the build.

    $ woodhouse publish bob/widget --build-status-image img/status.png --build-status-junit junit.xml --auth-token 0be..8a3

This example parses **junit.xml** to determine the build status, and then publishes the appropropriate
status image to **img/status.png**. The images at the top of this document are
published in this way.

The supported test report formats are:
 * JUnit XML
 * TAP (Test Anything Protocol)
 * PHPUnit JSON

You can also specify the build status directly on the command line using the `--build-status-result` option.

### Coverage badges

Much like the build status images, **Woodhouse** can also publish images showing code coverage percentages.

    $ woodhouse publish bob/widget --coverage-image img/coverage.png --coverage-phpunit coverage.txt --auth-token 0be..8a3

This example parses **coverage.txt** (A file created using PHPUnit's `--coverage-text` option) to determine
the coverage percentage, and then publishes the appropropriate image to **img/coverage.png**.

You can also specify the coverage percentage directly on the command line using the `--coverage-percentage` option.

### Image themes

**Woodhouse** uses [ezzatron/ci-status-images](https://github.com/ezzatron/ci-status-images) for the build status and
coverage images. There are several themes and variants available. The desired theme(s) can be chosen with the
`--image-theme` option. The default theme is [travis/variable-width](https://github.com/ezzatron/ci-status-images/tree/master/img/travis).

## Security

**Woodhouse** requires a GitHub OAuth token with write access to publish content.

**THIS TOKEN MUST BE KEPT SECURE, ANYONE WITH ACCESS TO THIS TOKEN CAN MANIPULATE YOUR GITHUB ACCOUNT**

Under [Travis CI](http://travis-ci.org), [encrypted environment variables](http://about.travis-ci.org/docs/user/build-configuration/#Secure-environment-variables)
can be used to store the token such that it can only be decrypted by Travis. To complement this feature, **Woodhouse**
provides the `--auth-token-env` option to read the token from an environment variable, preventing it from being logged
to the console.

Please note that although it is tempting to create a separate GitHub account solely for publishing of artifacts, this is explicitly
prohibited by GitHub's [Terms of Service](https://help.github.com/articles/github-terms-of-service).

### Creating a GitHub token

A GitHub token can be created using the `github:create-auth` command. This only needs to be done once for your GitHub
account.

    $ woodhouse github:create-auth

You will be prompted for your GitHub username and password. These credentials are used to create the authorization via
the [GitHub API](http://developer.github.com/v3/oauth/#create-a-new-authorization) and are not stored.

### Revoking a GitHub token

If you suspect your token has been compromised, it can be revoked on the [application settings](https://github.com/settings/applications)
page, or using the `github:delete-auth` command.

    $ woodhouse github:list-auth # To get a list of authorizations.
    158534: 0be..8a3 Woodhouse (API) [repo] https://github.com/IcecaveStudios/woodhouse

    $ woodhouse github:delete-auth 158534 # The authorization ID from above.

You will then need to create a new token as described above.

<!-- references -->
[Build Status]: https://travis-ci.org/IcecaveStudios/woodhouse.png?branch=develop
[Test Coverage]: https://coveralls.io/repos/IcecaveStudios/woodhouse/badge.png?branch=develop
[SemVer]: http://calm-shore-6115.herokuapp.com/?label=semver&value=0.5.0&color=yellow
