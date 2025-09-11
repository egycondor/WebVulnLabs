from http.server import BaseHTTPRequestHandler, HTTPServer
import json

class H(BaseHTTPRequestHandler):
    def _send(self, code, body, ctype="application/json"):
        self.send_response(code)
        self.send_header("Content-Type", ctype)
        self.end_headers()
        if isinstance(body, (dict, list)):
            self.wfile.write(json.dumps(body, indent=2).encode())
        elif isinstance(body, str):
            self.wfile.write(body.encode())
        else:
            self.wfile.write(body)

    def do_GET(self):
        if self.path == "/" or self.path == "/latest":
            return self._send(200, {"service":"meta","paths":[
                "/latest/meta-data/iam/security-credentials/demo",
                "/secrets/db",
                "/secrets/tokens"
            ]})
        if self.path == "/latest/meta-data/iam/security-credentials/demo":
            return self._send(200, {
                "AccessKeyId": "ASIADEMOACCESSKEY",
                "SecretAccessKey": "DEMOSECRETKEY",
                "Token": "DEMOSESSIONTOKEN",
                "Expiration": "2099-12-31T23:59:59Z"
            })
        if self.path == "/secrets/db":
            return self._send(200, "user=app
password=s3cr3t
host=10.0.0.5
", "text/plain")
        if self.path == "/secrets/tokens":
            return self._send(200, {"serviceA":"token-123","serviceB":"token-456"})
        return self._send(404, {"error":"not found","path": self.path})

if __name__ == "__main__":
    HTTPServer(("0.0.0.0", 8080), H).serve_forever()