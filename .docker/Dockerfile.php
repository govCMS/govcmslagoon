ARG CLI_IMAGE
FROM ${CLI_IMAGE} as cli

FROM govcmsdev/php

COPY --from=cli /app /app
