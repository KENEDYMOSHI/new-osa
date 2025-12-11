<?php

use CodeIgniter\Pager\PagerRenderer;

/**
 * @var PagerRenderer $pager
 */

$pager->setSurroundCount(4);

function getPageNumber(string $url): ?int
{
	// Parse the URL and get the query part
	$query = parse_url($url, PHP_URL_QUERY);

	// Parse the query string into an associative array
	parse_str($query, $params);

	// Return the page number if it exists, otherwise null
	return isset($params['page']) ? (int) $params['page'] : null;
}
?>

<style>
	.pagination {
		display: flex !important;
		padding-left: 0;
		list-style: none;
		/* border-radius: 0.25rem; */
	}



	.pagination li a {
		color: #C9571A;
		text-decoration: none;
		background-color: #fff;
		border: 1px solid #dee2e6;
		padding: 0.5rem 0.79rem;
		/* border-radius: 0.25rem; */
		transition: background-color 0.3s ease;
		font-size: small;
	}

	.pagination li a:hover {
		background-color: #e9ecef;

	}

	.pagination li.active a {
		z-index: 1;
		color: #fff;
		background-color: #C9571A;
		border-color: #C9571A;
	}

	.pagination li a:hover {
		cursor: pointer;
	}

	.pagination li.disabled a {
		color: #6c757d;
		pointer-events: none;
		background-color: #fff;
		border-color: #dee2e6;
	}

	.pagination li:first-child a {
		border-top-left-radius: 0.25rem;
		border-bottom-left-radius: 0.25rem;
	}

	.pagination li:last-child a {
		border-top-right-radius: 0.25rem;
		border-bottom-right-radius: 0.25rem;
	}

	.pager{
		margin-top: -6px;
		float: right;
	}
</style>

<nav class="pager" aria-label="<?= lang('Pager.pageNavigation') ?>">
	<ul class="pagination">
		<?php if ($pager->hasPrevious()) : ?>
			<li>
				<a aria-label="<?= lang('Pager.first') ?>" onclick="getCertificatesData('1')">
					<span aria-hidden="true"><?= lang('Pager.first') ?></span>
				</a>
			</li>
			<li>
				<a aria-label="<?= lang('Pager.previous') ?>" onclick="getCertificatesData('<?= getPageNumber($pager->getPrevious()) ?>')">
					<span aria-hidden="true"><?= lang('Pager.previous') ?></span>
				</a>
			</li>
		<?php endif ?>

		<?php foreach ($pager->links() as $link) : ?>
			<li <?= $link['active'] ? 'class="active"' : '' ?>>
				<a <?= $link['active'] ? 'class="currentPage"' : '' ?> onclick="getCertificatesData('<?= $link['title'] ?>')">
					<?= $link['title'] ?>
				</a>
			</li>
		<?php endforeach ?>

		<?php if ($pager->hasNext()) : ?>
			<li>
				<a aria-label="<?= lang('Pager.next') ?>" onclick="getCertificatesData('<?= getPageNumber($pager->getNext()) ?>')">
					<span aria-hidden="true"><?= lang('Pager.next') ?> </span>

				</a>
			</li>
			<li>
				<a aria-label="<?= lang('Pager.last') ?>" onclick="getCertificatesData('<?= getPageNumber($pager->getLast()) ?>')">
					<span aria-hidden="true"><?= lang('Pager.last') ?></span>
				</a>
			</li>
		<?php endif ?>
	</ul>
</nav>