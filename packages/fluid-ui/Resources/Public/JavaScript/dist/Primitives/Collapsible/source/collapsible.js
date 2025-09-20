import { Component, Machine, normalizeProps } from "../../../Client-6fj7hedm.js";
import * as collapsible from "@zag-js/collapsible";

//#region Resources/Private/Primitives/Collapsible/source/collapsible.ts
var Collapsible = class extends Component {
	name = "collapsible";
	initMachine(props) {
		return new Machine(collapsible.machine, props);
	}
	initApi() {
		return collapsible.connect(this.machine.service, normalizeProps);
	}
	render() {
		const rootEl = this.getElement("root");
		if (rootEl) this.spreadProps(rootEl, this.api.getRootProps());
		const triggerEl = this.getElement("trigger");
		if (triggerEl) this.spreadProps(triggerEl, this.api.getTriggerProps());
		const triggerTextEl = this.getElement("trigger-text");
		if (triggerTextEl && triggerTextEl.dataset.openText && triggerTextEl.dataset.closeText) triggerTextEl.textContent = this.api.open ? triggerTextEl.dataset.closeText : triggerTextEl.dataset.openText;
		const contentEl = this.getElement("content");
		if (contentEl) this.spreadProps(contentEl, this.api.getContentProps());
	}
};

//#endregion
export { Collapsible };