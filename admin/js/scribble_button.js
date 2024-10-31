var scribble = {
    getParameterByName: function( string, name ) {
        string = string.replace(/&amp;/g, '&');
        name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
        var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
            results = regex.exec( string );
        return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
    }
};

(function($) {

	tinymce.create( 'tinymce.plugins.scribblebutton', {
		init : function( ed, url ) {
			//hides the popup window when clicking on greyd out content
			$( "#mce-modal-block" ).live( "click", function(){
				ed.windowManager.close();
			});
			ed.addButton( 'scribblebutton', {
				title : 'ScribbleLive Embed to Shortcode',
				image : url + '/../img/scribble.png',
				onclick : function() {
					ed.windowManager.open({
						title    : "ScribbleLive Embed to Shortcode",
						width    : 600,
						height   : 200,
						autosize : true,
						body     : [
						{ type: 'label', text:"Enter your embed code"},
                     	{ type: 'textbox', name: 'embed_code', multiline: true, rows:4, style : "height:125px" },
                		],
                		onsubmit : function( e ) {

                            var embedCode = e.data.embed_code;
                            if( embedCode.indexOf( "LiveArticleEmbed" ) > -1 ){

                                // if article embed code, parse src and article id
                                var source_pattern = /src\s*=\s*"(.+?)"/;
                                var src_part = source_pattern.exec( embedCode );
                                if( src_part != null &&  src_part[1] != null ){

                                    var src = src_part[1].replace( '"', '' ); // Cleanup and extract url
                                    if ( src != null && src != 'undefined' ){
                                        var article_id = scribble.getParameterByName( src, 'Id' );
                                        if( article_id != null && article_id != '' && article_id != 'undefined' ){
                                            var thread_str = '';
                                            var thread_id = scribble.getParameterByName( src, 'ThreadId' );
                                            if( thread_id != null && thread_id != '' && thread_id != 'undefined' ){
                                                thread_str = 'theme="' + thread_id + '" ';
                                            }
                                            ed.insertContent( '[scribble type="article" id="' + article_id + '" ' + thread_str + '/]' );
                                        }
                                    }

                                }

                            }else{

                                data_source_pattern = /data-src="\/\S+\/\d+\/?\d+"/;
                                data_src = data_source_pattern.exec( e.data.embed_code );
                                //console.log( data_src )
                                if ( data_src != null && data_src != 'undefined' ){
                                    data_src_str = data_src[0].replace( "data-src", "src" );
                                    ed.insertContent( '[scribble ' + data_src_str + ' /]' );
                                }
                                else{
                                    ed.windowManager.alert( "The embed code you entered was not recognised" );
                                }

                            }
                    	}
 					});
				}
			});
		},
		createControl : function( n, cm ) {
			return null;
		},
		getInfo : function() {
			return {
				longname : "Scribble Live Embed to Short code",
				author : 'Reshift Media',
				authorurl : 'http://www.reshiftmedia.com/', 
				infourl : 'http://www.reshiftmedia.com/',
				version : "1.0"
			};
		}
	});
	tinymce.PluginManager.add( 'scribblebutton', tinymce.plugins.scribblebutton );
})( jQuery );