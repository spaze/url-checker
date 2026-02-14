# UrlChecker

A command line tool to check URL HTTP code and contents.

## Install
```
composer create-project --no-dev spaze/url-checker url-checker
```

## Usage
```
check.php <url> <expected_http_code> <required_text> <forbidden_text>
```

For example:
```
bin/check.php https://example.com 200 "required" "failure"
```

The checker sends a request to `<url>` and checks:
- If the HTTP status code matches `<expected_http_code>`
- If the page contains `<required_text>`
- If `<forbidden_text>` is not present

The exit code is
- `1` if the status code doesn't match `<expected_http_code>`
- `2` if `<required_text>` is missing
- `3` if `<forbidden_text>` is found
- `4` if other runtime error occurs
