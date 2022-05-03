# Pull official Cypress image
FROM cypress/base:14.16.0

WORKDIR /e2e

# Copy E2E Tests
COPY . .

# Install NPM dependencies and Cypress binary
RUN npm install

# Check if the binary was installed successfully
RUN npx cypress verify

RUN echo 'npx cypress install\nsleep infinity' > /bootstrap.sh
RUN chmod +x /bootstrap.sh

CMD /bootstrap.sh
