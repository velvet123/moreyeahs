jQuery(document).ready(function($) {
    // Function to load projects
    function loadProjects(page = 1) {
        var client = $('#client-filter').val();
        var deadline = $('#deadline-filter').val();
        var cost = $('#cost-filter').val();
        
        $.ajax({
            url: wpProjects.ajaxurl,
            type: 'POST',
            data: {
                action: 'filter_projects',
                client: client,
                deadline: deadline,
                cost: cost,
                page: page
            },
            beforeSend: function() {
                $('#projects-grid').addClass('loading');
            },
            success: function(response) {
                $('#projects-grid').html(response.html);
                $('.wp-projects-pagination').html(response.pagination);
                $('#projects-grid').removeClass('loading');
            },
            error: function(xhr, status, error) {
                console.log('Error:', error);
                $('#projects-grid').removeClass('loading');
            }
        });
    }
    
    // Handle filter button click
    $('#apply-filters').on('click', function(e) {
        e.preventDefault();
        loadProjects(1); // Reset to first page when filtering
    });
    
    // Handle pagination clicks
    $(document).on('click', '.wp-projects-pagination a.page-numbers', function(e) {
        e.preventDefault();
        var page = $(this).text();
        
        // Handle prev/next
        if($(this).hasClass('prev')) {
            page = parseInt($('.wp-projects-pagination .current').text()) - 1;
        } else if($(this).hasClass('next')) {
            page = parseInt($('.wp-projects-pagination .current').text()) + 1;
        }
        
        loadProjects(page);
        
        // Scroll to top of projects
        $('html, body').animate({
            scrollTop: $('.wp-projects-container').offset().top - 50
        }, 500);
    });
}); 