import { initAllComponentInstances } from 'fluid-primitives/client';
import { Tooltip } from 'fluid-primitives/primitives/tooltip';

(() => {
	initAllComponentInstances('tooltip', ({ props }) => {
		const tooltip = new Tooltip(props);
		tooltip.init();
		return tooltip;
	});
})();
