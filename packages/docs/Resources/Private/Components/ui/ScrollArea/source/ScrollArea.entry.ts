import { initAllComponentInstances } from 'fluid-ui/client';
import { ScrollArea } from 'fluid-ui/primitives/scroll-area';

(() => {
	initAllComponentInstances('scroll-area', ({ props }) => {
		const scrollArea = new ScrollArea(props);
		scrollArea.init();
		return scrollArea;
	});
})();
