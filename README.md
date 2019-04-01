# GovCMS Lagoon project

[![CircleCI](https://circleci.com/gh/govCMS/govcmslagoon.svg?style=svg&circle-token=b356e4c11fbbf32973d909ee37e048da981fc663)](https://circleci.com/gh/govCMS/govcmslagoon)

## Requirements

* [Docker](https://docs.docker.com/install/)
* [pygmy](https://docs.amazee.io/local_docker_development/pygmy.html#installation) (you might need sudo for this depending on your ruby configuration)
* [Ahoy](http://ahoy-cli.readthedocs.io/en/latest/#installation)

## Purpose

This project is used to create the images required by Lagoon, using the GovCMS distribution - it is only intended to be used by distribution/platform maintainers.

## Commands

Additional commands are listed in `.ahoy.yml`.

## Releasing a govcmslagoon release to dockerhub

1. Prepare a release branch from master (release/govcmslagoon-3.x.0 - replace x with the correct version)
2. Update the .env.default GOVCMS_PROJECT_VERSION with the latest GovCMS release tag (defaults to 7.x-3.x in docker-compose)
3. Update the .env.default LAGOON_IMAGE_VERSION with the latest Lagoon release tag (defaults to :latest in docker-compose)
4. Update the .env.default LAGOON_IMAGE_VERSION_PHP with the latest Lagoon release tag (defaults to null - equivalent to :latest - in docker-compose)
5. Update the .env.default SITE_AUDIT_VERSION with the latest Site Audit release tag (defaults to 7.x-3.x in docker-compose)
6. Add a 3.x.0-rc1 tag to this branch and push to Github - this will update the :beta and :3.x.0-rc1 tags on dockerhub
7. Deploy a couple of test projects to Openshift on the :beta tags (you may need to refresh the beta tags on the docker-host)
8. When ready to release, push the 3.x.0 tag to Github, and follow up with the `ahoy release` process
