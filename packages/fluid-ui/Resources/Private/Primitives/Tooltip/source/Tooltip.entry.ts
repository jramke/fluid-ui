import { Tooltip } from './tooltip';
import { initAllComponentInstances } from '../../../Client';

(() => {
	initAllComponentInstances('tooltip', data => {
		const tooltip = new Tooltip(data);
		tooltip.init();
		return tooltip;
	});
})();
