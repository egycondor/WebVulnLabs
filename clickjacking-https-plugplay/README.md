
# Clickjacking Lab (HTTPS, SameSite=None) — Plug & Play

This lab demonstrates clickjacking on a CSRF-protected action using a third‑party iframe.
Everything is self-contained in Docker: the container generates a self‑signed TLS cert and an HTTPS vhost.

## Hosts
Add to your tester machine:
```
10.10.0.2  victim.example.test attacker.example.test
```

## Run
```bash
unzip clickjacking-https-plugplay.zip
cd clickjacking-https-plugplay
docker compose up -d
```

Victim:
- HTTP:  http://victim.example.test:8099/
- HTTPS: https://victim.example.test:8443/

## Login (victim)
Visit **https://victim.example.test:8443/login.php** and log in:
- user: `alice`
- pass: `alice123`

On login the PHPSESSID cookie is set with **SameSite=None; Secure** so it will be sent in a third‑party iframe.

## Attacker page (different origin)
Serve the decoy from another origin:
```bash
cd src/attacker
python3 -m http.server 8000
# open in browser:
http://attacker.example.test:8000/decoy.html
```

If you see the Login page inside the iframe, you either aren't logged in yet on HTTPS,
or the cookie wasn't accepted. Trust the self‑signed cert in your browser, then reload.

## Debug → Stealth
- The iframe in `attacker/decoy.html` starts with `opacity:0.5` and a lime outline for alignment.
- Switch to stealth by changing to `opacity:0.01` and removing the outline.
- You may also crop the iframe to only cover the red Delete button (see comments in the CSS).
