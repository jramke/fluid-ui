import { initAllComponentInstances } from 'fluid-primitives/client';
import { ScrollArea } from 'fluid-primitives/primitives/scroll-area';

(() => {
	initAllComponentInstances('scroll-area', ({ props }) => {
		const scrollArea = new ScrollArea(props);
		scrollArea.init();
		return scrollArea;
	});
})();
