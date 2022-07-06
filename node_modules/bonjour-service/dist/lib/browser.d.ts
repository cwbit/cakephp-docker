/// <reference types="node" />
import { EventEmitter } from 'events';
export interface BrowserConfig {
    type: string;
    protocol?: 'tcp' | 'udp';
    subtypes?: Array<string>;
    txt?: any;
}
export declare class Browser extends EventEmitter {
    private mdns;
    private onresponse;
    private serviceMap;
    private txt;
    private name?;
    private wildcard;
    private _services;
    constructor(mdns: any, opts: any, onup?: (...args: any[]) => void);
    start(): void;
    stop(): void;
    update(): void;
    get services(): any[];
    private addService;
    private removeService;
    private goodbyes;
    private buildServicesFor;
}
export default Browser;
