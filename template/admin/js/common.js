jQuery(document).ready(function($) {

    var TTTqueryposition_sources = $('#TTTqueryposition').find('.source');
    var TTTqp = $('#TTTqueryposition');

    TTTqp.find('input[name=posts_per_page]').on('change',function(event) {
        
        var positions = TTTqp.find('.position').length;
        var val = $(this).val();

        if (positions > val) {
            for(var i=positions; i>=val; i--) {
                $(TTTqp).find('.position').eq(i).remove();
            }
        }
        else {
            for(var i=positions+1; i<=val; i++) {
                var html = '';
                html += '<div class="position new" data-num="0">';
                html += '   <input type="hidden" name="postion'+i+'" value="0">';
                html += '   <em class="num">'+i+'</em>';
                html += '</div>';
                $(TTTqp).find('.positions').append(html);
                var elem = $(TTTqp).find('.position.new');
                TTTqp.trigger('tttquerypositions:add-element',elem);
                elem.removeClass('new');
            }
        }

    });

    TTTqp.find('input[type=submit]').on('click',function(event) {
        event.preventDefault();

        if ($(this).hasClass('save')) {
            $(this).parents('form')
                .attr('target','_top')
                .attr('action','')
                .submit();
        }
        else {

            var url = tttquerypositionConf.preview_url;
            url += '?preview=true';
            url += '&nonce='+tttquerypositionConf.Nonce;

            $(this).parents('form')
                .attr('target','_new')
                .attr('action',url)
                .submit();
        }


    });

    TTTqp.on('tttquerypositions:add-element',function(event,element) {
        $(element).on('click',function(event) {
            event.preventDefault();

            var num = $(this).attr('data-num');
            num++;

            if (num > TTTqueryposition_sources.length-1) {
                num = 0;
            }

            for(var i=1; i<=TTTqueryposition_sources.length; i++) {
                $(this).removeClass('color'+i);
            }

            $(this).attr('data-num',num);
            $(this).find('input[type=hidden]').val(num);

            if (num > 0) $(this).addClass('color'+num);
        });

    });
    $('#TTTqueryposition').find('.position').each(function() {

        TTTqp.trigger('tttquerypositions:add-element',$(this));

    });

});
