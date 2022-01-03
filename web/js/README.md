Place your JavaScript files in this folder.

Use the `appendScript` helper function found in the /lib/base/View.php file to append a JavaScript file to the app.

### Example of usage

**HTML** - In the respective layout file

```phtml
<html lang="en">
<head>
    <?php $this->appendScript('main.js'); ?
</head>
```

**PHP** - In the respective controller file:

```php
public function indexAction(){
  $this->view->appendScript('main.js');
}
```


As you can see, the helper function `appendScript` needs to be called, with the JavaScript file name you want to load as parameter.