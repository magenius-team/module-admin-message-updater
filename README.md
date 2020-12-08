# Admin Message Updater Module for Magento 2

This module provides functionality for triggering admin message update from js

### Features

- Allow trigger admin message update

### Requirements

- Magento version:
  * 2.3.x
  * 2.4.x

### Installation guide

```shell script
composer require magenius/module-admin-message-updater
bin/magento se:up && bin/magento di:co
bin/magento c:f
```

### Usage

- Define dependency at js file
```js
define([
    'Magenius_AdminMessageUpdater/js/model/messages'
], function (messageUpdater) {
    
})
```
- Call reload method
```js
messageUpdater.reload();
```
