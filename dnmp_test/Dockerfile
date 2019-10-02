FROM centos
 
MAINTAINER brady.wang@qq.com
 
ADD nginx-1.2.9.tar.gz /usr/local/src
 
RUN yum install -y gcc gcc-c++ glibc make autoconf openssl openssl-devel
RUN yum install -y libxslt-devel -y gd gd-devel GeoIP GeoIP-devel pcre pcre-devel
 
RUN useradd -M -s /sbin/nologin nginx
 
VOLUME ["/data"]
 
WORKDIR /usr/local/src/nginx-1.2.9
 
RUN ./configure --user=nginx --group=nginx --prefix=/usr/local/nginx  && make && make install
ENV PATH /usr/local/nginx/sbin:$PATH
 
EXPOSE 80
 
ENTRYPOINT ["nginx"]
 
CMD ["-g","daemon off;"]
