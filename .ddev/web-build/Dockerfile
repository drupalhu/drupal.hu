ARG BASE_IMAGE
FROM $BASE_IMAGE

ENV NVM_DIR='/usr/local/nvm'
ARG APP_NVM_REVISION='v0.37.2'

RUN mkdir -p "${NVM_DIR}"
RUN curl \
    --silent \
    --location \
    "https://raw.githubusercontent.com/nvm-sh/nvm/${APP_NVM_REVISION}/install.sh" \
    --output install_nvm.sh \
  && \
  bash install_nvm.sh \
  && \
  rm install_nvm.sh

RUN echo "source ${NVM_DIR}/nvm.sh" >> '/etc/profile'

ARG APP_NODE_VERSION='14.17.1'
ARG APP_NPM_GLOBAL_PACKAGES='yarn'
ARG APP_YARN_GLOBAL_PACKAGES='node-sass'

RUN bash -i -c " \
  nvm install ${APP_NODE_VERSION} \
  && \
  nvm use ${APP_NODE_VERSION} \
  && \
  nvm alias default ${APP_NODE_VERSION} \
  && \
  npm --global install ${APP_NPM_GLOBAL_PACKAGES} --save \
  && \
  yarn global add ${APP_YARN_GLOBAL_PACKAGES} --save \
"
RUN chmod --recursive ugo+w "${NVM_DIR}"
