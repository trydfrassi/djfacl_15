	//--------------------------------
	// Save functions
	//--------------------------------
	var ajaxObjects = new Array();

	// Use something like this if you want to save data by Ajax.
	function saveMyTree()
	{
			saveString = treeObj.getNodeOrders();
			var ajaxIndex = ajaxObjects.length;
			ajaxObjects[ajaxIndex] = new sack();
			var url = 'saveNodes.php?saveString=' + saveString;
			ajaxObjects[ajaxIndex].requestFile = url;	// Specifying which file to get
			ajaxObjects[ajaxIndex].onCompletion = function() { saveComplete(ajaxIndex); } ;	// Specify function that will be executed after file has been found
			ajaxObjects[ajaxIndex].runAJAX();		// Execute AJAX function

	}
	function saveComplete(index)
	{
		alert(ajaxObjects[index].response);
	}


	// Call this function if you want to save it by a form.
	function saveMyTree_byForm()
	{
		document.myForm.elements['saveString'].value = treeObj.getNodeOrders();
		document.myForm.submit();
	}

