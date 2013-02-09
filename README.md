# Woodhouse

[![Build Status](https://api.travis-ci.org/IcecaveStudios/woodhouse.png)](http://travis-ci.org/IcecaveStudios/woodhouse)
[![Test Coverage](http://icecave.com.au/woodhouse/coverage-report/coverage.png)](http://icecave.com.au/woodhouse/coverage-report/index.html)

**Woodhouse** is a command line utility (and PHP library) for publishing build artifacts such as test reports and code coverage metrics to a GitHub pages repository.
It was originally designed to run in a [Travis CI](http://travis-ci.org) build, but can be used in any environment.

## Installation

* [Download executable PHAR](http://icecave.com.au/woodhouse/woodhouse)
* Available as [Composer](http://getcomposer.org) package [icecave/woodhouse](https://packagist.org/packages/icecave/woodhouse)

## Features

### Publishing artifacts

The most basic use of **Woodhouse** is to publish build artifacts.

    $ woodhouse publish bob/widget report.html:artifacts/tests.html --auth-token 0bee..8a33

The example above publishes a file called **report.html** in the current directory to
**artifacts/tests.html** in the **gh-pages** branch of the **bob/widget** GitHub repository.
Multiple artifacts can be published in a single commit by specifying additional **source:destination** pairs.
The source path may reference individual files or directories.

### Build status badges

**Woodhouse** is able to parse several common test report formats to deduce the result of a build
and publish an appropriate status image. This image can be used in your GitHub README file or on
a website to show the current status of the build.

    $ woodhouse publish bob/widget --build-status-image img/status.png --build-status-junit junit.xml --auth-token 0bee..8a33

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

    $ woodhouse publish bob/widget --coverage-image img/coverage.png --coverage-phpunit coverage.txt --auth-token 0bee..8a33

This example parses **coverage.txt** (A file created using PHPUnit's `--coverage-text` option) to determine
the coverage percentage, and then publishes the appropropriate image to **img/coverage.png**.

You can also specify the coverage percentage directly on the command line using the `--coverage-percentage` option.

### Image themes

**Woodhouse** uses [ezzatron/ci-status-images](https://github.com/ezzatron/ci-status-images) for the build status and coverage images.
There are several themes and variants available. The desired theme(s) can be chosen with the `--image-theme` option. The default theme is
[travis/variable-width](https://github.com/ezzatron/ci-status-images/tree/master/img/travis).

## Security

**Woodhouse** requires a GitHub OAuth token with write access to publish content.

**This token must be kept secure, anyone with access to this token can masquerade as you on GitHub.**

If you are using [Travis CI](http://travis-ci.org) you can use
[encrypted environment variables](http://about.travis-ci.org/docs/user/build-configuration/#Secure-environment-variables) to
store your token such that it can only be decrypted by Travis. To complement this feature, **Woodhouse** provides the
`--auth-token-env` option to read your token from an environment variable, preventing it from being logged to the console.

Please note that although it is tempting to create a separate GitHub account for publishing of artifacts, this is explicitly
prohibited by the [GitHub Terms of Service](https://help.github.com/articles/github-terms-of-service).

### Creating a GitHub token

To acquire a GitHub token you need to create an authorization using the
[GitHub API](http://developer.github.com/v3/oauth/#create-a-new-authorization). This only needs to be done once for your
GitHub account (not for each repository). The command below can be used to create such an authorization.

    $ curl -u <github-username> \
           -d '{"scopes":["repo"],"note":"icecave/woodhouse"}' \
           https://api.github.com/authorizations

### Revoking a GitHub token

If you suspect your token has been compromised, it can be revoked on the [application settings](https://github.com/settings/applications) page.
You will then need to create a new token as described above.
