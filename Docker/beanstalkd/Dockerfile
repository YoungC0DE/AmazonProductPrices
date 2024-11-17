FROM ubuntu:focal

RUN apt update
RUN apt -y install wget beanstalkd

RUN wget https://github.com/src-d/beanstool/releases/download/v0.2.0/beanstool_v0.2.0_linux_amd64.tar.gz
RUN tar -xvzf beanstool_v0.2.0_linux_amd64.tar.gz
RUN cp beanstool_v0.2.0_linux_amd64/beanstool /usr/local/bin/
RUN rm -f beanstool_v0.2.0_linux_amd64.tar.gz

EXPOSE 11300

CMD ["beanstalkd"]

