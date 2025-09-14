import { Component, Machine, normalizeProps } from "../../../Client-C3ni2oH9.js";
import * as tooltip from "@zag-js/tooltip";

//#region Resources/Private/Primitives/Tooltip/source/tooltip.ts
var Tooltip = class extends Component {
	name = "tooltip";
	initMachine(props) {
		return new Machine(tooltip.machine, props);
	}
	initApi() {
		return tooltip.connect(this.machine.service, normalizeProps);
	}
	render() {
		const triggerEl = this.getElement("trigger");
		if (triggerEl) this.spreadProps(triggerEl, this.api.getTriggerProps());
		const positionerEl = this.getElement("positioner");
		if (positionerEl) this.spreadProps(positionerEl, this.api.getPositionerProps());
		const arrowEl = this.getElement("arrow");
		if (arrowEl) this.spreadProps(arrowEl, this.api.getArrowProps());
		const arrowTipEl = this.getElement("arrow-tip");
		if (arrowTipEl) this.spreadProps(arrowTipEl, this.api.getArrowTipProps());
		const contentEl = this.getElement("content");
		if (contentEl) this.spreadProps(contentEl, this.api.getContentProps());
	}
};

//#endregion
export { Tooltip };