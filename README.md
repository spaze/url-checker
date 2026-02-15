# UrlChecker

A command line tool to check URL HTTP code and contents.

## Install
```
composer create-project --no-dev spaze/url-checker url-checker
```

## Usage
```
check.php <url> <expected_http_code> <required_text> <forbidden_text> [OPTIONS]
```

### Options
- `--ignore-case-required-text`: Ignore case when looking for the required text. Only ASCII case folding will be done, non-ASCII bytes will be compared by their byte value.
- `--ignore-case-forbidden-text`: Ignore case when looking for the forbidden text. Only ASCII case folding will be done, non-ASCII bytes will be compared by their byte value.

The checker sends a request to `<url>` and checks:
- If the HTTP status code matches `<expected_http_code>`
- If the page contains `<required_text>`
- If `<forbidden_text>` is not present

## Example
```
bin/check.php https://example.com 200 "required" "FAiLuRe" --ignore-case-forbidden-text
```

## Exit codes
The exit code is
- `1` if the status code doesn't match `<expected_http_code>`
- `2` if `<required_text>` is missing
- `3` if `<forbidden_text>` is found
- `4` if other runtime error occurs
