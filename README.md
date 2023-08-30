# Cloudflare Stream

> Upload, embed, and manage Cloudflare Stream videos from your CP.

Demo and `.env` setup instructions here - be up and running in less than 60 seconds!

[Click to see short video on Loom](https://www.loom.com/share/30ee4efe1ab7436188411390c2fa3807?sid=9c3e3361-3519-48c9-9ec5-fd2a56cc19d6)

## THIS IS A WORKING ADDON, BUT BIG CHANGES AUG/SEPT 2023 MAY BREAK WITH UPDATES

Please try this out, but be watchful when you `composer update` till I say.

Drag-and-drop does not seem to work properly on my system, but "browse for files" does.

## Features

- Upload large video files directly to Cloudflare Stream over tus
- Resumable uploads pending testing*
- Uploads from Google Drive, OneDrive, Dropbox pending testing*
- Option to see all videos on account or only those uploaded by this site
- Edit and delete forthcoming
- Fieldset incoming

## How to Install

You can search for this addon in the `Tools > Addons` section of the Statamic control panel and click **install**, or run the following command from your project root:

``` bash
composer require jmalko/cf-stream
```

Set these in your `.env` file. The first two can be found on the Cloudflare Stream page, top right.  The API key will require you to make one on your Account page and give it Read/Write access to Cloudflare Stream

```bash
CF_STREAM_ACCOUNT_ID= 
CF_STREAM_SUBDOMAIN=
CF_STREAM_API_KEY=
CF_STREAM_CREATOR_ID=optional_name // this is so that you can silo multiple clients on one Cloudflare account - set this and they will only see their own uploads vs. all the uploads on the Cloudflare account
```

## How to Use

Once you install via composer and add the `.env` information, you will see a "Videos" item on your Control Panel nav.  If you Press the "Upload videos" button, you will see a modal that will allow you to select videos to upload from your computer or phone.  Hit upload, then refresh the page.

You should see your newly uploaded videos.  Below the "Watch preview" button next to each video is the video ID.  Copy this ID and paste it into this antlers tag anywhere you want to embed the video.  Be sure you have Parse Antlers switched on in your blueprint for the field your are embedding in.

```antlers
{{ cf_stream:embed id="the_id_you_copied" }}
```

Check your work and enjoy.  More features coming very soon!

-- Jonathan Malko
