<?php 

/**
 * Generate HTML pagination links with custom icons.
 *
 * @param int $pageNumber   Current page number.
 * @param int $perPage      Number of items per page.
 * @param int $totalItems   Total number of items to paginate.
 *
 * @return string           HTML string of pagination links with icon replacements.
 */
function paginationLinks(int $pageNumber, int $perPage = 10, int $totalItems): string
{
    $pager = \Config\Services::pager();

    // Define text-to-icon replacements for pagination labels
    $replacements = [
        "First"    => "<i class='far fa-chevron-double-left'></i>",
        "Previous" => "<i class='far fa-chevron-left'></i>",
        "Next"     => "<i class='far fa-chevron-right'></i>",
        "Last"     => "<i class='far fa-chevron-double-right'></i>"
    ];

    // Generate default pagination HTML
    $pagination = $pager->makeLinks($pageNumber, $perPage, $totalItems);

    // Replace label text with icons
    return str_replace(array_keys($replacements), array_values($replacements), $pagination);
}