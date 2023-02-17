# TYPO3 Extension `iframecache`

![Build Status](https://github.com/jweiland-net/iframecache/workflows/CI/badge.svg)

Because of GDPR it is not allowed to initiate requests to third-party
websites without confirmation of the website visitor.

With `iframecache` you can create an iframe box and the webserver gets
the content of the third-party content and tries to download and replace all
foreign links to local links.

## 1 Features

* Create iframe where all links have been replaced with local links

## 2 Usage

### 2.1 Installation

#### Installation using Composer

The recommended way to install the extension is using Composer.

Run the following command within your Composer based TYPO3 project:

```
composer require jweiland/iframecache
```

#### Installation as extension from TYPO3 Extension Repository (TER)

Download and install `iframecache` with the extension manager module.

### 2.2 Minimal setup

1) Include the static TypoScript of the extension.
2) Insert new content element of type `Iframecache`
3) Fill up the fields to your need
