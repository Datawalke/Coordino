<p>Congratulations! You are almost ready to go. But before you can start using Coordino please make an admin account below:</p>
<form action="?" method="post">
	<table cellspacing="0"> 
		<tr>
			<td>Your Name:</td>
			<td><input type="text" name="data[User][username]"/></td>
			<td>Your name/username.</td>
		</tr>
		<tr>
			<td>Email:</td>
			<td><input type="text" name="data[User][email]"/></td>
			<td>Your email address.</td>
		</tr>
		<tr>
			<td>Password:</td>
			<td><input type="password" name="data[User][password]"/></td>
			<td>Your super secret password.</td>
		</tr>
	</table>
	<input type="submit" value="Create Your Account"/>
	<strong>Remember to set <span class="highlight">/app/config</span> back to read-only file permissions after
		this step!</strong>
</form>