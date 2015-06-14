var Alert = {

// State
isActive: true,

init: function() {
	//
	$(window).focus(function() {
		if( !Alert.isActive ) {
			Alert.isActive = true
		}
	});

	$(window).blur(function() {
		if( Alert.isActive ) {
			Alert.isActive = false
		}
	});
}

}

$(document).ready( function() {
    Alert.init()
});
