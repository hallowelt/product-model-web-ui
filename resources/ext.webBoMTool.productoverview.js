Ext.onReady( function(){
	Ext.Loader.setPath(
		'BS.WebBoMToolOverview',
		mw.config.get( 'wgScriptPath' ) + '/extensions/WebBoMTool' + '/resources/BS.WebBoMToolOverview'
	);
	Ext.create( 'BS.WebBoMToolOverview.TreePanel', {
		renderTo: 'bs-webbomtooloverview-grid'
	} );
} );