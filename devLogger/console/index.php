<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>

<head>
	<title>Determine if the agent is a person or random answering bot</title>


	<!-- Libraries -->
	<!--script src="http://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.8.1.min.js" type="text/javascript"></script-->
	<script src="../common_scripts/jquery-1.8.1.min.js" type="text/javascript"></script>
	<script src="../common_scripts/gup.js" type="text/javascript"></script>

	<!-- Chat Scripts -->
	<script src="scripts/chat.js" type="text/javascript"></script>
	<script src="scripts/alert.js" type="text/javascript"></script>

	<!-- Style -->
	<link rel="stylesheet" type="text/css" href="css/chat.css"></link>

</head>

<body>
	<div id="chat_outer-container">

		<!--div id="instructions-container">
			<div id="instructions-header">
				Instructions:
			</div>

			<div id="instructions-emph">
                                <ul>
                                  <li>Ask the agent for anything you'd like it to do. The agent will remember questions you've asked so that it can learn later!</li>
                                </ul>
			</div>
		</div-->

		<div id="chat_container">
			<div id="chat_header">
				Listener Agent
			</div>

			<ul id="chat_area"></ul>

			<form style="background: #eee; border-radius: 0px 0px 6px 6px;">
				<textarea id="chat_box" cols="30" rows="5" class="chat_defaultText"
				title="Enter a question here"></textarea>
                <span id="chat_context"><b></b></span>
			</form>
		</div>
	</div>
</body>

</html>
