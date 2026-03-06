/**
 * EmotionStylesProvider
 *
 * Custom Cache Provider for react-select in WordPress 6.9+ Iframe Context
 *
 * In WordPress 6.9+, block editor content renders inside an iframe.
 * This component provides an Emotion cache configured to inject styles
 * into the correct document (the iframe's head instead of the parent's).
 *
 * This is similar to react-select's NonceProvider but adds the crucial
 * `container` option to target the iframe's document.
 *
 * @see https://github.com/impress-org/givewp/pull/8183
 */
import { CacheProvider } from '@emotion/react';
import createCache from '@emotion/cache';
import { useRef, useState, useEffect } from '@wordpress/element';

const EmotionStylesProvider = ( { children, cacheKey = 'ld-reports' } ) => {
	const containerRef = useRef( null );
	const [ cache, setCache ] = useState( null );

	useEffect( () => {
		if ( containerRef.current && ! cache ) {
			setCache(
				createCache( {
					key: cacheKey,
					container: containerRef.current,
				} )
			);
		}
	}, [ cacheKey, cache ] );

	return (
		<div ref={ containerRef }>
			{ cache ? (
				<CacheProvider value={ cache }>{ children }</CacheProvider>
			) : (
				children
			) }
		</div>
	);
};

export default EmotionStylesProvider;
