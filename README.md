# cat_url
ExpressionEngineV2 Return category name, id, url-title from a url segment or a query string

### Return category ID from category_url_title
Example: http://example.com/category-url-title
```
{exp:cat_url:category_id segment="2"}
```

### Return category Name from category_url_title
Example: http://example.com/category-url-title
```
{exp:cat_url:category_name segment="2"}
```

### Return category Name from query string
Example: http://example.com?cat=my-category
```
{exp:cat_url:query_string query="my-category"}
```
