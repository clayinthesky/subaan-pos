# HTTP integration checks

Use a disposable installation and Python 3 with Pillow. Set TEST_USERNAME and TEST_PASSWORD to an active staff account; BASE_URL defaults to http://localhost:8080. Run `python tests/http_integration.py` from the project root. This suite changes database records and creates sales; restore a backup after running. The successful verification record is in docs/test-results.json.
