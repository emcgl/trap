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
	var passlength = document.getElementById('password').value.length;
	if(passlength < 8) {
		alert("Please enter password with minimal length of 8 characters!");
		return false;
	}
	//Passwords match?
	var pass = document.getElementById('password').value;
	var pass2 = document.getElementById('password2').value;
	if( pass != pass2 ) {
		alert("Passwords don't match!");
		return false;
	}

	return true;
}


//Dis/En-ables age file upload on predictor type
function needAgeFile() {
	value = document.getElementById("predictortype").value;
	
	if(value=="scaled") {
		document.getElementById("agefile").disabled=true;
		return true;
	} else	if(value=="general") {
		document.getElementById("agefile").disabled=false;
		return true;
	}
	
	alert("Unknown value!");
	return false;	
	
}