ARG CLI_IMAGE
FROM ${CLI_IMAGE} as cli

FROM govcmsdev/php

RUN apk add --update clamav clamav-libunrar \
    && freshclam

COPY --from=cli /app /app

RUN /app/sanitize.sh \
  && rm -rf /app/sanitize.sh
