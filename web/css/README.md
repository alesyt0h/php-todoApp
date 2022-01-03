Place your stylesheets in this folder.

Use the `appendCSS` helper function found in the /lib/base/View.php file to append a CSS file to the app.

### Example of usage

**HTML** - In the respective layout file

```phtml
<html lang="en">
<head>
    <?php $this->appendCSS('global.css'); ?
</head>
```

**PHP** - In the respective controller file:

```php
public function indexAction(){
  $this->view->appendCSS('style.css');
}
```


As you can see, the helper function `appendCSS` needs to be called, with the stylesheet you want to load as parameter.