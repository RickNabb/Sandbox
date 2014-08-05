function updateHeader(step){
	if(step == "login"){
		$("#hdr_step1").addClass("current");
	}
	else if(step == "schedule"){
		$("#hdr_step2").addClass("current");
	}
	else if(step == "payment"){
		$("#hdr_step3").addClass("current");
	}
}

function nextPage(loc){

	if($("#loaderDiv")){
		$("#loaderDiv").fadeIn();
	}
		
	if(loc.pathname.substring(9).indexOf("schedule.php") != -1){
		var titleString = $("div[id*='_price'][style!='display: none;'] p").text();
		var title = titleString.substring(0, titleString.indexOf(" -"));
		var curId = $("div[id*='event_'][class*='eventSelected']")[0].id.substring(6);

		if($("#event_" + curId).attr("featured") == "true"){

			var input_data = {
				"title": title,
				"location": "The Sandbox Playground",
				"featured": "true",
				"startDateTime": $("#event_" + curId).attr("startDateTime"),
				"endDateTime": $("#event_" + curId).attr("endDateTime"),
				"order_price": $("#" + curId + "_price_val").attr("price"),
				"order_desc": $("#" + curId + "_desc_val").attr("desc")
			};

			// Going to payment for the FIRST time
			
			input_data["method"] = "addFeaturedAppt";
			$.post("./pay_app/common/appointment.php",
				input_data, 
				function(data, status){
					window.location = "http://localhost/Sandbox/payment.php";
				}
			);		
		}
		else{

			var input_data = {
					"title": title,
					"location": "The Sandbox Playground",
					"startDateDay": $("#startDateDay").val(),
					"startDateMonth": $("#startDateMonth").val(),
					"startDateYear": $("#startDateYear").val(),
					"endDateDay": $("#endDateDay").val(),
					"endDateMonth": $("#endDateMonth").val(),
					"endDateYear": $("#endDateYear").val(),
					"startTimeHour": $("#startTimeHour").val(),
					"startTimeMinute": $("#startTimeMinute").val(),
					"startTimeAMPM": $("#startTimeAMPM").val(),
					"endTimeHour": $("#endTimeHour").val(),
					"endTimeMinute": $("#endTimeMinute").val(),
					"endTimeAMPM": $("#endTimeAMPM").val(),
					"order_price": $("#" + curId + "_price_val").attr("price"),
					"order_desc": $("#" + curId + "_desc_val").attr("desc") };

			var attendants = $("input[id*='attendant']");
			for(var i = 0; i < attendants.length; i++){
				input_data['attendant' + i] = attendants[i].value;
			}

			if(localStorage){
				localStorage.setItem("eventSelected", curId);
				localStorage.setItem("eventTitle", title);
				localStorage.setItem("startDateDay", $("#startDateDay").val());
				localStorage.setItem("startDateMonth", $("#startDateMonth").val());
				localStorage.setItem("startDateYear", $("#startDateYear").val());
				localStorage.setItem("startTimeHour", $("#startTimeHour").val());
				localStorage.setItem("startTimeMinute", $("#startTimeMinute").val());
				localStorage.setItem("startTimeAMPM", $("#startTimeAMPM").val());
				localStorage.setItem("numAttendants", attendants.length);

				for(var i = 0; i < attendants.length; i++){
					localStorage.setItem('attendant' + i, attendants[i].value);
				}
			}

			// Going to payment after going back to scheduling
			if(location.search.indexOf("method=update") != -1){
				input_data["method"] = "updateAppt";
				$.post("./pay_app/common/appointment.php",
					input_data, 
					function(data, status){
						if(data == "1" || data == "0"){
							window.location = "http://localhost/Sandbox/payment.php";
						}
						else{
							// TODO: Add logic for updating error & eliminate need for query string vars
						}
					}
				);	
			}
			// Going to payment for the FIRST time
			else{
				input_data["method"] = "addAppt";
				$.post("./pay_app/common/appointment.php",
					input_data, 
					function(data, status){
						window.location = "http://localhost/Sandbox/payment.php";
					}
				);
			}
		}
	}
	// Going to scheduling from login
	else if(loc.pathname == "/Sandbox/"){
		window.location = "http://localhost/Sandbox/schedule.php";
	}
	// Payment
	else if(loc.pathname == "/Sandbox/payment.php"){

		$.post("./pay_app/order/order_place.php",
			function(data, status){
				window.location = data;
			});
	}
}

function lastPage(loc){
	if(loc.pathname.substring(9).indexOf("payment.php") != -1){
		window.location = "./schedule.php?method=update";
	}
}

function cancelEvent(){

	var input_data = {
		"method": "removeAppt"
	};

	localStorage.clear();
	if($("#cancelModal div[class*='modal-body'] p.error")){
		$("#cancelModal div[class*='modal-body'] p.error").remove();
	}
	$.post("./pay_app/common/appointment.php",
		input_data,
		function(data, status){
			if(data == "1"){
				window.location = "http://localhost/Sandbox/";
			}
			else if(data == "" || data == "0"){
				$("#cancelModal div[class*='modal-body']").append("<p class='error'>" + 
					"There was an error cancelling your event. Please try again soon.</p>");
			}
		}
	);
}

/// LOGIN METHODS

function validateLogin(){
	var usernameTxt = $("#username").val();
	var passwordTxt = $("#password").val();

	$.ajax({
		type: "GET",
		url: "./validateLogin.php?email=" + usernameTxt + "&password=" + passwordTxt,
		success: function(result){
			if(result == "true"){
				nextPage(window.location);
			}
			else{
				$("#validateText").css("display", "block");
			}
		},
		error: function(){
			$("#validateText").css("display", "block");
		}
	});
}

function validateCreateActUsername(){
	if($("#createActUsername").val() != ""){

		if(!validateEmail($("#createActUsername").val())){
			$("#createActUsernameValidate").show();
		}
		else{
			$("#createActUsernameValidate").hide();
		}
	}
}

function validateGuestEmail(){
	if($("#guestEmail").val() != ""){

		if(!validateEmail($("#guestEmail").val())){
			$("#guestEmailValidate").show();
		}
		else{
			$("#guestEmailValidate").hide();
		}
	}
}

function validateCreateActPassMatch(){
	if($("#createActPwdConfirm").val() != ""){

		var pass = $("#createActPwd").val();
		var confirm = $("#createActPwdConfirm").val();

		if(pass !== confirm){
			$("#createActPwdValidate").show();
		}
		else{
			$("#createActPwdValidate").hide();
		}
	}
}

function createAccount(){

	if($("#createActPwdValidate").css("display") == "none"
		&& $("#createActUsernameValidate").css("display") == "none"
		&& $("#createActUsername").val() != ""
		&& $("#createActPwd").val() != ""
		&& $("#createActPwdConfirm").val() != ""){

		var email = $("#createActUsername").val();
		var input_data = {
			email: email,
			password: $("#createActPwd").val(),
			method: "addUser"
		};

		if($("#createAccountModal div[class*='modal-body'] p.error")){
			$("#createAccountModal div[class*='modal-body'] p.error").remove();
		}

		$.post("./pay_app/common/user.php",
			input_data,
			function(data, status){
				if(data === "success"){
					var modalBody = $("#createAccountModal div[class*='modal-body']");
					var modalBtn = $("#createAccountModal div[class*='modal-footer'] input");

					modalBody.empty();
					modalBody.append("<p>Thank you for creating an account!</p>");
					modalBody.append("<p>You should recieve a confirmation email at <strong>"
						+ email + "</strong> soon. Follow its instructions to confirm your account.</p><br /><br />");
					modalBtn.val("Close");
					modalBtn.css("width", "80px");
					modalBtn.attr("data-dismiss", "modal");
					modalBtn.attr("onclick", "window.location = 'localhost/Sandbox';");
					$("#createAccountModal div.modal-header button").remove();					
				}
				else if(data === "existing"){
					$("#createAccountModal div[class*='modal-body']").append("<p class='error'>" + 
						"There is already an account under that email address. Please enter a new"
						+ " email address and try again.</p>");
				}
				else if(data === "failure"){
					$("#createAccountModal div[class*='modal-body']").append("<p class='error'>" + 
						"There was an error adding you to our database. Please try again soon.</p>");
				}
				else{
					$test = "test";
				}
			}
		);
	}
}

function submitGuestEmail(){
	if($("#guestEmailValidate").css("display") == "none"
		&& $("#guestEmail").val != ""){

		var email = $("#guestEmail").val();
		var input_data = {
			"guest_email": $("#guestEmail").val()
		};

		$.post('./validateLogin.php',
			input_data,
			function(data, success){
				if(data == "guest_account"){
					nextPage(window.location);
				}
				else{

				}
		});
	}
}

function validateEmail(email) { 
	var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	return re.test(email);
}


/// SCHEDULING METHODS

function addAttendant(){
	var div = document.getElementById("attendants");
	var count = 0;
	for(var i = 0; i < div.children.length; i++){
		var child = div.children[i];
		if(child.id.indexOf('attendant') > -1){
			count++;
		}
	}
	count++;
	var newInput = document.createElement("input");
	newInput.id = 'attendant' + count;
	newInput.name = 'attendant' + count;
	newInput.type = "text";
	newInput.setAttribute("placeholder", "Attendant email...");
	div.appendChild(document.createElement("br"));
	div.appendChild(newInput);
}

function changeDays(sender){
	var month = $(sender).val();

	$("#startDateDay").empty();

	if(month == "September" || 
		month == "April" ||
		month == "June" ||
		month == "November"){
		for(var i = 1; i <= 30; i++){
			if(i < 10){
				$("#startDateDay").append("<option>0" + i + "</option>");
			}
			else{
				$("#startDateDay").append("<option>" + i + "</option>");
			}
		}
	}
	else if(month == "February"){
		if(parseInt($("#startDateYear").val()) % 4 == 0){
			for(var i = 1; i <= 29; i++){
				if(i < 10){
					$("#startDateDay").append("<option>0" + i + "</option>");
				}
				else{
					$("#startDateDay").append("<option>" + i + "</option>");
				}
			}
		}
		else{
			for(var i = 1; i <= 28; i++){
				if(i < 10){
					$("#startDateDay").append("<option>0" + i + "</option>");
				}
				else{
					$("#startDateDay").append("<option>" + i + "</option>");
				}
			}
		}
	}
	else{
		for(var i = 1; i <= 31; i++){
			if(i < 10){
				$("#startDateDay").append("<option>0" + i + "</option>");
			}
			else{
				$("#startDateDay").append("<option>" + i + "</option>");
			}
		}
	}
}

function changeEndDayTime(sender){
	
	$("#endTimeHour").empty();
	$("#endTimeMinute").empty();
	$("#endTimeAMPM").empty();	
	$("#endDateDay").empty();
	$("#endDateMonth").empty();
	$("#endDateYear").empty();
	$("#endTimeHour").append("<option>" + $("#startTimeHour").val() + "</option>");
	$("#endTimeMinute").append("<option>" + $("#startTimeMinute").val() + "</option>");
	$("#endTimeAMPM").append("<option>" + $("#startTimeAMPM").val() + "</option>");
	$("#endDateDay").append("<option>" + $("#startDateDay").val() + "</option>");
	$("#endDateMonth").append("<option>" + $("#startDateMonth").val() + "</option>");
	$("#endDateYear").append("<option>" + $("#startDateYear").val() + "</option>");

	if(sender.id == "startTimeHour" || sender.id == "startTimeMinute" || sender.id == "startTimeAMPM"){

		var curId = $("div[id*='event_'][class*='eventSelected']")[0].id.substring(6);
		var curDuration = $("#" + curId + "_duration_val").attr("duration");

		if(curDuration % 1 == 0){
			var newTime = parseInt($("#endTimeHour").val()) + parseInt(curDuration);
		}
		else{
			var newTime = parseInt($("#endTimeHour").val()) + parseInt(Math.floor(curDuration));
			var newMins = parseInt($("#endTimeMinute").val()) + parseInt((curDuration%1)*60);

			if(newMins >= 60){
				newTime++;
				newMins %= 60;	
			}

			$("#endTimeMinute").empty();
			$("#endTimeMinute").append("<option>" + (newMins == "0" ? "00" : newMins) + "</option>");
		}

		if(newTime < 12){
			$("#endTimeHour").empty();
			$("#endTimeHour").append("<option>" + newTime + "</option>");
		}
		else{
			if($("#endTimeAMPM").val() == "AM" && $("#endTimeHour").val() != "12"){
				$("#endTimeAMPM").empty();
				$("#endTimeAMPM").append("<option>PM</option>");
				$("#endTimeHour").empty();
				$("#endTimeHour").append("<option>" + (newTime == "12" ? "12" : newTime%12) + "</option>");
			}
			else if($("#endTimeAMPM").val() == "PM" && $("#endTimeHour").val() != "12"){
				if($("#endTimeAMPM").val() == "PM"){
					var curDay = $("#startDateDay").val();
					$("#endDateDay").empty();
					$("#endDateDay").append("<option>" + (parseInt(curDay)+1) + "</option>");
				}
				
				$("#endTimeAMPM").empty();
				$("#endTimeAMPM").append("<option>AM</option>");
				$("#endTimeHour").empty();
				$("#endTimeHour").append("<option>" + (newTime == "12" ? "12" : newTime%12) + "</option>");
			}
			else if($("#endTimeHour").val() == "12"){				
				$("#endTimeHour").empty();
				$("#endTimeHour").append("<option>" + (newTime == "12" ? "12" : newTime%12) + "</option>");
			}
			else{
				$("#endTimeHour").empty();
				$("#endTimeHour").append("<option>" + newTime + "</option>");
			}
		}
	}
}

function changeEvent(sender, eventId){

	if($(sender).attr('class') != 'span2 eventItem eventSelected'){
		$("div[id*='event_'").removeClass("eventSelected");
		$("div[id*='event_'").removeClass("eventHover");
		$("div[id*='event_'").addClass("eventHover");

		$("div[id*='_price']").fadeOut();
		$("div[id*='_desc']").fadeOut();

		$(sender).addClass("eventSelected");
		$(sender).removeClass("eventHover");

		$("#" + eventId + "_price").fadeIn();
		$("#" + eventId + "_desc").fadeIn();
	}
}

function fillEventInfo(){

	$("#startDateMonth option:contains('" + localStorage.getItem("startDateMonth") + "')").attr("selected", "");
	$("#startDateDay option:contains('" + localStorage.getItem("startDateDay") + "')").attr("selected", "");
	$("#startDateYear option:contains('" + localStorage.getItem("startDateYear") + "')").attr("selected", "");
	$("#startTimeHour option:contains('" + localStorage.getItem("startTimeHour") + "')").attr("selected", "");
	$("#startTimeMinute option:contains('" + localStorage.getItem("startTimeMinute") + "')").attr("selected", "");
	$("#startTimeAMPM option:contains('" + localStorage.getItem("startTimeAMPM") + "')").attr("selected", "");

	for(var i = 0; i < Number(localStorage.getItem("numAttendants")); i++){
		if(i == 0){
			$("#attendant1").val(localStorage.getItem("attendant0"));
		}
		else{
			addAttendant();
			$("#attendant" + i).val(localStorage.getItem("attendant" + i));
		}
	}

	changeEvent($("div[id*='" + localStorage.getItem("eventSelected") + "']")[0], localStorage.getItem("eventSelected"));
	changeEndDayTime($("#startTimeMinute")[0]);
}

function createAppointment(appt_id){

	var input_data = {"appt_id": appt_id};

	$.post('../../createApt.php',
		input_data,
		function(data, success){
			if(data == "refreshed"){

				$.post('../../createApt.php',
					input_data,
					function(data, success){

						if(data != "success"){
							// TODO: Add column to events signifying "attention required"
							input_data = {"fileAddress": "./logs/general/log.txt",
											"message": "Google Calendar Error - Could not create " + 
											"appointment " + appt_id}
							$.post('./errorLogger.php',
								input_data,
								function result(data, success){

									if(data == "success"){
										// Error Logged
									}
									else{
										// TODO: Handle somhow...
									}
								});
						}
					})
			}
			else if(data == "success"){

			}
		});
}

function showHideEventCreate(){

	if($("#createEvent").css("display") == "none"){

		$("#createEvent").slideDown();
		$("#eventSignup").slideUp();
		$("#eventSignupTitle").text("Create Your Own Event");
		$("#eventSignupTitle").css("color", "#000");
		$("#createEventTitle").text("(Sign up for an Event?)");
		$("#createEventTitle").css("color", "#2e8bcc");
	}
	else{
		$("#eventSignup").slideDown();
		$("#createEvent").slideUp();
		$("#createEventTitle").text("(Create Your Own Event?)");
		$("#createEventTitle").css("color", "#2e8bcc");
		$("#eventSignupTitle").text("Sign up for an Event");
		$("#eventSignupTitle").css("color", "#000");
	}
}

/// PAYMENT METHODS

function showHideCredit(sender){
	if($(sender).val() == 'PayPal'){
		$("#creditForm").slideUp();
		switchPayButton("next");
	}
	else{
		$("#creditForm").slideDown();
		switchPayButton("finish");
	}
}

function switchPayButton(btnName){
	if(btnName == "next"){
		$("#ftr_finish").hide();
		$("#ftr_next").show();
	}
	else if(btnName == "finish"){
		$("#ftr_finish").show();
		$("#ftr_next").hide();
	}
}

function saveCreditCard(){

}