initUrduEditor('http://www.radlabs.biz/');

makeUrduEditor('post_title',12);
makeUrduEditor('content',18);
makeUrduEditor('excerpt',18);
makeUrduEditor('tw',18);
makeUrduEditor('newtag[post_tag]',18);
makeUrduEditor('s',18);

jQuery('document').ready(function(){
    jQuery('#content').focus();
});