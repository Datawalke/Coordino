==================================
Requirements
==================================
All that is required is your basic LAMP/WAMP stack. 
However the best-case conditions:
	- PHP5+
	- MySQL 5.1+
	
==================================
How to Install Coordino
==================================
Unzip the Coordino package your root working www directory.
	ex: /var/www/htdocs/test.com
	
Then open your web browser and proceed to http://test.com to complete instillation.

==================================
Widget Tokens
==================================
Tokens may be used to render dynamic content to the user.
The following tokens are available: 
	The logged in user's username.       - [user.username]
	The logged in user's reputation.     - [user.reputation]
	The logged in user's age.            - [user.age]
	The logged in user's website.        - [user.website]
	The logged in user's information.    - [user.info]
	The logged in user's location.       - [user.location]
	The logged in user's answer count.   - [user.answer-count]
	The logged in user's comment count.  - [user.comment-count]
	The logged in user's question count. - [user.question-count]
	A link to the user's profile.        - [user.profile-link]
	
For example:
	Hello [user.username], Welcome to Coordino!
	Check out your profile at: [user.profile-link]
	Or answer some questions!
	
==================================
Remote Auth. Logins
==================================
Coordino works in two modes: 
	1.) An internal userbase. (Remote Auth Only "No")
	2.) Remote userbase. (Remote Auth Only "Yes")
These settings may be changed in the administration setting under "Admin" -> "Remote Settings"

The internal userbase is the standard setting for Coordino. New users will register either by asking a question,
answering a question or registering themselves. The users username, password, and email are kept internally.

However, if you have an external userbase already and do not wish to have all of your users re-register for a system you can use a form of automatic integration. You must create a script that first compiles a message and then forwards it to Coordino's Remote Login system based off of your current logged in user's details.

The following user details from your userbase are needed:
	-Username
	-Email Address
The following extra message details are needed:
	-Timestamp
	-Remote Auth Key (Found in Remote Settings)
	-Hash

Take the following example in PHP:
<?
/*
 * Remote authentication for PHP
 *
 * This is meant to be used as a template to base the integration with your application.  
 */
 
 /* The following values should comefrom your source of information */
$username = 'BillyRogan';
$email = 'rbillyscool@aol.com';

 /* Insert your Authentication key here */
$key = '98y94NIUfafnajskfn9823JNAIUz'; 

/* Build the Message */
$timestamp = time();
$message = $username . $email . $timestamp . $key;
$hash= md5($message);
/* Set the URL of your Answer Engine install and form the correct remote authentication URL. */
$url = 'http://your.domain.com/coordino_install/access/remote/' . $username . '/' . $email . '/' . $timestamp . '/' . $hash;
header('Location: ' . $url);
?>

The username and email address are pulled from your current userbase. 
Then a message is compiled with the User's username, email, a timestamp, and your remote auth key. That message is then md5'd into a check hash. A URL is then formed with the correct information and the remote logged in user is then forwarded to the Coordino Remote Access URL.

==================================
Contributors - Thank you!
==================================
* do9iigane
* cybervirax
* ultramundane
* datawalke
Don't see yourself Just edit the Readme!
