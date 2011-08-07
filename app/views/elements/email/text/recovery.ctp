Hi <?=$user['User']['username'];?>,

You requested your password for your account.
We've assigned a new password which you can change along with your other user settings 
at http://<?=$_SERVER['SERVER_NAME'];?>/users/settings/<?=$user['User']['public_key'];?>

Your password is now: <?=$password;?>

Use the above URL to go to the settings page after logging in using this email address and password.

As always, thank you for using our site!