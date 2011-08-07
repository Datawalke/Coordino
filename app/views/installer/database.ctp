<p>Please enter your database details below:</p>
<form action="?" method="post">
	<table cellspacing="0"> 
		<tr>
			<td>Database Host:</td>
			<td><input type="text" name="data[Database][host]" value="localhost"/></td>
			<td>The location of your database. In most cases this will be <strong>localhost</strong></td>
		</tr>
		<tr>
			<td>Database Username:</td>
			<td><input type="text" name="data[Database][login]" value="coordino"/></td>
			<td>The username of the user that has access to the database.</td>
		</tr>
		<tr>
			<td>Database Password:</td>
			<td><input type="password" name="data[Database][password]"/></td>
			<td>The password for the database user.</td>
		</tr>
		<tr>
			<td>Database Name:</td>
			<td><input type="text" name="data[Database][database]" value="coordino"/></td>
			<td>The name of your database.</td>
		</tr>
	</table>
	<input type="submit" value="Create database.php"/>
</form>