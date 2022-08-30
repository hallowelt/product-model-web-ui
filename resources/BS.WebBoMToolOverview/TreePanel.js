Ext.define( "BS.WebBoMToolOverview.TreePanel", {
	extend: 'BS.CRUDPanel',
	requires: [
		'BS.BlueSpiceCategoryManager.Model',
		'BS.dialog.BatchActions',
		'Ext.data.TreeStore'
	],
	originalParent: undefined,
	afterInitComponent: function () {
		this.store = new Ext.data.TreeStore({
			proxy: {
				type: 'ajax',
				url: mw.util.wikiScript( 'api' ),
				reader: {
					type: 'json',
					rootProperty: 'results',
					totalProperty: 'total'
				},
				extraParams: {
					action: 'bs-webbomtool-product-store',
					format: 'json'
				}
			},
			root: {
				text: 'Products',
				id: 'src',
				expanded: true
			},
			model: 'BS.BlueSpiceCategoryManager.Model'
		});

		var me = this;

		this.treePanel = new Ext.tree.Panel({
			useArrows: true,
			height: 500,
			rootVisible: false,
			displayField: 'text',
			store: this.store,
			rowLines: true,
			viewConfig: {
				stripeRows : true
			},
			columns : [{
				xtype: 'treecolumn', //this is so we know which column will show the tree
				flex: 2,
				dataIndex: 'text',
				sortable: false
			},{
				header: mw.message('bs-webbomtool-version-column-header').plain(),
				flex: 2,
				dataIndex: 'version',
				sortable: false
			},{
				header: mw.message('bs-webbomtool-license-column-header').plain(),
				flex: 2,
				dataIndex: 'license',
				sortable: false
			},{
				header: mw.message('bs-webbomtool-compatibility-column-header').plain(),
				flex: 2,
				dataIndex: 'compatible',
				sortable: false,
				renderer: this.renderIcon
			},
			new Ext.grid.column.Action({
				header: mw.message('bs-extjs-actions-column-header').plain(),
				flex: 0,
				items: [
					{
						tooltip: mw.message('bs-extjs-delete').plain(),
						iconCls: 'bs-extjs-actioncolumn-icon bs-icon-cross destructive',
						glyph: true,
						handler: function( object, index, col, object2, object3, store) {
							me.onBtnRemoveClick( me.btnRemove, store, null);
						},
						isDisabled: function( view, rowIndex, colIndex, item, record ) {
							return true; //record.get( 'leaf' ) === true ? true : false;
						}
					}, {
						tooltip: mw.message('bs-categorymanager-action-show-category').plain(),
						iconCls: 'bs-extjs-actioncolumn-icon bs-icon-eye',
						dataIndex: 'link',
						glyph: true,
						handler: this.onEyeClick
					}
				],
				menuDisabled: true,
				hideable: false,
				sortable: false
			})
			]
		} );

		//this.treePanel.expandAll();
		this.treePanel.getView().on( 'itemclick', this.onItemclick, this );

		this.items = [
			this.treePanel
		];

		this.callParent();
	},
	onEyeClick: function() {
		bs.api.tasks.exec(
			'webbomtool-product',
			'startScan'
		).done( function( result ) {
			bs.util.alert( 'bs-webbomtool-product-tasks-success', {
					title: 'Task executed successfully',
					text: result.message
				}
			);
		});
	},
	onItemclick: function ( obj, record, item, index, e, eOpts ) {
		this.btnRemove.enable();
		this.btnRemove.element = record;
	},
	addCategories: function( page, categories ) {
		console.log( 'not implemented' );
		return;
		this.treePanel.setLoading( true );

		return new BS.action.APIAddCategories({
			pageTitle: page,
			categories: categories
		}).execute();
	},

	onBtnAddClick: function ( oButton, oEvent ) {
		bs.util.prompt(
			"bs-webbomtool-add-repository",
			{
				titleMsg: "bs-webbomtool-add-repository-title",
				textMsg: 'bs-webbomtool-add-repository-prompt'
			},
			{
				ok: function( input ) {
					console.log( input );
					bs.api.tasks.exec(
						'webbomtool-product',
						'initialise',
						{
							url: input.value
						}
					).done( function( result ) {
						bs.util.alert( 'bs-webbomtool-product-tasks-success', {
								title: 'Task executed successfully',
								text: result.message
							}
						);
					});
				}
			}
		);

		return;
	},
	onBtnRemoveClick: function ( oButton, oStore, oEvent ) {
		console.log( oButton.element.data.bomId );
		bs.api.tasks.exec(
			'webbomtool-product',
			'delete',
			{
				bomId: oButton.element.data.bomId
			}
		).done( function( result ) {
			bs.util.alert( 'bs-webbomtool-product-tasks-success', {
					title: 'Task executed successfully',
					text: result.message
				}
			);
		});
		return;
	},

	renderIcon: function( value, meta ) {
		meta.tdCls = "ns-manager-icon";
		var icon = '<img src="' + mw.config.get( "wgScriptPath" ) + '/extensions/BlueSpiceFoundation/resources/bluespice/images/bs-{0}.png" alt="Icon {0}"/>';

		if ( value === false ) {
			return icon.format( 'cross' );
		}
		return icon.format( 'tick');
	},

	opPermitted: function( operation ) {
		//Edit functionality is not yet implemented. For the time being this
		//is the simplest way to hide the "edit" button from the UI.
		if( operation === 'update' ) {
			return false;
		}
		return this.callParent( arguments );
	}

} );
