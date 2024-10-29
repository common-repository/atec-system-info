function phpinfo()
{
	const phpinfo=jQuery("#phpinfo"); 
	let html=phpinfo.html();
	html = html.replaceAll('&lt;', '<'); 
	html = html.replaceAll('&gt;', '>');
	html = html.replaceAll('&quot;', '"');
	html = html.replaceAll('&#039;', "'");
	html = html.replaceAll('&amp;', "&");
	phpinfo.html(html).show();
}

function formatHtaccess(id)
{
	const htaccess=jQuery("#"+id);
	if (htaccess)
	{
		let html=htaccess.html();
		html=html.replace(/#\s([^\n]*)\n?/gm, "<font color='#aaa'># $1</font>\n");
		html=html.replace(/Rewrite([^\n]*)\n?/gm, "&nbsp;&nbsp;<span class='dashicons dashicons-controls-forward'></span> Rewrite$1</font>\n");
		htaccess.html(html);
	}
};

function beautifyPhpinfo()
{
	const phpinfo=jQuery("#phpinfo"); 
	let html=phpinfo.html();
	html = html.replaceAll('&lt;', '<'); 
	html = html.replaceAll('&gt;', '>');
	html = html.replaceAll('&quot;', '"');
	html = html.replaceAll('&#039;', "'");
	html = html.replaceAll('&amp;', "&");
	
	html = html.replaceAll('%20', " ");
	html = html.replaceAll('%7C', "| ");
	html = html.replaceAll('%3D', "=");
	html = html.replaceAll('|', "| ");
	html = html.replaceAll('%26', "&<br>");
	phpinfo.html(html).show();
}