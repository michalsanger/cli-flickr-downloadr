# Flickr Downloadr

Backup your Flickr with this console app

## Installation

[Get the latest package](https://github.com/michalsanger/cli-flickr-downloadr/releases) and save it into the ```$PATH```:

    wget https://github.com/michalsanger/cli-flickr-downloadr/releases/download/v0.5.1/flickr_downloadr.phar -O flickr_downloadr.phar
    chmod u+x flickr_downloadr.phar
    mv flickr_downloadr.phar /usr/local/bin/flickr_downloadr

## Usage
Run just the app ```flickr_downloadr``` to get intro help:

![Help screen](https://farm8.staticflickr.com/7540/15684400780_71c8f45300_o.png)

Use ```--help``` to get info about commands:

```flickr_downloadr photoset:list --help```

You don't have to write full command, it's enough if the prefix match one command. 
```flickr_downloadr photoset:download```, ```flickr_downloadr photoset:d``` or 
```flickr_downloadr p:d``` are all the same.

### Authorize
At first, you have to allow access to your photos for this app:

```flickr_downloadr authorize```

There is a [step by step tutorial with screenshots](https://github.com/michalsanger/cli-flickr-downloadr/wiki/Authorization)

### Photosets list
Get a list of your albums:

```flickr_downloadr photoset:list```

![List photosets](https://farm8.staticflickr.com/7548/15685714469_0993a160dd_o.png)

### Download a photoset
Use the photoset ID as argument for ```photoset:download``` command:

```flickr_downloadr photoset:download 72157647129250803```

![Download photoset](https://farm8.staticflickr.com/7474/15684400790_fc011fb7bb_o.png)
