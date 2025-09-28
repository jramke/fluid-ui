import { Component$1 as Component, Machine$1 as Machine } from "../../../index-_-33c6AW.js";
import * as _zag_js_types0 from "@zag-js/types";
import * as tooltip from "@zag-js/tooltip";

//#region Resources/Private/Primitives/Tooltip/source/tooltip.d.ts
declare class Tooltip extends Component<tooltip.Props, tooltip.Api> {
  name: string;
  initMachine(props: tooltip.Props): Machine<any>;
  initApi(): tooltip.Api<_zag_js_types0.PropTypes<{
    [x: string]: any;
  }>>;
  render(): void;
}
//#endregion
export { Tooltip };