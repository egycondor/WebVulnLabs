"""
StaffHub — intentionally vulnerable staff directory (XPath injection training app).
"""

from __future__ import annotations

import os
from pathlib import Path

from flask import (
    Flask,
    jsonify,
    redirect,
    render_template,
    request,
    session,
    url_for,
)
from markupsafe import escape
from lxml import etree

from xpath2_simulation import XPATH2_MARKERS, preprocess_for_xpath2

BASE_DIR = Path(__file__).resolve().parent
DATA_PATH = BASE_DIR / "data" / "employees.xml"

app = Flask(__name__)
app.secret_key = "staffhub-hardcoded-secret-key-change-in-production"

TREE = None


def load_tree():
    global TREE
    if TREE is None:
        TREE = etree.parse(str(DATA_PATH))
    return TREE


@app.after_request
def add_powered_by(response):
    response.headers["X-Powered-By"] = "lxml/4.9"
    return response


def require_login():
    return "user" in session


@app.route("/")
def index():
    if require_login():
        return redirect(url_for("dashboard"))
    return redirect(url_for("login"))


@app.route("/login", methods=["GET", "POST"])
def login():
    if request.method == "GET":
        return render_template("login.html", error=None)

    username = request.form.get("username", "")
    password = request.form.get("password", "")

    # Intentionally vulnerable: concatenation into XPath (xpath injection on username/password).
    xpath = (
        f"/employees/employee[username/text()='{username}' "
        f"and password/text()='{password}']"
    )

    try:
        tree = load_tree()
        nodes = tree.xpath(xpath)
    except etree.XPathEvalError as e:
        return (
            render_template(
                "generic_error.html",
                html_comment=(
                    f"<!-- Error: lxml.etree.XPathEvalError: {escape(str(e))} -->"
                ),
            ),
            500,
        )

    if not nodes:
        return render_template("login.html", error="Invalid credentials")

    node = nodes[0]
    session["user"] = {
        "username": node.findtext("username", default=""),
        "name": node.findtext("name", default=""),
        "role": node.findtext("role", default=""),
        "department": node.findtext("department", default=""),
    }
    return redirect(url_for("dashboard"))


@app.route("/dashboard")
def dashboard():
    if not require_login():
        return redirect(url_for("login"))
    u = session["user"]
    return render_template("dashboard.html", user=u)


@app.route("/search")
def search():
    if not require_login():
        return redirect(url_for("login"))
    q = request.args.get("q", "")
    # Intentionally vulnerable: concatenation (in-band reflection in results template).
    xpath = f"/employees/employee[contains(department/text(),'{q}')]"
    tree = load_tree()
    try:
        nodes = tree.xpath(xpath)
    except etree.XPathEvalError:
        nodes = []

    rows = []
    for n in nodes:
        rows.append(
            {
                "name": n.findtext("name", default=""),
                "department": n.findtext("department", default=""),
                "title": n.findtext("title", default=""),
            }
        )
    return render_template("search.html", q=q, rows=rows)


@app.route("/api/lookup")
def api_lookup():
    employee = request.args.get("employee", "")

    # Intentionally vulnerable: concatenation (blind XPath — only JSON status differs).
    xpath = f"/employees/employee[name/text()='{employee}']"

    if any(marker in xpath for marker in XPATH2_MARKERS):
        xpath = preprocess_for_xpath2(xpath)

    tree = load_tree()
    try:
        nodes = tree.xpath(xpath)
    except etree.XPathEvalError:
        return jsonify(status="not_found")

    if nodes:
        return jsonify(status="found")
    return jsonify(status="not_found")


@app.route("/api/filtered-search")
def filtered_search():
    """Same as /search — routed through Nginx WAF simulation on /api/filtered-search."""
    if not require_login():
        return redirect(url_for("login"))
    q = request.args.get("q", "")
    xpath = f"/employees/employee[contains(department/text(),'{q}')]"
    tree = load_tree()
    try:
        nodes = tree.xpath(xpath)
    except etree.XPathEvalError:
        nodes = []

    rows = []
    for n in nodes:
        rows.append(
            {
                "name": n.findtext("name", default=""),
                "department": n.findtext("department", default=""),
                "title": n.findtext("title", default=""),
            }
        )
    return render_template("search.html", q=q, rows=rows)


@app.route("/robots.txt")
def robots_txt():
    body = (
        "User-agent: *\n"
        "Disallow: /api/\n"
        "Allow: /static/config.xml\n"
    )
    return app.response_class(body, mimetype="text/plain")


@app.route("/static/config.xml")
def static_config_xml():
    xml = """<?xml version="1.0" encoding="UTF-8"?>
<config>
  <app_name>StaffHub</app_name>
  <version>1.0</version>
  <database_type>xml-native</database_type>
</config>
"""
    return app.response_class(xml, mimetype="application/xml")


if __name__ == "__main__":
    app.run(host="0.0.0.0", port=int(os.environ.get("PORT", "5000")))
