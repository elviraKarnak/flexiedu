// cspell:ignore globalfilters .

/**
 * Recursively checks if a block or any of its innerBlocks matches the report-filters block name.
 *
 * @since 3.0.5
 *
 * @param {Object} block The block to check.
 *
 * @return {boolean} True if the block or any nested block matches.
 */
function hasReportFiltersBlockRecursive( block ) {
	if ( ! block ) {
		return false;
	}

	// Check if this block is the report-filters block
	if ( block.name === 'wisdm-learndash-reports/report-filters' ) {
		return true;
	}

	// Recursively check innerBlocks if they exist
	if ( block.innerBlocks && Array.isArray( block.innerBlocks ) ) {
		return block.innerBlocks.some( ( innerBlock ) =>
			hasReportFiltersBlockRecursive( innerBlock )
		);
	}

	return false;
}

/**
 * Check if Report Tools Block exists on the page.
 *
 * This function works in both frontend and editor contexts:
 * - Frontend: Checks for DOM elements using jQuery
 * - Editor: Checks WordPress block editor for the report-filters block
 *
 * @since 3.0.5
 *
 * @return {boolean} True if Report Tools Block exists on the page, false otherwise.
 */
export function hasReportToolsBlock() {
	// Check if we're in the WordPress block editor context
	if (
		typeof wp !== 'undefined' &&
		typeof wp.data !== 'undefined' &&
		typeof wp.data.select === 'function'
	) {
		try {
			const blockEditor = wp.data.select( 'core/block-editor' );
			if ( blockEditor && typeof blockEditor.getBlocks === 'function' ) {
				const blocks = blockEditor.getBlocks();
				return blocks.some( ( block ) =>
					hasReportFiltersBlockRecursive( block )
				);
			}
		} catch ( e ) {
			// If block editor store is not available, fall through to DOM check
		}
	}

	// Frontend context: Check for DOM elements
	return (
		jQuery( '.wisdm-learndash-reports-report-filters' ).length > 0 ||
		jQuery( '#wisdm-learndash-report-filters-container' ).length > 0 ||
		jQuery( '.wp-block-wisdm-learndash-reports-report-filters' ).length > 0
	);
}

/**
 * Get all filter settings from the Report Tools Block.
 *
 * This function retrieves the current filter selections (Course, Lesson, Topic, Category, Group, Learner)
 * from the Report Tools Block component instance.
 *
 * @since 3.0.5
 *
 * @return {Object} An object containing all filter settings:
 *                  {
 *                    selected_courses: { value: number|null, label: string },
 *                    selected_lessons: { value: number|null, label: string },
 *                    selected_topics: { value: number|null, label: string },
 *                    selected_categories: { value: number|null, label: string },
 *                    selected_groups: { value: number|null, label: string },
 *                    selected_learners: { value: number|null, label: string } | null,
 *                    report_type_selected: string,
 *                    active_tab: string ('course' = Course Reports, 'quiz' = Quiz Reports)
 *                  }
 */
export function getReportToolsBlockSettings() {
	const defaultSettings = {
		selected_courses: { value: null, label: '' },
		selected_lessons: { value: null, label: '' },
		selected_topics: { value: null, label: '' },
		selected_categories: { value: null, label: '' },
		selected_groups: { value: null, label: '' },
		selected_learners: null,
		report_type_selected: 'default-course-reports',
		active_tab: 'course', // 'course' = Course Reports, 'quiz' = Quiz Reports
	};

	// Method 1: Check if Report Tools Block exposes its instance globally
	if (
		typeof window !== 'undefined' &&
		window.wisdmReportToolsBlockInstance &&
		window.wisdmReportToolsBlockInstance.state
	) {
		const state = window.wisdmReportToolsBlockInstance.state;
		return {
			selected_courses:
				state.selected_courses || defaultSettings.selected_courses,
			selected_lessons:
				state.selected_lessons || defaultSettings.selected_lessons,
			selected_topics:
				state.selected_topics || defaultSettings.selected_topics,
			selected_categories:
				state.selected_categories ||
				defaultSettings.selected_categories,
			selected_groups:
				state.selected_groups || defaultSettings.selected_groups,
			selected_learners:
				state.selected_learners || defaultSettings.selected_learners,
			report_type_selected:
				state.report_type_selected ||
				defaultSettings.report_type_selected,
			active_tab: ( () => {
				if ( state.active_tab === undefined ) {
					return defaultSettings.active_tab;
				}
				return state.active_tab === 1 ? 'quiz' : 'course';
			} )(),
		};
	}

	// Method 2: Check window.globalfilters (updated when filters are applied)
	if (
		typeof window !== 'undefined' &&
		window.globalfilters &&
		window.globalfilters.detail
	) {
		const detail = window.globalfilters.detail;
		return {
			selected_courses: {
				value: detail.selected_courses || null,
				label: '',
			},
			selected_lessons: {
				value: detail.selected_lessons || null,
				label: '',
			},
			selected_topics: {
				value: detail.selected_topics || null,
				label: '',
			},
			selected_categories: {
				value: detail.selected_categories || null,
				label: '',
			},
			selected_groups: {
				value: detail.selected_groups || null,
				label: '',
			},
			selected_learners: detail.selected_learners || null,
			report_type_selected: defaultSettings.report_type_selected, // Not available in globalfilters
			active_tab: defaultSettings.active_tab, // Not available in globalfilters
		};
	}

	// Return default settings if no Report Tools Block found
	return defaultSettings;
}
