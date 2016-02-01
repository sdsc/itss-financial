window.onload = function() { $(".children").hide(); };
	
/* Function name: showChildren
 * Parameters: number - the master ticket number
 * Description: Toggles the visibility of children tickets
 */
function showChildren(number) {
	$("#p" + number).toggle();
	if($("#p" + number).is(':visible')) {
		this.innerHtml = "Collapse";
	} else {
		this.innerHtml = "Expand";
	}
}

function expandAll() {
	$(".expand").click();
}