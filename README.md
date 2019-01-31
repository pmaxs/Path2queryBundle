# Symfony Path2queryBundle

Symfony bundle adds query vars to Request from path.

Installation
------------

    composer require pmaxs/path2query-bundle

Usage
-----

Add param `__path2query__` with default value `1`.
Such routes are processed by this bundle in order to add query vars to Request from path.

Ex.:  


```php
class DefaultController extends AbstractController
{
    /**
     * @Route("/xxx", defaults={"__path2query__"=1})
     */
    public function indexAction(Request $request)
    {
        // for path /xxx/var1/val1/var2/val2
        $request->get('var1') // val1
        $request->get('var2') // val2
    }
}
```
