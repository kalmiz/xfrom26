function Xfrom26_Init_Game(window, $) {
	'use strict';

	var $submit = $("#submit"),
		$list = $("#list"),
		$word = $("#word"),
		len = parseInt($("#word").attr("maxlength"), 10),
		succesHandler = function (data) {
			var html = "";
			if (data.status === 1) {
				html = "<strong>Match!</strong>";
			} else if (data.status === 2) {
				html = "<strong>Wrong word!</strong>";
			} else {
				html = data.left + " <span>" + data.word + "</span> " + data.right;
			}
			$list.append("<li>" + html + "</li>");
		},
		submitHandler = function (ev) {
			var value = $word.val();
			if (value.length !== len) {
				window.alert("Please, choose a word which contains " + len + " letters");
			} else {
				$.getJSON("/game/check?word=" + window.escape(value), succesHandler);
			}
			return false;
		};

	$submit.on("click", submitHandler);
	$("#showme").on("click", function () {
		$("#current-word").removeClass("hidden");
		return false;
	});
	console.log("Done.");
}

// main entry point
function Xfrom26_Init(window, $) {
	'use strict';
	var app = null;
	if ($("#view-game").length) {
		app = new Xfrom26_Init_Game(window, $);
	}
}

