/**
 * Wrapped Select components with EmotionStylesProvider
 *
 * These components wrap react-select's Select and AsyncSelect with
 * EmotionStylesProvider to ensure proper styling in iFrame contexts.
 *
 * Usage:
 *   import Select from '../commons/emotion-styles-provider/select';
 *   import { AsyncSelect } from '../commons/emotion-styles-provider/select';
 */
import ReactSelect from 'react-select';
import ReactAsyncSelect from 'react-select/async';
import EmotionStylesProvider from './index.js';

const Select = ( props ) => {
	return (
		<EmotionStylesProvider cacheKey="ld-reports-select">
			<ReactSelect { ...props } />
		</EmotionStylesProvider>
	);
};

export const AsyncSelect = ( props ) => {
	return (
		<EmotionStylesProvider cacheKey="ld-reports-async-select">
			<ReactAsyncSelect { ...props } />
		</EmotionStylesProvider>
	);
};

export default Select;
