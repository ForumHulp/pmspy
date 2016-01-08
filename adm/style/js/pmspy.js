; (function ($, window, document) {
	// do stuff here and use $, window and document safely
	// https://www.phpbb.com/community/viewtopic.php?p=13589106#p13589106
	
		$("a.simpledialog").simpleDialog({
	    opacity: 0.1,
	    width: '650px',
		closeLabel: '&times;'
	});

})(jQuery, window, document);

function ShowHide(id)
{
	var obj = "";	

	// Check browser compatibility
	if(document.getElementById)
	{
		obj = document.getElementById(id);
	}
	else if(document.all)
	{
		obj = document.all[id];
	}
	else if(document.layers)
	{
		obj = document.layers[id];
	}
	else
	{
		return 1;
	}

	if (!obj) 
	{
		return 1;
	}
	else if (obj.style) 
	{			
		obj.style.display = ( obj.style.display != "none" ) ? "none" : "";
	}
	else 
	{ 
		obj.visibility = "show"; 
	}
}
