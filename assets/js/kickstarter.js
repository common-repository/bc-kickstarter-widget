jQuery(document).ready(function($){
    var url = $('.wp_widget_kickstarter').attr('data-url');

    var backers = $('.js-track-project-card').attr('data-project_backers_count');
    $('ul.project-stats').prepend('<li>'+backers+'<span> backers</span></li>');
    
    $('.wp_widget_kickstarter a').each(function(){ 
        $(this).attr("href", url); // Set herf value
        $(this).attr("target", "_blanc");
    });
}); 
