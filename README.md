# GitLab to Microsoft Teams Gateway written in PHP

This is a simple php script to send notifications from GitLab to Microsoft Teams channels.
Just use it as a webhook endpoint in GitLab, and all notifications get posted directly to Microsoft Teams.

This is a very early release and work in progress - not all triggers are implemented yet and the code needs some cleanup.

## Usage

Just create an [incoming webhook](https://msdn.microsoft.com/en-us/microsoft-teams/connectors#create-the-webhook) for your channel in MS Teams.

I call this the webhook url and it has the form https://outlook.office.com/webhook/xxxxxx...

In GitLab you go to your Project's Settings->Integration Tab and paste the webhook url in the input field.
Now just replace https://outlook.office.com with your own server URL. It should look like this:

    https://teamstest.simplethings.de/webhook/xxxxxx...

If you don't like the uri-magic I do in the script you can hand the webhook url over as a parameter as well:
    https://teamstest.simplethings.de/index.php?url=https://outlook.office.com/webhook/xxxxxx...

Click _Add Webhook_ and use the _Test_-Button to try it out. GitLab should report a 200 return code and you should see a new message in your Teams channel.

## Test-Gateway

If you want to do a quick test feel free to use the installation we set up under 

    https://teamstest.simplethings.de
    
But remember that your GitLab-Notifies may contain confidential information! Of course we don't save anything - but use this for testing only.

## Installation

Just clone the repository

    git clone https://github.com/simplethings/gitlab-msteams.git
    
Rename config.template.php to config.php and customize it if needed (usually works out of the box).

Configure your webserver to get web access to the index.php. That's it.
Try the URL in your Browser - you should get a nice 500-Error with _Microsoft Teams Gateway - no input_

## Configuration Options

### Debug mode

You can enable Debug-Mode in the config. All json input is saved and each message gets two extra Actions to view the original json data or to resend it. This way you can easily adapt the code and try the same message again without triggering the event a second time in gitlab. Just try it out!

### CLI-Mode

You can call the script from command line as well. Paramter is a jsonfile in your jsondir (in debug mode all requests get saved there)

### Quick&Dirty mode

If you like it quick&dirty you can just use the orginal webhook url in gitlab as well. This only makes sense if you have lots of projects with different channels.

All you have to do is to use /etc/hosts or iptables or whatever works for you to redirect all requests to https://outlook.office.com to your own webserver and disable SSL verification in GitLab.

**If you don't know how to do this - just don't do it and use the standard method :-)**


