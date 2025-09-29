import { Component$1 as Component, Machine$1 as Machine } from "../../../index-C5b-t0zz.js";
import * as _zag_js_types1 from "@zag-js/types";
import * as collapsible from "@zag-js/collapsible";

//#region Resources/Private/Primitives/Collapsible/source/collapsible.d.ts
declare class Collapsible extends Component<collapsible.Props, collapsible.Api> {
  name: string;
  initMachine(props: collapsible.Props): Machine<any>;
  initApi(): collapsible.Api<_zag_js_types1.PropTypes<{
    [x: string]: any;
  }>>;
  render(): void;
}
//#endregion
export { Collapsible };