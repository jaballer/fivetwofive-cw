/**
 * "Projects Archive" Query Loop variation: the Projects sibling of the Work plugin's "Work Archive" block.
 * Reuses the Work plugin's "Cards" block style (is-style-ftf-work-archive-cards).
 */
wp.domReady( function () {
	const PROJECT_QUERY_LOOP_BLOCK_NAME = 'ftf-project/project-archive';

	wp.blocks.registerBlockVariation( 'core/query', {
		name: PROJECT_QUERY_LOOP_BLOCK_NAME,
		title: 'Projects Archive',
		description: 'A grid of Projects cards, newest first.',
		icon: 'lightbulb',
		isActive: ( { namespace, query } ) => {
			return namespace === PROJECT_QUERY_LOOP_BLOCK_NAME && query.postType === 'ftf_project';
		},
		attributes: {
			namespace: PROJECT_QUERY_LOOP_BLOCK_NAME,
			className: 'is-style-ftf-work-archive-cards',
			query: {
				perPage: 9,
				pages: 0,
				offset: 0,
				postType: 'ftf_project',
				order: 'desc',
				orderBy: 'date',
				author: '',
				search: '',
				exclude: [],
				sticky: '',
				inherit: false,
			},
			displayLayout: {
				type: 'flex',
				columns: 3,
			},
		},
		innerBlocks: [
			[
				'core/post-template',
				{},
				[
					[ 'core/post-featured-image', { isLink: true, height: '250px', style: { spacing: { margin: { bottom: '0' } } } } ],
					[
						'core/group',
						{
							style: { spacing: { padding: { top: 'var:preset|spacing|x-small', right: 'var:preset|spacing|x-small', bottom: 'var:preset|spacing|x-small', left: 'var:preset|spacing|x-small' } } },
							layout: { type: 'constrained' },
						},
						[
							[ 'core/post-title', { isLink: true, fontSize: 'medium' } ],
							[ 'core/post-excerpt', { fontSize: 'small' } ],
							[ 'core/post-terms', { term: 'topic', fontSize: 'small' } ],
						],
					],
				],
			],
			[ 'core/spacer' ],
			[ 'core/query-pagination', { layout: { type: 'flex', justifyContent: 'center' } } ],
			[ 'core/query-no-results' ],
		],
		scope: [ 'inserter' ],
	} );
} );
