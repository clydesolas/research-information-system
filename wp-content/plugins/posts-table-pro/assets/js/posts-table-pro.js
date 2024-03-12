
( function( $, window, document, undefined ) {
    "use strict";

    const blockConfig = {
        message: null,
        overlayCSS: {
            background: '#fff',
            opacity: 0.7
        }
    };

    function addRowAttributes( $row ) {
        return function( key, value ) {
            if ( 'class' === key ) {
                $row.addClass( value );
            } else {
                $row.attr( key, value );
            }
        };
    }

    function appendFilterItems( $select, items, depth ) {
        depth = ( typeof depth !== 'undefined' ) ? depth : 0;

        // Add each term to filter drop-down
        $.each( items, function( i, item ) {
            let name = item.name,
                value = 'slug' in item ? item.slug : name,
                pad = '';

            if ( depth ) {
                pad = Array( ( depth * 2 ) + 1 ).join( '\u00a0' ) + '\u2013\u00a0';
            }

            $select.append( '<option value="' + value + '">' + pad + name + '</option>' );

            if ( 'children' in item ) {
                appendFilterItems( $select, item.children, depth + 1 );
            }
        } );
    }

    function initMedia( $el ) {
        if ( !$el || !$el.length ) {
            return;
        }

        if ( typeof WPPlaylistView !== 'undefined' ) {
            // Initialise audio and video playlists
            $el.find( '.wp-playlist' ).filter( function() {
                return $( '.mejs-container', this ).length === 0; // exclude playlists already initialized
            } ).each( function() {
                return new WPPlaylistView( { el: this } );
            } );
        }

        if ( 'wp' in window && 'mediaelement' in window.wp ) {
            $( window.wp.mediaelement.initialize );
        }

        // Run fitVids to ensure videos in table have correct proportions
        if ( $.fn.fitVids ) {
            $el.fitVids();
        }
    }

    function initPhotoswipeForContent( $el ) {
        if ( typeof PhotoSwipe === 'undefined' || typeof PhotoSwipeUI_Default === 'undefined' ) {
            return;
        }

        $el
            .find( '.posts-table-gallery__image a' )
            .off( 'click.ptp' )
            .on( 'click.ptp', onOpenPhotoswipe );
    }

    /*
     * Removes items from the allItems list which aren't present in visibleItems. Used to restrict search filters options.
     */
    function removeHiddenItems( allItems, visibleItems ) {
        let item,
            result = JSON.parse( JSON.stringify( allItems ) ); // clone the terms array, so the original is unmodified.

        for ( let i = result.length - 1; i >= 0; i-- ) {
            item = result[i];

            if ( item.hasOwnProperty( 'children' ) ) {
                item.children = removeHiddenItems( item.children, visibleItems );

                if ( 0 === item.children.length ) {
                    // No children left, so delete property from term.
                    delete item.children;
                }
            }

            // Keep the term if it's found in requiredSlugs or it has children.
            if ( -1 === visibleItems.indexOf( item.slug ) && !item.hasOwnProperty( 'children' ) ) {
                result.splice( i, 1 );
            }
        }

        return result;
    }

    /*
     * A renderer for $.fn.DataTables.Responsive to display all visible content for a row when using modal responsive display.
     *
     * @see https://datatables.net/reference/option/responsive.details.renderer
     */
    function responsiveRendererAllVisible( options ) {
        options = $.extend( {
            tableClass: ''
        }, options );

        return function( api, rowIdx, columns ) {
            var data = $.map( columns, function( col ) {
                // Bail if column data is hidden.
                if ( !api.column( col.columnIndex ).visible() ) {
                    return '';
                }

                return '<tr data-dt-row="' + col.rowIndex + '" data-dt-column="' + col.columnIndex + '">' +
                    '<td>' + ( col.title ? col.title + ':' : '' ) + '</td> ' +
                    '<td>' + col.data + '</td>' +
                    '</tr>';
            } ).join( '' );

            let $table = $( '<table class="' + options.tableClass + ' dtr-details" width="100%"/>' ).append( data );

            // Initialise media for modal.
            initMedia( $table );

            // Prevent clicking on featured image in modal by removing the link.
            let $img = $table.find( '.posts-table-image-wrapper > a > img' );

            if ( $img.length ) {
                $img.parent().parent().prepend( $img );
                $img.siblings( 'a' ).remove();
            }

            return $table;
        };
    }

    function select2Enabled() {
        return ( 'select2' in $.fn ) && posts_table_params.enable_select2;
    }

    /******************************************
     * POSTSTABLE PROTOTYPE
     ******************************************/

    let PostsTable = function( $table ) {
        // Properties
        this.$table = $table;
        this.id = $table.attr( 'id' );
        this.dataTable = null;
        this.config = null;
        this.ajaxData = [];
        this.hasAdminBar = $( '#wpadminbar' ).length > 0;

        this.$filters = [];
        this.$tableWrapper = [];
        this.$pagination = [];
        this.$tableControls = [];

        // Register events
        $table
            .on( 'draw.dt', { table: this }, onDraw )
            .on( 'init.dt', { table: this }, onInit )
            .on( 'page.dt', { table: this }, onPage )
            .on( 'processing.dt', { table: this }, onProcessing )
            .on( 'responsive-display.dt', { table: this }, onResponsiveDisplay )
            .on( 'xhr.dt', { table: this }, onAjaxLoad );

        $( window ).on( 'load.ptp', { table: this }, onWindowLoad );

        // Show the table - loading class removed on init.dt
        $table.addClass( 'loading' ).css( 'visibility', 'visible' );
    };

    PostsTable.prototype.buildConfig = function() {
        let config = {
            retrieve: true, // so subsequent calls to DataTable() return the same API instance
            responsive: true,
            processing: true, // display 'processing' indicator when loading
            orderMulti: false, // disable ordering by multiple columns at once
            language: posts_table_params.language
        };

        // Get config for this table instance.
        let tableConfig = this.$table.data( 'config' );

        if ( tableConfig ) {
            // We need to do deep copy for the 'language' property to be merged correctly.
            config = $.extend( true, { }, config, tableConfig );
        }

        // If English language, replace 'posts' with 'post' when there's only 1 result.
        if ( -1 !== config.language.info.indexOf( posts_table_params.language.totalsPlural ) ) {
            config.infoCallback = function( settings, start, end, max, total, pre ) {
                if ( pre && 1 === total ) {
                    return pre.replace( posts_table_params.language.totalsPlural, posts_table_params.language.totalsSingle );
                }
                return pre;
            };
        }

        // Config for server side processing
        if ( config.serverSide && 'ajax_url' in posts_table_params ) {
            config.deferRender = true;
            config.ajax = {
                url: posts_table_params.ajax_url,
                type: 'POST',
                data: {
                    table_id: this.id,
                    action: posts_table_params.ajax_action,
                    _ajax_nonce: posts_table_params.ajax_nonce
                },
                xhrFields: {
                    withCredentials: true
                }
            };
        }

        // Set responsive display and renderer functions
        if ( 'responsive' in config && ( typeof config.responsive === 'object' ) && 'details' in config.responsive && 'display' in config.responsive.details ) {
            if ( 'child_row_visible' === config.responsive.details.display ) {
                config.responsive.details.display = $.fn.dataTable.Responsive.display.childRowImmediate;
                config.responsive.details.renderer = $.fn.dataTable.Responsive.renderer.listHidden();
            } else if ( 'modal' === config.responsive.details.display ) {
                config.responsive.details.display = $.fn.dataTable.Responsive.display.modal();
                config.responsive.details.renderer = responsiveRendererAllVisible( { tableClass: posts_table_params.table_class } );
            }
        }

        // Legacy config for language (we now use Gettext for translation).
        if ( 'lang_url' in posts_table_params ) {
            config.language = { url: posts_table_params.lang_url };
        }

        return config;
    };

    /*
     * Gets the current list of filter items for the $select based on the stored filter data. For standard loading, the list is then restricted
     * to just those items visible in the current table.
     */
    PostsTable.prototype.getCurrentFilterItems = function( $select ) {
        let table = this,
            filters = table.$table.data( 'filters' ),
            column = $select.data( 'column' );

        if ( !filters || !( column in filters ) ) {
            return null;
        }

        let terms = filters[column].terms;

        if ( !terms ) {
            return [];
        }

        if ( !table.config.serverSide ) {
            // For standard load, find all data items in search column so we can restrict filter to relevant data only.
            let searchData = table.getDataTable()
                .column( $select.data( 'searchColumn' ) + ':name', { search: 'applied' } )
                .data();

            if ( searchData.any() ) {
                let searchDataArray = searchData.join( ' ' ).split( ' ' );
                terms = removeHiddenItems( terms, searchDataArray );
            }
        }

        return terms;
    };

    PostsTable.prototype.getDataTable = function() {
        if ( !this.dataTable ) {
            this.init();
        }

        return this.dataTable;
    };

    PostsTable.prototype.init = function() {
        let table = this;

        table.$table.trigger( 'preInit.ptp', [table] );

        // Initialize DataTables instance.
        table.config = table.buildConfig();
        table.dataTable = table.$table.DataTable( table.config );

        return table;
    };

    PostsTable.prototype.initFilters = function() {
        let table = this,
            filtersData = table.$table.data( 'filters' );

        if ( !filtersData ) {
            return table;
        }

        let $filtersWrap = $( '<div class="posts-table-select-filters" id="' + table.id + '_select_filters" />' ),
            filtersAdded = 0;

        if ( posts_table_params.language.filterBy ) {
            $filtersWrap.append( '<label class="filter-label">' + posts_table_params.language.filterBy + '</label>' );
        }

        // Build the filters
        for ( let column in filtersData ) {
            // Create <select> for the filter.
            let selectAtts = {
                'name': 'ptp_filter_' + column,
                'data-tax': filtersData[column].taxonomy,
                'data-column': column,
                'data-search-column': column + '_hfilter',
                'aria-label': filtersData[column].heading,
                'data-placeholder': filtersData[column].heading
            };

            if ( filtersData[column].class ) {
                selectAtts['class'] = filtersData[column].class;
            }

            let $select = $( '<select/>' ).attr( selectAtts );

            table.refreshFilterItems( $select );

            // Don't add the filter if we have no items (length will be 1 because of default value).
            if ( $select.children().length <= 1 ) {
                continue;
            }

            // Append select to wrapper
            $select
                .on( 'change.ptp', { table: table }, onFilterChange )
                .appendTo( $filtersWrap );

            filtersAdded++;
        } // foreach filter

        // Add filters to table - before search box if present, otherwise as first element above table
        if ( filtersAdded > 0 ) {
            // Add filters to table
            let $searchBox = table.$tableControls.find( '.dataTables_filter' );

            if ( $searchBox.length ) {
                $filtersWrap.prependTo( $searchBox.closest( '.posts-table-controls' ) );
            } else {
                $filtersWrap.prependTo( table.$tableControls.filter( '.posts-table-above' ) );
            }
        }

        // Store filters here as we use this when searching columns.
        table.$filters = table.$tableControls.find( '.posts-table-select-filters select' );

        return table;
    };

    PostsTable.prototype.initPhotoswipe = function() {
        let table = this;

        if ( typeof PhotoSwipe === 'undefined' || typeof PhotoSwipeUI_Default === 'undefined' ) {
            return table;
        }

        table.$table.on( 'click.ptp', 'td.child .posts-table-gallery__image a', onOpenPhotoswipe );

        return table;
    };

    PostsTable.prototype.initResetButton = function() {
        let table = this;

        if ( !table.config.resetButton ) {
            return table;
        }

        let $resetButton =
            $( '<div class="posts-table-reset"><a class="reset" href="#">' + posts_table_params.language.resetButton + '</a></div>' )
            .on( 'click.ptp', 'a', { table: table }, onReset );

        // Append reset button
        let $firstChild = table.$tableControls.filter( '.posts-table-above' ).children( '.posts-table-select-filters, .dataTables_length, .dataTables_filter' ).eq( 0 );

        if ( $firstChild.length ) {
            $firstChild.append( $resetButton );
        } else {
            table.$tableControls.filter( '.posts-table-above' ).prepend( $resetButton );
        }

        return table;
    };

    PostsTable.prototype.initSearchOnClick = function() {
        if ( this.config.clickFilter ) {
            this.$table.on( 'click.ptp', 'a', { table: this }, onClickToSearch );
        }

        return this;
    };

    PostsTable.prototype.initSelect2 = function() {
        let table = this;

        if ( !select2Enabled() ) {
            return table;
        }

        let select2Options = {
            dropdownCssClass: 'posts-table-dropdown',
            dropdownParent: table.$tableWrapper
        };

        // Initialize select2 for search filters.
        if ( table.$filters.length ) {
            table.$filters.select2(
                Object.assign( select2Options, { minimumResultsForSearch: 5 } )
                );
        }

        // Initialize select2 for page length - minimumResultsForSearch of -1 disables the search box.
        table.$tableControls.find( '.dataTables_length select' ).select2(
            Object.assign( select2Options, { minimumResultsForSearch: -1 } )
            );

        return table;
    };

    PostsTable.prototype.processAjaxData = function() {
        let table = this;

        if ( !table.config.serverSide || !table.ajaxData.length ) {
            return;
        }

        let $rows = table.$table.find( 'tbody tr' );

        // Add row attributes to each row in table
        for ( let i = 0; i < table.ajaxData.length; i++ ) {
            if ( '__attributes' in table.ajaxData[i] && $rows.eq( i ).length ) {
                $.each( table.ajaxData[i].__attributes, addRowAttributes( $rows.eq( i ) ) );
            }
        }

        return table;
    };

    /*
     * Rebuild the specified search filter - all current options are dropped then filter is rebuilt.
     */
    PostsTable.prototype.refreshFilterItems = function( $select ) {
        let table = this,
            filters = table.$table.data( 'filters' ),
            column = $select.data( 'column' ),
            val = $select.val(); // Store value so we can reset later.

        if ( !filters || !( column in filters ) ) {
            return table;
        }

        // Drop all filter items.
        $select.empty();

        // Add the default filter option.
        $( '<option value="" />' ).text( filters[column].heading ).prependTo( $select );

        // Fetch the current list of items, then attach them to the filter.
        appendFilterItems( $select, table.getCurrentFilterItems( $select ) );

        // Restore previous selected value.
        $select.val( val );

        return table;
    };

    PostsTable.prototype.scrollToTop = function() {
        let table = this,
            scroll = table.config.scrollOffset;

        if ( scroll !== false && !isNaN( scroll ) ) {
            let tableOffset = table.$tableWrapper.offset().top - scroll;

            if ( table.hasAdminBar ) { // Adjust offset for WP admin bar
                tableOffset -= 32;
            }

            $( 'html,body' ).animate( { scrollTop: tableOffset }, 300 );
        }

        return table;
    };

    PostsTable.prototype.showHidePagination = function() {
        let table = this;

        // Hide pagination if we only have 1 page
        if ( table.$pagination.length ) {
            let pageInfo = table.getDataTable().page.info();

            if ( pageInfo && pageInfo.pages <= 1 ) {
                table.$pagination.hide( 0 );
            } else {
                table.$pagination.show();
            }
        }

        return table;
    };

    /******************************************
     * EVENTS
     ******************************************/

    function onAjaxLoad( event, settings, json, xhr ) {
        let table = event.data.table;

        if ( null !== json && 'data' in json && $.isArray( json.data ) ) {
            table.ajaxData = json.data;
        }

        table.$table.trigger( 'lazyload.ptp', [table] );
    }

    function onClickToSearch( event ) {
        let $link = $( this ),
            table = event.data.table,
            dataTable = table.getDataTable(),
            $td = $link.closest( 'td' );

        // Get nearest <td> cell for clicked link. If it's child row, we need to use the <li data-dt-column> to find the column.
        if ( $td.hasClass( 'child' ) ) {
            $td = $link.closest( '[data-dt-column]', $td );
        }

        // Find the DataTables column.
        let column = dataTable.column( $td.get( 0 ) );

        if ( !column.length ) {
            return true;
        }

        // Is the column click filterable?
        let $columnHeader = $( column.header() );

        if ( !$columnHeader.data( 'clickFilter' ) ) {
            return true;
        }

        // Column found and is filterable.
        event.preventDefault();

        let searchVal = $link.text(),
            columnName = $columnHeader.data( 'name' ),
            $filter = ( table.$filters && table.$filters.length ) ? table.$filters.filter( '[data-column="' + columnName + '"]' ) : [];

        // If we have filters, update selection to match the value being searched for
        if ( $filter.length ) {
            // Clear any hidden filter column search
            dataTable.column( $filter.first().data( 'searchColumn' ) + ':name' ).search( '' );

            $( 'option', $filter ).filter( function() {
                return $.trim( $( this ).text().replace( '\u2013', '' ) ) === searchVal;
            } )
                .prop( 'selected', true )
                .trigger( 'change', [true] );
        }

        // Perform the search.
        if ( table.config.serverSide ) {
            column.search( searchVal ).draw();
        } else {
            searchVal = '(^|, )' + $.fn.dataTable.util.escapeRegex( searchVal ) + '(, |$)';
            column.search( searchVal, true, false ).draw();
        }

        // Finally, scroll to top of table.
        table.scrollToTop();

        return false;
    }

    function onDraw( event ) {
        let table = event.data.table;

        // Add row attributes to each <tr> if using lazy load
        if ( table.config.serverSide ) {
            table.processAjaxData();
        }

        // If using server side processing, or not on first draw event, initialise content
        if ( table.config.serverSide || !table.$table.hasClass( 'loading' ) ) {
            initMedia( table.$table );
        }

        initPhotoswipeForContent( table.$table );

        table
            .showHidePagination()
            .$table.trigger( 'draw.ptp', [table] );
    }

    function onFilterChange( event, setValueOnly ) {
        let $select = $( this ),
            table = event.data.table;

        if ( setValueOnly ) {
            return true;
        }

        let value = $select.val(),
            column = $select.data( 'column' ),
            dataTable = table.getDataTable(),
            searchColumn = dataTable.column( $select.data( 'searchColumn' ) + ':name' );

        if ( table.config.serverSide ) {
            searchColumn.search( value ).draw();
        } else {
            let searchVal = value ? '(^| )' + $.fn.dataTable.util.escapeRegex( value ) + '( |$)' : '';
            searchColumn.search( searchVal, true, false ).draw();
        }

        let $thisFilterGroup = table.$filters.filter( '[data-column="' + column + '"]' ),
            $otherFilters = table.$filters.not( $thisFilterGroup );

        // If we have filters above and below table, update corresponding filter to match.
        $thisFilterGroup
            .not( $select[0] )
            .val( value )
            .trigger( 'change', [true] );

        if ( !table.config.serverSide ) {
            // Update other filters to show only relevant items.
            $otherFilters.each( function() {
                table.refreshFilterItems( $( this ) );
            } );
        }
    }

    function onInit( event ) {
        let table = event.data.table;

        table.$tableWrapper = table.$table.parent();
        table.$pagination = table.$tableWrapper.find( '.dataTables_paginate' );
        table.$tableControls = table.$tableWrapper.find( '.posts-table-controls' );

        table
            .initFilters()
            .initSelect2()
            .initResetButton()
            .initSearchOnClick()
            .initPhotoswipe()
            .showHidePagination();

        // fitVids will run on every draw event for lazy load, but for standard loading
        // we need to run here as well because initMedia only runs on subsequent draw events.
        if ( !table.config.serverSide && $.fn.fitVids ) {
            table.$table.fitVids();
        }

        table.$table
            .removeClass( 'loading' )
            .trigger( 'init.ptp', [table] );
    }

    function onOpenPhotoswipe( event ) {
        event.stopPropagation();

        // Only open for click events.
        if ( 'click' !== event.type ) {
            return false;
        }

        let pswpElement = $( '.pswp' )[0],
            $target = $( event.target ),
            $galleryImage = $target.closest( '.posts-table-gallery__image' ),
            items = [];

        if ( $galleryImage.length > 0 ) {
            $galleryImage.each( function( i, el ) {
                let img = $( el ).find( 'img' ),
                    large_image_src = img.attr( 'data-large_image' ),
                    large_image_w = img.attr( 'data-large_image_width' ),
                    large_image_h = img.attr( 'data-large_image_height' ),
                    item = {
                        src: large_image_src,
                        w: large_image_w,
                        h: large_image_h,
                        title: ( img.attr( 'data-caption' ) && img.attr( 'data-caption' ).length ) ? img.attr( 'data-caption' ) : img.attr( 'title' )
                    };
                items.push( item );
            } );
        }

        const options = {
            index: 0,
            shareEl: false,
            closeOnScroll: false,
            history: false,
            hideAnimationDuration: 0,
            showAnimationDuration: 0
        };

        // Initializes and opens PhotoSwipe
        let photoswipe = new PhotoSwipe( pswpElement, PhotoSwipeUI_Default, items, options );
        photoswipe.init();

        return false;
    }

    function onPage( event ) {
        // Animate back to top of table on next/previous page event
        event.data.table.scrollToTop();
    }

    function onProcessing( event, settings, processing ) {
        if ( processing ) {
            event.data.table.$table.block( blockConfig );
        } else {
            event.data.table.$table.unblock();
        }
    }

    function onReset( event ) {
        event.preventDefault();

        let table = event.data.table,
            dataTable = table.getDataTable();

        // Reset responsive child rows
        table.$table.find( 'tr.child' ).remove();
        table.$table.find( 'tr.parent' ).removeClass( 'parent' );

        // Clear search for all filtered columns
        dataTable.columns( 'th[data-searchable="true"]' ).search( '' );

        // Reset ordering
        let initialOrder = table.$table.attr( 'data-order' );

        if ( initialOrder.length ) {
            let orderArray = initialOrder.replace( /[\[\]\" ]+/g, '' ).split( ',' );

            if ( 2 === orderArray.length ) {
                dataTable.order( orderArray );
            }
        }

        // Reset filters
        if ( table.$filters.length ) {
            table.$filters.val( '' ).trigger( 'change', [true] );

            if ( !table.config.serverSide ) {
                table.$filters.each( function() {
                    table.refreshFilterItems( $( this ) );
                } );
            }
        }

        // Find initial search term
        let searchTerm = ( 'search' in table.config && 'search' in table.config.search ) ? table.config.search.search : '';

        // Reset global search and page length
        dataTable
            .search( searchTerm )
            .page.len( table.config.pageLength )
            .draw( true );

        if ( select2Enabled() ) {
            // If using select2, we need to trigger change to update the value.
            table.$tableControls.find( '.dataTables_length select' ).trigger( 'change' );
        }
    }

    function onResponsiveDisplay( event, datatable, row, showHide, update ) {
        if ( showHide && ( typeof row.child() !== 'undefined' ) ) {
            // Initialise elements in child row
            initMedia( row.child() );

            let table = event.data.table;

            //@deprecated
            table.$table.trigger( 'responsive-display.ptp', [table, row.child()] );

            table.$table.trigger( 'responsiveDisplay.ptp', [table, datatable, row, showHide] );
        }
    }

    function onWindowLoad( event ) {
        let table = event.data.table;

        // Recalc column sizes on window load (e.g. to correctly contain media playlists)
        table.getDataTable()
            .columns.adjust()
            .responsive.recalc();

        table.$table.trigger( 'load.ptp', [table] );
    }

    /******************************************
     * JQUERY PLUGIN
     ******************************************/

    /**
     * jQuery plugin to create a post table for the current set of matched elements.
     *
     * @returns jQuery object - the set of matched elements the function was called with (for chaining)
     */
    $.fn.postsTable = function() {
        return this.each( function() {
            let table = new PostsTable( $( this ) );
            table.init();
        } );
    };

    $( function() {
        if ( 'DataTable' in $.fn && $.fn.DataTable.ext ) {
            // Change DataTables error reporting to throw rather than alert
            $.fn.DataTable.ext.errMode = 'throw';
        }

        // Initialise all post tables
        $( '.' + posts_table_params.table_class ).postsTable();
    } );

} )( jQuery, window, document );