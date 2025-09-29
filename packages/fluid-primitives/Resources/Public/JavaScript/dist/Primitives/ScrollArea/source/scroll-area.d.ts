import { Component$1 as Component, Machine$1 as Machine } from "../../../index-C5b-t0zz.js";
import { PropTypes } from "@zag-js/types";
import * as scrollArea from "@zag-js/scroll-area";

//#region Resources/Private/Primitives/ScrollArea/source/scroll-area.d.ts
declare class ScrollArea extends Component<scrollArea.Props, scrollArea.Api<PropTypes>> {
  name: string;
  initMachine(props: scrollArea.Props): Machine<any>;
  initApi(): scrollArea.Api<PropTypes<{
    [x: string]: any;
  }>>;
  render(): void;
}
//#endregion
export { ScrollArea };