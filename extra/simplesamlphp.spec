%define name      simplesamlphp
%define summary   SAML IDP/SP written in PHP
%define version   dev
%define release   1
%define license   LGPL 2.1
%define group     Networking/WWW
%define source    %{name}-%version.tar.gz
%define url       https://simplesamlphp.org
%define vendor    UNINETT AS
%define buildroot %{_tmppath}/%{name}-root
%define _prefix   /opt/uninett/simplesamlphp/

Name:      %{name}
Summary:   %{summary}
Version:   %{version}
Release:   %{release}
License:   %{license}
Group:     %{group}
Source0:   %{source}
BuildArch: noarch
Requires:  httpd, mod_ssl, mod_php, php-ldap, php-xml, policycoreutils-python
Requires(pre): shadow-utils
Provides:  %{name}
URL:       %{url}
Buildroot: %{buildroot}

%description
SimpleSAMLphp is an award-winning application written in native PHP that deals with authentication. The project is led by UNINETT, has a large user base, a helpful user community and a large set of external contributors. The main focus of SimpleSAMLphp is providing support for:

    SAML 2.0 as a Service Provider (SP)
    SAML 2.0 as an Identity Provider (IdP)

For further information, see the documentation at https://simplesamlphp.org/docs/.

%prep

%setup

%build

%install
mkdir -p /var/log/simplesamlphp
mkdir -p /var/cache/simplesamlphp
mkdir -p /var/opt/uninett/simplesamlphp/data
mkdir -p %{buildroot}%{_prefix}simplesamlphp/cert
install -d %{buildroot}%{_prefix}

tar cf - . | (cd %{buildroot}%{_prefix}simplesamlphp; tar xfp -)

%pre
   semanage fcontext -a -t httpd_sys_content_t '/opt/uninett/simplesamlphp(/.*)?'
   semanage fcontext -a -t httpd_sys_rw_content_t '/var/opt/uninett/simplesamlphp(/.*)?'
   semanage fcontext -a -t httpd_sys_rw_content_t '/var/cache/simplesamlphp(/.*)?'
   semanage fcontext -a -t httpd_sys_rw_content_t '/var/log/simplesamlphp(/.*)?'

%post
   restorecon -R /opt/simplesamlphp
   restorecon -R /var/opt/uninett/simplesamlphp
   restorecon -R /var/cache/simplesamlphp
   restorecon -R /var/log/simplesamlphp


%postun
# keep the labels, as uninstall + restorecon
# may result in admin revealing sensitive data by mistake.
#    semanage fcontext -d -t httpd_sys_content_t '/opt/uninett/simplesamlphp(/.*)?'
#    semanage fcontext -d -t httpd_sys_rw_content_t '/var/opt/uninett/simplesamlphp(/.*)?'
#    semanage fcontext -d -t httpd_sys_rw_content_t '/var/cache/simplesamlphp(/.*)?'
#    semanage fcontext -d -t httpd_sys_rw_content_t '/var/log/simplesamlphp(/.*)?'

%preun


%files
%defattr(-,root,root)
/var/lib/simplesamlphp/
%dir %attr(0750, root,apache) /opt/uninett/simplesamlphp/config
%config(noreplace) %attr(0640, root,apache) /opt/uninett/simplesamlphp/config/acl.php
%config(noreplace) %attr(0640, root,apache) /opt/uninett/simplesamlphp/config/authsources.php
%config(noreplace) %attr(0640, root,apache) /opt/uninett/simplesamlphp/config/config.php
%dir %attr(0750, root,apache) /opt/uninett/simplesamlphp/metadata
%config(noreplace) %attr(0640, root,apache) /opt/uninett/simplesamlphp/metadata/adfs-idp-hosted.php
%config(noreplace) %attr(0640, root,apache) /opt/uninett/simplesamlphp/metadata/adfs-sp-remote.php
%config(noreplace) %attr(0640, root,apache) /opt/uninett/simplesamlphp/metadata/saml20-idp-hosted.php
%config(noreplace) %attr(0640, root,apache) /opt/uninett/simplesamlphp/metadata/saml20-idp-remote.php
%config(noreplace) %attr(0640, root,apache) /opt/uninett//simplesamlphp/metadata/saml20-sp-remote.php
%config(noreplace) %attr(0640, root,apache) /opt/uninett/simplesamlphp/metadata/shib13-idp-hosted.php
%config(noreplace) %attr(0640, root,apache) /opt/uninett/simplesamlphp/metadata/shib13-idp-remote.php
%config(noreplace) %attr(0640, root,apache) /opt/uninett/simplesamlphp/metadata/shib13-sp-hosted.php
%config(noreplace) %attr(0640, root,apache) /opt/uninett/simplesamlphp/metadata/shib13-sp-remote.php
%dir %attr(0770, root, apache) /var/log/simplesamlphp
%dir %attr(0770, root, apache) /var/cache/simplesamlphp
%dir %attr(0770, root, apache) /var/opt/uninett/simplesamlphp
%dir %attr(0750, root, apache) /opt/uninett/simplesamlphp/cert
