# Website directory script

This is a flat-file PHP script for managing a directory of user-submitted websites. There is manual member approval, update management, mature content filtering, a tag filter system, and optional automailing. There are quite a few anti-spam measures built in, but this is designed for my use case, which is a smaller website, and as such may not be as effective for websites that get a lot of traffic (and, by extension, a lot of bots). 

## Setup

- Edit `hasher.php` with your password, upload it to your website, and open it. Copy whatever it gives you, then delete the file from your server. Paste that string to `prefs.php`, taking care to escape the dollar signs ($) and remove any spaces. 
- Finish editing `prefs.php` with everything else.
- The only files you need to touch after that are in the folder  `templates`
    - `top.php` and `bottom.php` are the header and footer for the directory.
    - `member.php` is the template for displaying entries. The variables you have to work with are `$name`, `$url`, `$title`, `$country`, `$button`, `$mature`, and `$comment`. You can also use the `displayTags($tags)` function to show tags if you're using them.
    - Additionally, there's a file called `styles-to-copy.css` with CSS to make the built-in pagination work correctly, should you choose to use your own stylesheet. Feel free to edit as much as necessary.
- Upload everything in the folder to your website wherever you want your directory.
- CHMOD all the .csv files to 640 to avoid them being accessed by the public.
- Have fun!

## Troubleshooting

- If stuff doesn't look like it's working after you test your first member, download and inspect the csv files. Sometimes the first entry will get stuck to the end of the header line instead of being put onto its own line. Just hit enter, save, and upload again, and it'll be good to go for all subsequent entries. This *shouldn't* happpen, but you never know...
- Please let me know about any other wonk so I can fix it.