function sure() {
	return confirm("Are you sure?");
}

function updateValue(id, newvalue) {
	document.getElementById(id).value=newvalue;
	return true;
}

function validatePassword() {

	//Validate Password
	//Good length (min 8 chars)
	var passlength = document.forms["register"]["password"].value.length;
	if(passlength < 8) {
		alert("Please enter password with minimal length of 8 characters!");
		return false;
	}
	//Passwords match?
	var pass = document.forms["register"]["password"].value;
	var pass2 = document.forms["register"]["password2"].value;
	if( pass != pass2 ) {
		alert("Passwords don't match!");
		return false;
	}

	return true;
}