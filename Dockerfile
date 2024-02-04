# https://getkirby.com/docs/cookbook/setup/kirby-meets-docker
FROM webdevops/php-apache-dev:8.1

# The container OS is Debian GNU/Linux 10 (buster) and it runs Git 2.20.1 as its
# latest stable version. We install Git 2.30.2 through backporting, which
# installs new package releases on older OS releases.
# https://unix.stackexchange.com/a/112160/405871
# https://stackoverflow.com/a/54277540/3130281
RUN echo 'deb http://deb.debian.org/debian buster-backports main' > /etc/apt/sources.list.d/backports.list
RUN apt-get update -y
RUN apt-get install -y -t buster-backports git