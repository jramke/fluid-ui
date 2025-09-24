import { Component$1 as Component, Machine$1 as Machine } from "../../../index-vp8KAZU4.js";
import * as _zag_js_types1 from "@zag-js/types";
import * as tooltip from "@zag-js/tooltip";

//#region Resources/Private/Primitives/Tooltip/source/tooltip.d.ts
declare class Tooltip extends Component<tooltip.Props, tooltip.Api> {
  name: string;
  initMachine(props: tooltip.Props): Machine<any>;
  initApi(): tooltip.Api<_zag_js_types1.PropTypes<{
    [x: string]: any;
  }>>;
  render(): void;
}
//#endregion
export { Tooltip };