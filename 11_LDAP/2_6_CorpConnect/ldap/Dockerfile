FROM debian:bookworm-slim

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update \
    && echo "slapd slapd/internal/generated_adminpw password CorpConnect_LDAP_Admin!" | debconf-set-selections \
    && echo "slapd slapd/internal/adminpw password CorpConnect_LDAP_Admin!" | debconf-set-selections \
    && echo "slapd slapd/password1 password CorpConnect_LDAP_Admin!" | debconf-set-selections \
    && echo "slapd slapd/password2 password CorpConnect_LDAP_Admin!" | debconf-set-selections \
    && echo "slapd slapd/domain string corpconnect.local" | debconf-set-selections \
    && echo "slapd slapd/organization string CorpConnect" | debconf-set-selections \
    && echo "slapd slapd/backend string MDB" | debconf-set-selections \
    && echo "slapd slapd/purge_database boolean true" | debconf-set-selections \
    && echo "slapd slapd/move_old_database boolean true" | debconf-set-selections \
    && echo "slapd slapd/dump_database select never" | debconf-set-selections \
    && apt-get install -y slapd ldap-utils \
    && rm -rf /var/lib/apt/lists/*

COPY bootstrap/ /bootstrap/

RUN slapd -h "ldap:/// ldapi:///" -u openldap -g openldap -d 0 & \
    SLAPD_PID=$!; \
    i=0; \
    while [ "$i" -lt 120 ]; do \
      ldapsearch -Y EXTERNAL -H ldapi:/// -s base -b "" "(objectClass=*)" >/dev/null 2>&1 && break; \
      i=$((i+1)); \
      sleep 0.25; \
    done; \
    ldapmodify -Y EXTERNAL -H ldapi:/// -f /bootstrap/00-frontend-password-hash.ldif; \
    ldapmodify -Y EXTERNAL -H ldapi:/// -f /bootstrap/01-mdb-sizelimit.ldif; \
    ldapmodify -Y EXTERNAL -H ldapi:/// -f /bootstrap/02-load-modules.ldif; \
    ldapmodify -Y EXTERNAL -H ldapi:/// -f /bootstrap/03-refint-overlay.ldif; \
    ldapmodify -Y EXTERNAL -H ldapi:/// -f /bootstrap/04-memberof-overlay.ldif; \
    ldapadd -x -H ldap://127.0.0.1:389 \
      -D "cn=admin,dc=corpconnect,dc=local" \
      -w "CorpConnect_LDAP_Admin!" \
      -f /bootstrap/10-directory.ldif; \
    kill $SLAPD_PID; wait $SLAPD_PID || true

EXPOSE 389

CMD ["slapd", "-h", "ldap:/// ldapi:///", "-u", "openldap", "-g", "openldap", "-d", "0"]
