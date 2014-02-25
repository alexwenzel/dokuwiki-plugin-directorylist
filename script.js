/* DOKUWIKI:include js/jquery.tinysort.min.js */

jQuery(function()
{
	jQuery('ul.directorylist li').tsort('a', {
		order:'asc'
	});

	jQuery('ul.directorylist li.menu').on('click', function()
	{
		jQuery('ul.directorylist li').tsort('a', {
			order:'desc'
		});

		console.log('click');
	});
});