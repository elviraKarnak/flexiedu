/**
 * ChartWrapper
 *
 * Wrapper for react-apexcharts Chart component that patches known bugs.
 *
 * ApexCharts has multiple bugs that occur during React's StrictMode
 * double-mount cycle (or similar remount scenarios in WordPress block editor).
 * These bugs stem from ApexCharts' SVG.js library accessing elements that
 * are detached from the DOM during remounts.
 *
 * cspell:ignore svgjs
 */

import ReactApexChart from 'react-apexcharts';
import { Component, createRef } from '@wordpress/element';

/**
 * Checks if chart options are valid.
 *
 * @since 3.0.4
 *
 * @param {Object|Array} options Chart options object
 *
 * @return {boolean} Whether options are valid
 */
const isValidOptions = ( options ) => {
	if ( ! options || Array.isArray( options ) ) {
		return false;
	}
	if ( typeof options !== 'object' || ! options.chart ) {
		return false;
	}
	return true;
};

/**
 * Checks if chart series data is valid.
 *
 * @since 3.0.4
 *
 * @param {Array} series Chart series data.
 *
 * @return {boolean} Whether series is valid
 */
const isValidSeries = ( series ) => {
	if ( ! series || ! Array.isArray( series ) || series.length === 0 ) {
		return false;
	}

	// Check if the first series element has valid data structure.
	const firstSeries = series[ 0 ];
	if ( ! firstSeries || typeof firstSeries !== 'object' ) {
		return false;
	}

	// For bar/line charts, check if data exists and is an array.
	if (
		firstSeries.data &&
		( ! Array.isArray( firstSeries.data ) || firstSeries.data.length === 0 )
	) {
		return false;
	}

	return true;
};

/**
 * Fix for ApexCharts grid.position === 'back' bug (src/modules/axes/Axes.js)
 *
 * ApexCharts has a bug where it assumes Paper.children()[1] exists:
 *
 *   if (this.w.config.grid.position === 'back') {
 *     const inner = gl.dom.Paper.children()[1]
 *     inner.remove()  // Fails if children()[1] is undefined
 *     gl.dom.Paper.add(inner)
 *   }
 *
 * This code attempts to reorder SVG elements so the grid appears behind
 * the chart data. During React's StrictMode double-mount cycle (or similar
 * remount scenarios in WordPress block editor), Paper may not have the
 * expected children structure.
 *
 * Error: "can't access property 'remove', i is undefined"
 *
 * Fix: Set grid.position to 'front' to skip this code path.
 * Visual impact: Grid lines appear in front of chart data rather than behind.
 *
 * @since 3.0.4
 *
 * @param {Object} options Chart options.
 *
 * @return {Object} Patched options with grid.position set to 'front'.
 */
const patchGridPosition = ( options ) => {
	return {
		...options,
		grid: {
			...( options.grid || {} ),
			position: 'front',
		},
	};
};

/**
 * Fix for ApexCharts dropShadow/filter bug (src/modules/Filters.js, src/svgjs/svg.js)
 *
 * When applying drop shadows or filters, ApexCharts calls:
 *
 *   this.filterer = block instanceof SVG.Element ?
 *     block : this.doc().filter(block);  // svg.js
 *
 * During React's StrictMode double-mount cycle (or similar remount scenarios
 * in WordPress block editor), the element may be detached from DOM, causing
 * this.doc() to return undefined. Calling .filter() on undefined fails.
 *
 * Error: "can't access property 'filter', this.doc() is undefined"
 *
 * This is triggered by:
 * - chart.dropShadow.enabled (shadows on chart elements)
 * - dataLabels.dropShadow.enabled (shadows on pie/donut labels)
 *
 * Fix: Disable both dropShadow options to avoid triggering filter code paths.
 *
 * Visual impact: Drop shadows are disabled.
 *
 * @since 3.0.4
 *
 * @param {Object} options Chart options.
 *
 * @return {Object} Patched options with dropShadow disabled.
 */
const patchDropShadow = ( options ) => {
	return {
		...options,
		chart: {
			...( options.chart || {} ),
			dropShadow: {
				...( options.chart?.dropShadow || {} ),
				enabled: false,
			},
		},
		dataLabels: {
			...( options.dataLabels || {} ),
			dropShadow: {
				...( options.dataLabels?.dropShadow || {} ),
				enabled: false,
			},
		},
	};
};

/**
 * Apply all patches to chart options to avoid ApexCharts bugs.
 *
 * @since 3.0.4
 *
 * @param {Object} options Original chart options.
 *
 * @return {Object} Patched options that avoid ApexCharts bugs
 */
const patchOptions = ( options ) => {
	if ( ! options ) {
		return options;
	}

	let patched = options;
	patched = patchGridPosition( patched );
	patched = patchDropShadow( patched );
	return patched;
};

/**
 * Chart wrapper component
 *
 * @since 3.0.4
 */
class ChartWrapper extends Component {
	constructor( props ) {
		super( props );
		this.containerRef = createRef();
	}

	render() {
		const { options, series, type, width, height, ...rest } = this.props;

		// Only render chart when data is valid
		if ( ! isValidOptions( options ) || ! isValidSeries( series ) ) {
			return <div ref={ this.containerRef } />;
		}

		// Patch options to avoid ApexCharts bug
		const patchedOptions = patchOptions( options );

		return (
			<div ref={ this.containerRef }>
				<ReactApexChart
					options={ patchedOptions }
					series={ series }
					type={ type }
					width={ width }
					height={ height }
					{ ...rest }
				/>
			</div>
		);
	}
}

export default ChartWrapper;
