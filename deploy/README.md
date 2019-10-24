# Deployment

## Requirements

### CLOUD SDK (gcloud)

The Google Cloud SDK contains tools and libraries that allow to create and manage resources on 
Google's Cloud Platform, including App Engine, Compute Engine, Cloud Storage, Cloud SQL, and BigQuery.

For installation instructions see following link.

https://cloud.google.com/sdk/downloads

### Kubernetes-Shell (kubectl)

Kubectl is a command line interface for running commands against Kubernetes clusters. This overview covers the syntax of 
kubectl, describes the command operations and provides common examples. For details about each command, including all 
the supported flags and subcommands, see the kubectl reference documentation.

For installation instructions see following link.

https://kubernetes.io/docs/tasks/tools/install-kubectl/

### Helm

Helm is a tool that streamlines installing and managing Kubernetes applications and resources. Think of it like 
apt/yum/homebrew for Kubernetes. Using helm charts is recommended for installing resources is recommended, because they 
are maintained and typically kept up-to-date by the Kubernetes community.

* Helm has two parts: a client (helm) and a server (tiller)
* Tiller runs inside of your Kubernetes cluster and manages releases (installations) of your helm charts.
* Helm runs on your laptop, CI/CD, or in our case, the Cloud Shell.

For installation instructions see following link.

https://docs.helm.sh/using_helm/#installing-helm
