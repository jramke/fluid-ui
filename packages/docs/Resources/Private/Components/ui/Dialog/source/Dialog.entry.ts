import { initAllComponentInstances } from 'fluid-ui/client';
import { Dialog } from 'fluid-ui/primitives/dialog';

(() => {
	initAllComponentInstances('dialog', ({ props }) => {
		const dialog = new Dialog(props);
		dialog.init();
		return dialog;
	});
})();
