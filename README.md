# GovCMS Lagoon project - Drupal 7

## Overview

This project is used to create the images required by Lagoon, using the GovCMS distribution - it is only intended to
be used by distribution/platform maintainers.

Images are published to the [govcmslagoon](https://hub.docker.com/u/govcmslagoon) namespace on Docker Hub.

There is also the equivalent project for [GovCMS Drupal 8 images](https://github.com/govcms/govcms8lagoon). Please
be mindful that there is some duplication across the two projects, so consider whether pull requests for changes
should be accompanied by PRs on the other repository.

## Instructions

Please refer to [govcms8lagoon](https://github.com/govcms/govcms8lagoon#instructions) for guidelines.

## Releasing a govcmslagoon release to dockerhub

1. Prepare a release branch from master (release/govcmslagoon-3.x.0 - replace x with the correct version)
2. Update the .env.default GOVCMS_PROJECT_VERSION with the latest GovCMS release tag (defaults to 7.x-3.x in docker-compose)
3. Update the .env.default LAGOON_IMAGE_VERSION with the latest Lagoon release tag (defaults to :latest in docker-compose)
4. Update the .env.default LAGOON_IMAGE_VERSION_PHP with the latest Lagoon release tag (defaults to null - equivalent to :latest - in docker-compose)
5. Update the .env.default SITE_AUDIT_VERSION with the latest Site Audit release tag (defaults to 7.x-3.x in docker-compose)
6. Add a 3.x.0-rc1 tag to this branch and push to Github - this will update the :beta and :3.x.0-rc1 tags on dockerhub
7. Deploy a couple of test projects to Openshift on the :beta tags (you may need to refresh the beta tags on the docker-host)
8. When ready to release, push the 3.x.0 tag to Github, and follow up with the `ahoy release` process
