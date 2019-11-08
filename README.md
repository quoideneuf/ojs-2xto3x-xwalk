Usage:

```
git clone https://github.com/quoideneuf/ojs-2xto3x-xwalk
cd ojs-2xto3x-xwalk
composer update
php transform.php --xml 2xports/articles.xml --out 3xports/testing.xml --xsl transform.xsl --test
```

Or, if you don't use composer, you can just run a transform

```
git clone https://github.com/quoideneuf/ojs-2xto3x-xwalk
cd ojs-2xto3x-xwalk
php transform.php --xml 2xports/articles.xml --out 3xports/testing.xml --xsl transform.xsl
```
